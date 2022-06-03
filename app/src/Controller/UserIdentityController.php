<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserIdentity;
use App\Repository\UserRepository;
use App\Repository\UserIdentityRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

class UserIdentityController
{
    private UserRepository $userRepository;
    private UserIdentityRepository $userIdentityRepository;

    public function __construct(UserRepository $userRepository, UserIdentityRepository $userIdentityRepository)
    {
        $this->userRepository = $userRepository;
        $this->userIdentityRepository = $userIdentityRepository;
    }

    public function signUp(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $params = json_decode($request->getBody()->getContents(), true);

        $errors = $this->getErrors($params);

        if (!empty($errors)) {
            $newStr = json_encode($errors);
            $response->getBody()->write($newStr);
            return $response;
        }

        $name = $params['name'];
        $phone = $params['phone'];
        $password = $params['password'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $user = new User($name, $phone);
        $this->userRepository->add($user, true);

        $user = $this->userRepository->findOneByNameField($name);
        $token = Uuid::uuid1()->toString();

        $userIdentity = new UserIdentity($user->getId(), $user->getPhone(), $password, $token);
        $this->userIdentityRepository->add($userIdentity, true);

        return $response
            ->withStatus(201)
            ->withHeader("Token", $token);
    }

    private function getErrors(array $params): array
    {
        $messages = [];
        if(empty($params['name'])) {
            $messages['name'] ='Name not be empty';
        } else {
            $findUser = $this->userRepository->findByName($params['name']);
            if($findUser instanceof User) {
                $messages['name'] = 'user with the same name already exists';
            }
        }

        if(empty($params['phone'])) {
            $messages['phone'] = 'Phone not be empty';
        } else {
            $findUserPhone = $this->userRepository->findByPhone($params['phone']);
            if ($findUserPhone instanceof User) {
                $messages['phone'] = 'user with the same phone already exists';
            }
        }

        if(empty($params['password'])) {
            $messages['password'] = 'Password not be empty';
        }

        return $messages;
    }

    public function signIn(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $loginParams = json_decode($request->getBody()->getContents(), true);

        $login = $loginParams['login'];
        $password = $loginParams['password'];

        $userIdentity = $this->userIdentityRepository->findOneByLoginField($login);
        if($userIdentity instanceof UserIdentity) {
            $result = password_verify($password ,$userIdentity->getPassword());
            if($result) {
                $token = $userIdentity->getToken();

                return $response
                    ->withStatus(200)
                    ->withHeader('Token', $token);
            } else {
                $errorPass['password'] = "Password entered incorrectly";
                $errorMessage = json_encode($errorPass);
                $response->getBody()->write($errorMessage);

                return $response
                    ->withStatus(422);
            }
        } else {
            $errorLog['login'] = "Login entered incorrectly";
            $errorMessageLog = json_encode($errorLog);
            $response->getBody()->write($errorMessageLog);

            return $response
                ->withStatus(422);
        }
    }
}