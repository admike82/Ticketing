<?php

namespace App\Security;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class AccessTokenHandler extends AbstractAuthenticator
{
  public function __construct(
    private ApplicationRepository $repository
  ) {
  }

  public function supports(Request $request): ?bool
  {
    return str_starts_with($request->getPathInfo(), '/api/');
  }

  public function authenticate(Request $request): Passport
  {
    $apiToken = $request->headers->get('Authorization');

    if (null === $apiToken) {
      throw new CustomUserMessageAuthenticationException('Token obligatoire !');
    }

    $bearer = explode(' ', $apiToken)[1];
    return new SelfValidatingPassport(
      new UserBadge($bearer, function ($bearer) {
        $application = $this->repository->findOneBy(['token' => $bearer]);

        if (!$application) {
          throw new UserNotFoundException("Token invalide !");
        }

        return $application;
      })
    );
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    return null;
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
  {
    $data = [
      'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
    ];

    return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
  }


  // public function getUserBadgeFrom(string $accessToken): UserBadge
  // {
  //   dd($accessToken);
  //   $accessToken = $this->repository->findOneBy(['token' => $accessToken]);
  //   if (null === $accessToken || !$accessToken instanceof Application) {
  //     return new JsonResponse(["status" => "401", "message" => "Token invalide"]);
  //   }

  //   // and return a UserBadge object containing the user identifier from the found token
  //   return new UserBadge($accessToken->getId());
  // }
}
