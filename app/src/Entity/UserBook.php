<?php

namespace App\Entity;

use App\Repository\UserBookRepository;
use Base\Util\DateTime;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: UserBookRepository::class), Table(name: 'user_books')]
final class UserBook
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'integer', unique: true, nullable: false)]
    private int $userId;

    #[Column(type: 'integer', nullable: false)]
    private int $bookId;

    #[Column(type: 'datetime', nullable: false)]
    private DateTime $dateFrom;

    #[Column(type: 'datetime', nullable: false)]
    private DateTime $dateTo;

    public function __construct(int $userId, int $bookId, DateTime $dateFrom, DateTime $dateTo)
    {
        $this->userId = $userId;
        $this->bookId = $bookId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
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

    public function getDateFrom(): DateTime
    {
        return $this->dateFrom;
    }

    public function getDateTo(): DateTime
    {
        return $this->dateTo;
    }
}
