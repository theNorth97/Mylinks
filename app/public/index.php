<?php

use App\Controller\BookController;
use App\Controller\UserBookController;
use App\Controller\UserIdentityController;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\UserBook;
use App\Entity\UserIdentity;
use App\Entity\WaitingItem;
use App\Repository\BookRepository;
use App\Repository\UserBookRepository;
use App\Repository\UserRepository;
use App\Repository\UserIdentityRepository;
use App\Repository\WaitingItemRepository;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
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

    $connection = $entityManager->getConnection();

    return new UserIdentityController($userIdentityRepository, $userRepository, $connection);

});

$container->set(BookController::class, function (ContainerInterface $container){
    /** @var EntityManager $entityManager */
    $entityManager = $container->get(EntityManager::class);

    /** @var BookRepository $bookRepository */
    $bookRepository = $entityManager->getRepository(Book::class);

    return new BookController($bookRepository);
});

$container->set(UserBookController::class, function (ContainerInterface $container){
    /** @var EntityManager $entityManager */
    $entityManager = $container->get(EntityManager::class);

    /** @var UserIdentityRepository $userIdentityRepository */
    $userIdentityRepository = $entityManager->getRepository(UserIdentity::class);

    /** @var BookRepository $bookRepository */
    $bookRepository = $entityManager->getRepository(Book::class);

    /** @var UserBookRepository $userBookRepository */
    $userBookRepository = $entityManager->getRepository(UserBook::class);

    /** @var WaitingItemRepository $waitingItemRepository */
    $waitingItemRepository = $entityManager->getRepository(WaitingItem::class);

    $connection = $entityManager->getConnection();

    return new UserBookController($bookRepository, $userBookRepository, $userIdentityRepository, $waitingItemRepository, $connection);
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

$app->post('/userBooks', [UserBookController::class, 'booking']);
$app->delete('/userBooks/{bookId}', [UserBookController::class, 'returnBook']);

$app->run();


