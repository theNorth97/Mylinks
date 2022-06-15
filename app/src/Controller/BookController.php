<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use DateTime;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


class BookController
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $params = json_decode($request->getBody()->getContents(), true);

        $errors = $this->getErrors($params);

        if (!empty($errors)) {
            $newStr = json_encode($errors);
            $response->getBody()->write($newStr);
            return $response;
        }

        $title = $params['title'];
        $author = $params['author'];
        $date = new DateTime($params['date']);

        $book = new Book($title, $author, $date);
        $this->bookRepository->add($book, true);

        return $response
            ->withStatus(201);
    }

    private function getErrors(array $params): array
    {
        $messages = [];
        if(empty($params['title'])) {
            $messages['title'] ='Title not be empty';
        } else {
            $findTitle = $this->bookRepository->findByTitle($params['title']);
            if($findTitle instanceof Book) {
                $messages['title'] = 'Book with the same title already exists';
            }
        }

        if(empty($params['author'])) {
            $messages['author'] = 'Author not be empty';
        }

        if(empty($params['date'])) {
            $messages['date'] = 'Date not be empty';
        } else {
            try {
                new DateTime($params['date']);
            } catch (\Exception $exception) {
                $messages['date'] = 'Invalid date';
            }
        }
        return $messages;
    }

    public function get(ResponseInterface $response, array $args)
    {
        $book = $this->bookRepository->findByTitle($args['title']);
        if ($book instanceof Book) {
            $bookStr = json_encode($book->toArray());
            $response->getBody()->write($bookStr);

            return $response->withStatus(200);
        }
        else {
            $error['title'] = "Title entered incorrectly";
            $errorMessage = json_encode($error);
            $response->getBody()->write($errorMessage);
            return $response->withStatus(422);
        }
    }

    public function getAllBooks(ResponseInterface $response, array $args)
    {
       $books = $this->bookRepository->findAll();
       if(empty($books)) {
           $error = 'no books';
           $response->getBody()->write($error);

           return $response
               ->withStatus(400);
       } else {
           $allBooks = [];
           foreach ($books as $value) {
               $allBooks[] = $value->toArray();
           }
           $bookStr = json_encode($allBooks);
           $response->getBody()->write($bookStr);

           return $response
               ->withStatus(200);
       }
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $book = $this->bookRepository->findById($args['id']);
        $params = json_decode($request->getBody()->getContents(), true);

        $title = $params['title'];
        $author = $params['author'];
        $date = new DateTime($params['date']);

        if ($book instanceof Book) {
            if ($book->getTitle() !== $title) {
                $book->setTitle($title);
            }
            if ($book->getAuthor() !== $author) {
                $book->setAuthor($author);
            }
            if ($book->getDate() !== $date) {
                $book->setDate($date);
            }
            $this->bookRepository->add($book, true);
            return $response->withStatus(201);
        } else {
            $error['id'] = "book with this id not found";
            $errorMessage = json_encode($error);
            $response->getBody()->write($errorMessage);

            return $response->withStatus(422);
        }
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $book = $this->bookRepository->findById($args['id']);
        if($book instanceof Book) {
            $this->bookRepository->delete($book, true);

            return $response->withStatus(200);
        } else {
            $error['id'] = "book with this id not found";
            $errorMessage = json_encode($error);
            $response->getBody()->write($errorMessage);
            return $response->withStatus(422);
        }
    }
}

