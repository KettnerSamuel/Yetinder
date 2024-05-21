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

    #[ORM\Column(type: Types::ARRAY)]
    private array $rating = [];

    public function getRating(): array
    {
        return $this->rating;
    }

    public function setRating(array $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

}
