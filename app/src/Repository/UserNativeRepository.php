<?php

namespace App\Repository;

use App\Entity\User;
use PDO;

class UserNativeRepository
{
    private PDO $connection;

    public function __construct()
    {
        $dsn = 'pgsql:dbname=dbname;host=db';
        $user = 'dbuser';
        $password = 'dbpwd';

        $this->connection = new PDO($dsn, $user, $password);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->connection->query(sprintf(
            'INSERT INTO users("name", "phone") VALUES ( %s, %s)',
            $this->connection->quote($entity->getName()),
            $this->connection->quote($entity->getPhone()),
        ));
    }

    public function findOneByNameField(string $value): ?User
    {
       $user = $this->connection->prepare(sprintf('SELECT * FROM users WHERE value = %s', $value))->fetch(PDO::FETCH_ASSOC);
        if(!empty($user)){
            return new User($user['id'], $user['name'], $user['phone']);
        } else {
            return NULL;
        }
    }

    public function findByName(string $name): ?User
    {
        $user = $this->connection->query("SELECT * FROM users WHERE name = '{$name}'")->fetch(PDO::FETCH_ASSOC);
        if (!empty($user)){
            return new User($user['name'], $user['phone'], $user['id']);
        } else {
            return null;
        }
    }

    public function findByPhone(string $phone): ?User
    {
        $user =   $this->connection->prepare("SELECT * FROM users WHERE phone = '{$phone}'")->fetch(PDO::FETCH_ASSOC);
        if(!empty($user)){
            return new User($user['id'], $user['name'], $user['phone']);
        } else{
            return NULL;
        }
    }
}
