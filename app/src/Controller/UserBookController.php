<?php

namespace App\Controller;

use App\Entity\WaitingItem;
use App\Repository\UserNativeRepository;
use App\Repository\WaitingItemRepository;
use DateTime;
use App\Entity\Book;
use App\Entity\UserBook;
use App\Entity\UserIdentity;
use App\Repository\BookRepository;
use App\Repository\UserBookRepository;
use App\Repository\UserIdentityRepository;
use Doctrine\DBAL\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserBookController
{
    private BookRepository $bookRepository;
    private UserBookRepository $userBookRepository;
    private UserIdentityRepository $userIdentityRepository;
    private WaitingItemRepository $waitingItemRepository;
    private Connection $connection;

    public function __construct(
        BookRepository $bookRepository,
        UserBookRepository $userBookRepository,
        UserIdentityRepository $userIdentityRepository,
        WaitingItemRepository $waitingItemRepository,
        Connection $connection
    ) {
        $this->bookRepository = $bookRepository;
        $this->userBookRepository = $userBookRepository;
        $this->userIdentityRepository = $userIdentityRepository;
        $this->waitingItemRepository = $waitingItemRepository;
        $this->connection = $connection;
    }

    public function booking(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $token = $request->getHeader( 'Token');
        $params = json_decode($request->getBody()->getContents(), true);

        $errors = $this->getErrors($params);
        if (!empty($errors)) {
            $newStr = json_encode($errors);
            $response->getBody()->write($newStr);
            return $response;
        }

        $bookId  = $params['bookId'];
        $dateFrom = new DateTime('now');
        $dateTo = new DateTime('now +10 day');

        $user = $this->userIdentityRepository->findOneByToken($token);
        if ($user instanceof UserIdentity) {
            $book = $this->bookRepository->findById($bookId);
            if ($book instanceof Book) {
                $userBook = $this->userBookRepository->findByBookId($bookId);
                if ($userBook instanceof UserBook) {
                    $dateCreated = new DateTime('now');
                    $waitingItem = new WaitingItem($user->getUserId(), $book->getId(), $dateCreated);
                    $this->waitingItemRepository->add($waitingItem, true);

                    $error['bookId'] = "Out of stock, added to waiting list";
                    $errorMessage = json_encode($error);
                    $response->getBody()->write($errorMessage);

                    return $response->withStatus(201);

                } else {
                    $userBook2 = new UserBook($user->getUserId(), $book->getId(), $dateFrom, $dateTo);
                    $this->userBookRepository->add($userBook2, true);

                    return $response
                        ->withStatus(201);
                }
            } else {
                $error['id'] = "book is not found";
                $errorMessage = json_encode($error);
                $response->getBody()->write($errorMessage);

                return $response->withStatus(422);
            }
        } else {
            $error['token'] = "user is not found";
            $errorMessage = json_encode($error);
            $response->getBody()->write($errorMessage);

            return $response->withStatus(422);
        }
    }

    private function getErrors(array $params): array
    {
        $messages = [];
        if(empty($params['bookId'])) {
            $messages['bookId'] ='bookId not be empty';
        }
        return $messages;
    }

    public function returnBook(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $token = $request->getHeader('Token');

        $userIdentity = $this->userIdentityRepository->findOneByToken($token);
        if (!$userIdentity instanceof UserIdentity) {
            $userBook = $this->userBookRepository->findOneBy(['bookId' => $args['bookId'], 'userId' => $userIdentity->getUserId()]);
            if ($userBook instanceof UserBook) {
                $this->connection->beginTransaction();
                try {
                    $this->userBookRepository->delete($userBook, true);

                    $waitingItem = $this->waitingItemRepository->getNextByBookId($args['bookId']);
                    $dateFrom = new DateTime('now');
                    $dateTo = new DateTime('now +10 day');
                    $newUserBook = new UserBook($waitingItem->getUserId(), $waitingItem->getBookId(), $dateFrom, $dateTo);
                    $this->userBookRepository->add($newUserBook, true);

                    $this->waitingItemRepository->delete($waitingItem, true);
                } catch (\Exception $exception) {
                    $this->connection->rollBack();

                    $error = "error";
                    $errorMessage = json_encode($error);
                    $response->getBody()->write($errorMessage);
                    return $response->withStatus(422);
                }
                $this->connection->commit();
                return $response
                    ->withStatus(201);

            } else {
                $error['id'] = "user with this book is not found";
                $errorMessage = json_encode($error);
                $response->getBody()->write($errorMessage);
                return $response->withStatus(422);
            }
        }
    }
}

