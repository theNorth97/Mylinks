<?php

namespace App\Entity;

use App\Repository\WaitingItemRepository;
use Base\Util\DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: WaitingItemRepository::class), Table(name: 'waiting_items')]
final class WaitingItem
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'integer', unique: true, nullable: false)]
    private int $userId;

    #[Column(type: 'integer', nullable: false)]
    private int $bookId;

    #[Column(type: 'datetime', nullable: false)]
    private DateTime $dateCreated;

    public function __construct(int $userId, int $bookId, DateTime $dateCreated)
    {
        $this->userId = $userId;
        $this->bookId = $bookId;
        $this->dateCreated = $dateCreated;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getDateCreated(): DateTime
    {
        return $this->dateCreated;
    }
}
