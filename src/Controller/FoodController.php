<?php

namespace App\Controller;

use App\Entity\Food;
use App\Repository\FoodRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
 
 

//final class FoodController extends AbstractController
//{
   // #[Route('/food', name: 'app_food')]
   // public function index(): Response
    //{
     //   return $this->render('food/index.html.twig', [
     //       'controller_name' => 'FoodController',
     //   ]);
   // }
//}

#[Route('api/food',name:'api_food_')]
final class FoodController extends AbstractController
{
    public function __construct (private EntityManagerInterface $manager,private FoodRepository $repository)
    {      
    }


    #[route(name:'new',methods:'POST')]
    public function new():Response
    {
          // 2. Créer le Restaurant
        $food = new Food();
        $food->setTitle('carotte rapée');
        $food->setDescription('carotte rapée avec vignaigrette');
        $food->setPrice(8);
        $food->setCreateAt(new DateTimeImmutable());

        //a stocker en base
        $this->manager->persist($food);
        $this->manager->flush();

        // 4. Retourner une réponse
        return $this->json(
            ['message'=>"food ressource créer avec {$food->getId()}id"],
         Response::HTTP_CREATED,
        );      

    }
    #[route('/{id}',name: 'show',methods:'GET')]
    public function show(int $id):Response{
        $food = $this->repository->findOneBy(['id' => $id]);

        if (!$food) {
            throw $this->createNotFoundException("No Food found for {$id} id");
        }
        return $this->json(
        [
        'id' => $food->getId(),
        'title' => $food->getTitle(),
        'description' => $food->getDescription(), 
        ]
        );
    }

    #[route('/{id}',name: 'edit',methods:'PUT')]
    public function edit(int $id):Response{
        $food = $this->repository->findOneBy(['id' => $id]);
        if (!$food) {
            throw new \Exception("No Food found for {$id} id");
        }
        $food->setTitle('Food name updated');
        $this->manager->flush();
        return $this->redirectToRoute('api_food_show', ['id' => $food->getId()]);
    }

    #[route('/{id}',name: 'delete',methods:'DELETE')]
    public function delete(int  $id):Response{
        $food = $this->repository->findOneBy(['id' => $id]);
        if (!$food) {
            throw new \Exception("No Food found for {$id} id");
        }
        $this->manager->remove($food);
        $this->manager->flush();
        return $this->json(['message' => "Food resource deleted"], Response::HTTP_NO_CONTENT);
    }
}