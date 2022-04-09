<?php
declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Conference;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ConferenceEntityListener
{
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param Conference $conference
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(Conference $conference, LifecycleEventArgs $event): void
    {
        $conference->computeSlug($this->slugger);
    }

    /**
     * @param Conference $conference
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function preUpdate(Conference $conference, LifecycleEventArgs $event): void
    {
        $conference->computeSlug($this->slugger);
    }
}