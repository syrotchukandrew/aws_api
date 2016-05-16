<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Picture;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ImageEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'preRemove'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $picture = $args->getEntity();
        if ($picture instanceof Picture && $picture->getPictureName() !== null &&
            file_exists($picture->getPictureName())) {
            unlink($picture->getPictureName());
        }
    }

}