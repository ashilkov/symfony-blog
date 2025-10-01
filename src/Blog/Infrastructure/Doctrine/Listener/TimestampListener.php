<?php

/**
 * @author Andrei Shilkov <aishilkov94@gmail.com>
 * @license MIT
 *
 * @see https://github.com/ashilkov/symfony-blog
 */

namespace App\Blog\Infrastructure\Doctrine\Listener;

use App\Blog\Infrastructure\Doctrine\Entity\TimestampableInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class TimestampListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $now = new \DateTimeImmutable();

        if (null === $entity->getCreatedAt()) {
            $entity->setCreatedAt($now);
        }

        $entity->setUpdatedAt($now);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $entity->setUpdatedAt(new \DateTimeImmutable());

        // Ensure Doctrine sees the change to updated_at
        $em = $args->getObjectManager();
        $meta = $em->getClassMetadata($entity::class);
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $entity);
    }
}
