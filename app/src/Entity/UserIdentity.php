<?php

namespace App\Entity;

use App\Repository\UserIdentityRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: UserIdentityRepository::class), Table(name: 'user_identities')]
final class UserIdentity
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'integer', unique: true, nullable: false)]
    private int $userId;

    #[Column(type: 'string', nullable: false)]
    private string $login;

    #[Column(type: 'string', nullable: false)]
    private string $password;

    #[Column(type: 'string', nullable: false)]
    private string $token;

    public function __construct(int $userId, string $login, string $password, string $token)
    {
        $this->userId = $userId;
        $this->login = $login;
        $this->password = $password;
        $this->token = $token;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
