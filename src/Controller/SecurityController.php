<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use  Symfony\Component\Routing\Attribute\Route; 
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse as HttpFoundationJsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface as SerializerSerializerInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Items;

 
 


#[Route('/api',name:'app_api_')]
final class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerSerializerInterface $serializer)
    {
    }
 
    #[Route('/registration', name: 'registration', methods: ['POST','OPTION'])]
#[OA\Post(
    path: "/api/registration",
    summary: "Inscription d'un nouvel utilisateur",
    requestBody: new OA\RequestBody(
        required: true,
        description: "Données de l'utilisateur à inscrire",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "email", type: "string", example: "adresse@email.com"),
                new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                new OA\Property(property: "first_name", type: "string", example: "Jean"),
                new OA\Property(property: "last_name", type: "string", example: "Dupont"),
                new OA\Property(property: "guest_number", type: "integer", example: 2)
            ]
        )
    ),
    responses: [
        new OA\Response(
            response: 201,
            description: "Utilisateur inscrit avec succès",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "user", type: "string", example: "Nom d'utilisateur"),
                    new OA\Property(property: "apiToken", type: "string", example: "31a023e212f116124a36af14ea0c1c3806eb9378"),
                    new OA\Property(
                        property: "roles",
                        type: "array",
                        items: new Items(type: "string", example: "ROLE_USER")
                    ),
                ]
            )
        )
    ]
)] 
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

//https://symfony.com/doc/current/security.html#json-login
    #[Route('/login', name: 'login', methods: 'POST')]
    #[OA\Post(
        path: "/api/login",
        summary: "Connecter un utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: " Données de l’utilisateur pour se connecter",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "adresse@email.com"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "user", type: "string", example: "Nom d'utilisateur"),
                        new OA\Property(property: "apiToken", type: "string", example: "31a023e212f116124a36af14ea0c1c3806eb9378"),
                        new OA\Property(
                            property: "roles",
                            type: "array",
                            items: new Items(type: "string", example: "ROLE_USER")
                        ),
                    ]
                )
            )
        ]
    )] 

    public function login(#[CurrentUser] ?User $user): JsonResponse
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }
        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
        ]);
    }
}


 