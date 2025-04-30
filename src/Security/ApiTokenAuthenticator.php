<?php

namespace App\Security;

use App\Repository\UserRepository as RepositoryUserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Security\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use App\Security\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException as ExceptionUserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;


class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(private RepositoryUserRepository $repository)
    {
    }
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }
    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }
        $user = $this->repository->findOneBy(['apiToken' => $apiToken]);
        if (null === $user) {
            throw new ExceptionUserNotFoundException();
        }
        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())],
            Response::HTTP_UNAUTHORIZED
        );
    }
}