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
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation as Extbase;

class Event extends \DERHANSEN\SfEventMgt\Domain\Model\Event
{
    protected ?DateTime $tstamp = null;

    /**
     * @var ObjectStorage<Startdates>
     * @Extbase\ORM\Cascade("remove")
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $startdates;

    protected int $eventduration;

    public function __construct()
    {
        $this->initializeObject();
    }

    /**
     * Initialize all ObjectStorages as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void
    {
        $this->startdates = new ObjectStorage();
    }

    public function getStartdates(): ?ObjectStorage
    {
        return $this->startdates;
    }

    public function setStartdates(?ObjectStorage $startdates): void
    {
        $this->startdates = $startdates;
    }

    public function addStartdates(Startdates $startdates): void
    {
        $this->startdates->attach($startdates);
    }

    public function removeStartdates(Startdates $startdates): void
    {
        $this->startdates->detach($startdates);
    }

    /**
     * Returns all active startdates sorted by date ASC
     *
     * @return array
     */
    public function getActiveStartdates(): array
    {
        $activeStartdates = [];
        if ($this->getStartdates()) {
            $compareDate = new DateTime('tomorrow +1day midnight');
            foreach ($this->getStartdates() as $startdate) {
                if ($startdate->getStartdatetime() >= $compareDate) {
                    $activeStartdates[$startdate->getStartdatetime()->getTimestamp()] = $startdate;
                }
            }
        }
        ksort($activeStartdates);
        return $activeStartdates;
    }
    public function getEventduration(): ?int
    {
        return $this->eventduration;
    }

    public function setEventduration(?int $eventduration): void
    {
        $this->eventduration = $eventduration;
    }
}
