<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Items;

#[Route('/api/account', name: 'api_account_')]
class AccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager,
        private SerializerInterface $serializer
    ) {}

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[OA\Get(
        path: '/api/account/me',
        summary: 'Récupérer toutes les informations de l’utilisateur connecté',
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'X-AUTH-TOKEN',
                in: 'header',
                required: true,
                description: 'Token d’authentification',
                schema: new Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Utilisateur connecté',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(property: 'roles', type: 'array', items: new Items(type: 'string')),
                        new OA\Property(property: 'apiToken', type: 'string'),
                        new OA\Property(property: 'createAt', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updateAt', type: 'string', format: 'date-time', nullable: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Non authentifié'),
        ]
    )]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['error' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $data = $this->serializer->serialize($user, 'json');

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/edit', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/account/edit',
        summary: 'Modifier son compte utilisateur avec un ou tous les champs',
        tags: ['Account'],
        parameters: [
            new OA\Parameter(
                name: 'X-AUTH-TOKEN',
                in: 'header',
                required: true,
                description: 'Token d’authentification',
                schema: new Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'email', type: 'string', example: 'nouveau@mail.com'),
                    new OA\Property(property: 'password', type: 'string', example: 'newpass123'),
                    new OA\Property(property: 'roles', type: 'array', items: new Items(type: 'string'), example: ['ROLE_USER'])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 204, description: 'Modification réussie'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 401, description: 'Non authentifié'),
        ]
    )]
    public function edit(
        #[CurrentUser] ?User $user,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
        if (!$user) {
            return new JsonResponse(['error' => 'Non authentifié'], Response::HTTP_UNAUTHORIZED);
        }

        $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [\Symfony\Component\Serializer\Normalizer\AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $data = $request->toArray();
        if (!empty($data['password'])) {
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        }

        $user->setUpdateAt(new \DateTimeImmutable());

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}