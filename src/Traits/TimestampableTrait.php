<?php

declare(strict_types=1);

namespace TimestampBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use TimestampBundle\Attribute\Timestampable;

trait TimestampableTrait
{
    /**
     * FIX: Utiliser DATETIME_IMMUTABLE et non DATE_IMMUTABLE
     * 
     * DATE_IMMUTABLE = stocke uniquement la date (ex: 2026-02-07)
     * DATETIME_IMMUTABLE = stocke date + heure (ex: 2026-02-07 15:30:45)
     * 
     * Pour un timestamp de création/modification, on veut l'heure précise.
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * FIX: Même correction - DATETIME_IMMUTABLE au lieu de DATE_IMMUTABLE
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
