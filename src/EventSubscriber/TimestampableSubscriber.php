<?php

declare(strict_types=1);

namespace TimestampBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use TimestampBundle\Attribute\Timestampable;

#[AsDoctrineListener(event: Events::preUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::prePersist, priority: 500, connection: 'default')]
class TimestampableSubscriber
{
    /**
     * Remplit automatiquement createdAt lors de la création d'une entité.
     */
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->updateTimestamps($entity, isUpdate: false);
    }

    /**
     * Remplit automatiquement updatedAt lors de la modification d'une entité.
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->updateTimestamps($entity, isUpdate: true);
    }

    private function updateTimestamps(object $entity, bool $isUpdate): void
    {
        $reflection = new \ReflectionClass($entity);
        $attributes = $reflection->getAttributes(Timestampable::class);

        if (empty($attributes)) {
            return;
        }

        $now = new \DateTimeImmutable();

        if (!$isUpdate) {
            if (method_exists($entity, 'setCreatedAt')) {
                $entity->setCreatedAt($now);
            }
        }

        if (method_exists($entity, 'setUpdatedAt')) {
            $entity->setUpdatedAt($now);
        }
    }
}
