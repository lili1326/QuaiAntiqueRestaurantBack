<?php

namespace App\Controller;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;

     
#[Route('api/category',name:'api_category_')]
final class CategoryController extends AbstractController
{
    public function __construct (private EntityManagerInterface $manager,private CategoryRepository $repository)
    {      
    }


    #[route(name:'new',methods:'POST')]
    public function new():Response
    {
          // 2. Créer le Restaurant
        $category = new Category();
        $category->setTitle('Entrée');
        $category->setCreateAt(new DateTimeImmutable());

        //a stocker en base
        $this->manager->persist($category);
        $this->manager->flush();

        // 4. Retourner une réponse
        return $this->json(
            ['message'=>" category ressource créer avec {$category->getId()}id"],
         Response::HTTP_CREATED,
        );      

    }
    #[route('/{id}',name: 'show',methods:'GET')]
    public function show(int $id):Response{
        $category = $this->repository->findOneBy(['id' => $id]);

        if (!$category) {
            throw $this->createNotFoundException("No  category found for {$id} id");
        }
        return $this->json(
            ['message' => "A category was found : {$category->getTitle()} for {$category->getId()} id"]
        );
    }

    #[route('/{id}',name: 'edit',methods:'PUT')]
    public function edit(int $id):Response{
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw new \Exception("No category found for {$id} id");
        }
        $category->setTitle('category name updated');
        $this->manager->flush();
        return $this->redirectToRoute('api_category_show', ['id' => $category->getId()]);
    }

    #[route('/{id}',name: 'delete',methods:'DELETE')]
    public function delete(int  $id):Response{
        $category = $this->repository->findOneBy(['id' => $id]);
        if (!$category) {
            throw new \Exception("No category found for {$id} id");
        }
        $this->manager->remove($category);
        $this->manager->flush();
        return $this->json(['message' => "category resource deleted"], Response::HTTP_NO_CONTENT);
    }
}