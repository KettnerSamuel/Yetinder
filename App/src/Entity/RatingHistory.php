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
    private array $Record = [];

    public function getRecord(): array
    {
        return $this->Record;
    }

    public function setRecord(array $Record): static
    {
        $this->Record = $Record;

        return $this;
    }

    public function addRecord(array $Record): static
    {
        $this->Record += $Record;

        return $this;
    }

    public function addRecordDown(array $Record): static
    {
        $this->Record = $Record;

        return $this;
    }


}
