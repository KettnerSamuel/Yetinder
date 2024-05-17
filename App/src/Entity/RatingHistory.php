<?php

namespace App\Entity;

use App\Repository\RatingHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatingHistoryRepository::class)]
class RatingHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(nullable: true)]
    private ?int $up = null;

    #[ORM\Column(nullable: true)]
    private ?int $down = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;

        return $this;
    }

    public function getUp(): ?int
    {
        return $this->up;
    }

    public function setUp(?int $up): static
    {
        $this->up = $up;

        return $this;
    }

    public function getDown(): ?int
    {
        return $this->down;
    }

    public function setDown(?int $down): static
    {
        $this->down = $down;

        return $this;
    }
}
