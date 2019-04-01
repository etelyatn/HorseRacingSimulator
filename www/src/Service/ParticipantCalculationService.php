<?php

namespace App\Service;


use App\Entity\Participant;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ParticipantCalculationService
 * @package App\Service
 */
class ParticipantCalculationService
{
    /** @var int $distance */
    private $distance;

    /**
     * ParticipantCalculationService constructor.
     *
     * @param int $distance
     */
    public function __construct(int $distance)
    {
        $this->distance = $distance;
    }

    /**
     * Calculate Participant
     *
     * @param Participant $participant
     * @return Participant
     */
    public function proceed(Participant $participant): Participant
    {
        $horseCalculator = new HorseCalculationService();

        $newParticipantDistance = $horseCalculator->calculateDistance(
            $participant->getHorse(),
            $participant->getDistance()
        );
        $participant->setDistance($newParticipantDistance);
        $participant->setTime($participant->getTime() + 1);
        $participant->setActive($this->isFinish($participant));

        return $participant;
    }

    /**
     * Check participant for distance finish
     *
     * @param Participant $participant
     * @return bool
     */
    public function isFinish(Participant $participant): bool
    {

        return $participant->getDistance() < $this->distance;
    }

    /**
     * Calculate Participant Positions
     *
     * @param ArrayCollection $participants
     *
     * @return ArrayCollection
     */
    public function calculatePositions(ArrayCollection $participants): ArrayCollection
    {
        $iterator = $participants->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getTime() < $b->getTime()) ? -1 : 1;
        });

        $sortedParticipants = new ArrayCollection(iterator_to_array($iterator));

        for ($i = 1; $i < $sortedParticipants->count(); $i++) {
            $sortedParticipants[$i]->setPosition($i);
        }

        return $sortedParticipants;
    }

    /**
     * Get leader of participants
     *
     * @param ArrayCollection $participants
     * @return Participant|null
     */
    public function getLeader(ArrayCollection $participants): ?Participant
    {
        $participants = $participants->filter(function (Participant $participant) {
            return $participant->getPosition() == 1;
        });

        return $participants[0] ?? null;
    }
}