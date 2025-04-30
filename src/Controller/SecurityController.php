<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;

#[Route('/api',name:'app_api_')]
final class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerSerializerInterface $serializer)
    {
    }
    #[Route('/registration', name: 'registration', methods: 'POST')]
    public function register(Request $request, HasherUserPasswordHasherInterface $passwordHasher): HttpFoundationJsonResponse
    {

       
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreateAt(new DateTimeImmutable());
        $this->manager->persist($user);
        $this->manager->flush();
        return new JsonResponse(
            ['user'  => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()],
            Response::HTTP_CREATED
        );
    }
} 

 