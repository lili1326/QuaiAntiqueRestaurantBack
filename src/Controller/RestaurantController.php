<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\AbstracNormalizer;
use OpenApi\Annotations\Parameter;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Schema;

#[Route('api/restaurant',name:'app_api_restaurant_')]
final class RestaurantController extends AbstractController
{
    public function __construct (
        private EntityManagerInterface $manager,
        private RestaurantRepository $repository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator
        )
    {      
    }

    #[route('',methods:'POST')]
    
    #[OA\Post(
        path: "/api/restaurant",
        summary: "Créer un restaurant",
        requestBody: new OA\RequestBody(
            required: true,
            description: "  Donnes du restaurant a créer",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Nom du restaurant "),
                    new OA\Property(property: "description", type: "string", example: " Description du restaurant"),
                    new OA\Property(property: "max_guest", type: "int", example: " Nombre de place maximun"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Restaurant créer avec succés",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: " 1 "),
                        new OA\Property(property: "name", type: "string", example: " Nom du restaurant "),
                        new OA\Property(property: "description", type: "string", example: "Description du restaurant"),
                        new OA\Property(property: "max_guest", type: "integer", example: " Nombre de place maximun"),
                        new OA\Property(property: "createAt", type: "string",format:"date-time"),
                    ]
                )
            )
        ]
    )] 

    public function new(Request $request):JsonResponse
    {   
        $restaurant = $this->serializer->deserialize($request->getContent(), Restaurant::class, 'json');
        $restaurant->setCreateAt(new DateTimeImmutable());

        $this->manager->persist($restaurant);
        $this->manager->flush();

        $responseData = $this->serializer->serialize($restaurant, 'json');

        $location = $this->urlGenerator->generate(
            'app_api_restaurant_show',
            ['id' => $restaurant->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location],true); 
    }    
    
    #[route('/{id}',name: 'show',methods:'GET')]

    #[OA\Get(
        path: "/api/restaurant/{id}",
        summary: "Afficher un restaurant par son ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du restaurant à afficher",
                schema: new Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Restaurant trouvé",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Nom du restaurant"),
                        new OA\Property(property: "description", type: "string", example: "Description du restaurant"),
                        new OA\Property(property: "max_guest", type: "integer", example: 80),
                        new OA\Property(property: "createAt", type: "string", format: "date-time"),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Restaurant non trouvé"
            )
        ]
    )]
    public function show(int $id):JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
            $restaurantData = $this->serializer->serialize($restaurant,'json');

            return new JsonResponse($restaurantData,Response::HTTP_OK,[],true);
        }
        return new JsonResponse(null,Response::HTTP_NOT_FOUND);
        
    }

    #[route('/{id}',name: 'edit',methods:'PUT')]

    #[OA\Put(
        path: '/api/restaurant/{id}',
        summary: 'Modifier un restaurant existant',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID du restaurant à modifier',
                schema: new Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Données mises à jour du restaurant',
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Nouveau nom'),
                    new OA\Property(property: 'description', type: 'string', example: 'Nouvelle description'),
                    new OA\Property(property: 'max_guest', type: 'integer', example: 100),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: 'Mise à jour réussie (pas de contenu retourné)'
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant non trouvé'
            )
        ]
    )]

    public function edit(int $id, Request $request):JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if (!$restaurant) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    
        $restaurant = $this->serializer->deserialize(
            $request->getContent(),
            Restaurant::class,
            'json',
            [\Symfony\Component\Serializer\Normalizer\AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant]
        );
    
        $restaurant->setUpdateAt(new \DateTimeImmutable());
    
        $this->manager->flush();
    
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    
    }
    

    #[route('/{id}',name: 'delete',methods:'DELETE')]

    #[OA\Delete(
        path: '/api/restaurant/{id}',
        summary: 'Supprimer un restaurant',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID du restaurant à supprimer',
                schema: new Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Restaurant supprimé avec succès'
            ),
            new OA\Response(
                response: 404,
                description: 'Restaurant non trouvé'
            )
        ]
    )]
    public function delete(int  $id):JsonResponse
    {
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if ($restaurant) {
        $this->manager->remove($restaurant);
        $this->manager->flush();  
        
        return new JsonResponse(null,Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null,Response::HTTP_NOT_FOUND);      
    }
}