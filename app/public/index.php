<?php

use App\Controller\BookController;
use App\Controller\UserIdentityController;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\UserIdentity;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use App\Repository\UserIdentityRepository;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use UMA\DIC\Container;

require __DIR__ . '/../vendor/autoload.php';

/** @var Container $container */
$container = require '../bootstrap.php';

$container->set(UserIdentityController::class, function (ContainerInterface $container) {
    /** @var EntityManager $entityManager */
    $entityManager = $container->get(EntityManager::class);

    /** @var UserRepository $userRepository */
    $userRepository = $entityManager->getRepository(User::class);

    /** @var UserIdentityRepository $userIdentityRepository */
    $userIdentityRepository = $entityManager->getRepository(UserIdentity::class);

    return new UserIdentityController($userRepository, $userIdentityRepository);
});

$container->set(BookController::class, function (ContainerInterface $container){
    /** @var EntityManager $entityManager */
    $entityManager = $container->get(EntityManager::class);

    /** @var BookRepository $bookRepository */
    $bookRepository = $entityManager->getRepository(Book::class);

    return new BookController($bookRepository);
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->post('/signUp', [UserIdentityController::class, 'signUp']);
$app->post('/signIn', [UserIdentityController::class, 'signIn']);

$app->post('/books', [BookController::class, 'create']);
$app->get('/books/{title}', [BookController::class, 'get']);
$app->get('/books', [BookController::class, 'getAllBooks']);
$app->put('/books/{id}', [BookController::class, 'update']);
$app->delete('/books/{id}', [BookController::class, 'delete']);

$app->run();


