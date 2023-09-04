<?php

declare(strict_types=1);

/*
 * This file is part of the Extension "sf_event_mgt" for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Machwert\SfEventMgtMultidates\Domain\Model;

use DateTime;

class Startdates extends \DERHANSEN\SfEventMgt\Domain\Model\Event
{
    protected ?DateTime $startdatetime = null;
    protected ?Event $event = null;

    public function getStartdatetime(): ?DateTime
    {
        // if (date("c") < $this->startdatetime->format('c')) {
        return $this->startdatetime;
    }

    public function setStartdatetime(?DateTime $startdatetime): void
    {
        $this->startdatetime = $startdatetime;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): void
    {
        $this->event = $event;
    }
}
