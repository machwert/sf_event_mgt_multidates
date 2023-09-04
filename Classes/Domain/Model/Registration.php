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

class Registration extends \DERHANSEN\SfEventMgt\Domain\Model\Registration
{
    public ?DateTime $startdatetime = null;

    public function getStartdatetime(): ?DateTime
    {
        return $this->startdatetime;
    }

    public function setStartdatetime(?DateTime $startdatetime): void
    {
        $this->startdatetime = $startdatetime;
    }
}
