<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "sf_event_mgt" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Machwert\SfEventMgtMultidates\Xclass;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;

class NewEmailService extends \DERHANSEN\SfEventMgt\Service\EmailService
{
    /**
     * Sends an email, if sender and recipient is an valid email address
     *
     * @param string $sender The sender
     * @param string $recipient The recipient
     * @param string $subject The subject
     * @param string $body E-Mail body
     * @param string|null $name Optional sendername
     * @param array $attachments Array of files (e.g. ['/absolute/path/doc.pdf'])
     * @param string|null $replyTo The reply-to mail
     *
     * @return bool true/false if message is sent
     */
    public function sendEmailMessage(
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
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $useFluidEmail = (bool) $typoscript['plugin.']['tx_sfeventmgt_mulitdates.']['settings.']['useFluidEmail'];

        if(!$useFluidEmail) {
            return parent::sendEmailMessage(
                $sender,
                $recipient,
                $subject,
                $body,
                $name,
                $attachments,
                $replyTo
            );
        } else {
            if (!GeneralUtility::validEmail($sender) || !GeneralUtility::validEmail($recipient)) {
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

            if ($replyTo !== null && $replyTo !== '') {
                $email->replyTo($replyTo);
            }
            foreach ($attachments as $attachment) {
                if (file_exists($attachment)) {
                    $email->attachFromPath($attachment);
                }
            }

            if ($GLOBALS['TYPO3_REQUEST'] instanceof ServerRequestInterface) {
                $email->setRequest($GLOBALS['TYPO3_REQUEST']);
            }
            GeneralUtility::makeInstance(Mailer::class)->send($email);
            return true;
        }
    }
}
