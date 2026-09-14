<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "sf_event_mgt" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Machwert\SfEventMgtMultidates\Xclass;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;

class NewEmailService extends \DERHANSEN\SfEventMgt\Service\EmailService
{
    /**
     * Sends an email, if sender and recipient is an valid email address
     *
     * @param ServerRequestInterface $request Seit sf_event_mgt 9.x erster Parameter
     * @param string $sender The sender
     * @param string $recipient The recipient
     * @param string $subject The subject
     * @param string $body E-Mail body
     * @param string|null $name Optional sendername
     * @param array<string> $attachments Array of files (e.g. ['/absolute/path/doc.pdf'])
     * @param string|null $replyTo The reply-to mail
     *
     * @return bool true/false if message is sent
     */
    public function sendEmailMessage(
        ServerRequestInterface $request,
        string $sender,
        string $recipient,
        string $subject,
        string $body,
        ?string $name = null,
        array $attachments = [],
        ?string $replyTo = null
    ): bool {

        $configurationManager = GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);
        $typoscript = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
        );
        $useFluidEmail = (bool) $typoscript['plugin.']['tx_sfeventmgt_mulitdates.']['settings.']['useFluidEmail'];

        if (!$useFluidEmail) {
            return parent::sendEmailMessage(
                $request,
                $sender,
                $recipient,
                $subject,
                $body,
                $name,
                $attachments,
                $replyTo
            );
        } else {
            // Gleiche Eingangspruefung wie im Original: leerer Betreff und
            // ungueltige Adressen fuehren zum Abbruch.
            if ($subject === ''
                || !GeneralUtility::validEmail($sender)
                || !GeneralUtility::validEmail($recipient)
            ) {
                return false;
            }

            // NEW: FLUIDEMAIL
            $email = GeneralUtility::makeInstance(FluidEmail::class)
                ->from($sender)
                ->to($recipient)
                ->subject($subject)
                ->setTemplate('Email')
                ->assign('headline', $subject)
                ->assign('content', $body);

            // replyTo kann Nutzereingabe sein, wenn
            // notification.registrationDataAsSenderForAdminEmails aktiv ist -
            // dann steht dort die im Anmeldeformular eingegebene Adresse.
            // Das Original prueft sie, diese Fassung tat es nicht.
            if ($replyTo !== null && $replyTo !== '' && GeneralUtility::validEmail($replyTo)) {
                $email->replyTo($replyTo);
            }
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $email->attachFromPath($attachment);
                }
            }

            $email->setRequest($request);
            GeneralUtility::makeInstance(Mailer::class)->send($email);
            return true;
        }
    }
}
