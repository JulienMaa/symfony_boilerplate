<?php

namespace App\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait TimestampableTrait
{
    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    private ?DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Type(DateTime::class)]
    private ?DateTime $updatedAt = null;

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function updateTimestamps(): void
    {
        $this->updatedAt = new DateTime();

        if ($this->createdAt === null) {
            $this->createdAt = new DateTime();
        }
    }

    public function getFormattedCreatedAt(): string
    {
        return $this->createdAt
            ? $this->createdAt->format('Y-m-d H:i:s')
            : 'N/A';
    }

    public function getFormattedUpdatedAt(): string
    {
        return $this->updatedAt
            ? $this->updatedAt->format('Y-m-d H:i:s')
            : 'N/A';
    }
}
