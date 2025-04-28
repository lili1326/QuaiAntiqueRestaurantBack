<?php

namespace App\Controller;
use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
 



  //  #[Route('/restaurant', name: 'app_restaurant')]
  //  public function index(): Response
  //  {
   //     return $this->render('restaurant/index.html.twig', [
   //         'controller_name' => 'RestaurantController',
   //     ]);
   // }
    
#[Route('api/restaurant',name:'api_restaurant_')]
final class RestaurantController extends AbstractController
{
    public function __construct (private EntityManagerInterface $manager,private RestaurantRepository $repository)
    {      
    }


    #[route(name:'new',methods:'POST')]
    public function new():Response
    {
          // 2. Créer le Restaurant
        $restaurant = new Restaurant();
        $restaurant->setName('Quai Antique');
        $restaurant->setDescription('Very good');
        $restaurant->setMaxGuest(50);
        $restaurant->setCreateAt(new DateTimeImmutable());

        //a stocker en base
        $this->manager->persist($restaurant);
        $this->manager->flush();

        // 4. Retourner une réponse
        return $this->json(
            ['message'=>"restaurant ressource créer avec {$restaurant->getId()}id"],
         Response::HTTP_CREATED,
        );      

    }
    #[route('/{id}',name: 'show',methods:'GET')]
    public function show(int $id):Response{
        $restaurant = $this->repository->findOneBy(['id' => $id]);

        if (!$restaurant) {
            throw $this->createNotFoundException("No Restaurant found for {$id} id");
        }
        return $this->json(
            ['message' => "A Restaurant was found : {$restaurant->getName()} for {$restaurant->getId()} id"]
        );
    }

    #[route('/{id}',name: 'edit',methods:'PUT')]
    public function edit(int $id):Response{
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if (!$restaurant) {
            throw new \Exception("No Restaurant found for {$id} id");
        }
        $restaurant->setName('Restaurant name updated');
        $this->manager->flush();
        return $this->redirectToRoute('api_restaurant_show', ['id' => $restaurant->getId()]);
    }

    #[route('/{id}',name: 'delete',methods:'DELETE')]
    public function delete(int  $id):Response{
        $restaurant = $this->repository->findOneBy(['id' => $id]);
        if (!$restaurant) {
            throw new \Exception("No Restaurant found for {$id} id");
        }
        $this->manager->remove($restaurant);
        $this->manager->flush();
        return $this->json(['message' => "Restaurant resource deleted"], Response::HTTP_NO_CONTENT);
    }
}