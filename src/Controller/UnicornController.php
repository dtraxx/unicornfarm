<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Unicorn;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UnicornController extends AbstractController
{
    #[Route('/unicorn', name: 'app_unicorn')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $unicorns = $doctrine
            ->getRepository(Unicorn::class)
            ->findAll();

        $data = [];

        foreach ($unicorns as $unicorn) {
            $data[] = [
                'id' => $unicorn->getId(),
                'name' => $unicorn->getName(),
                'color' => $unicorn->getColour(),
                'age' => $unicorn->getAge(),
                'price' => $unicorn->getPrice(),
            ];
        }


        return $this->json($data);
    }

    /**
     * @Route("/unicorn", name="unicorn_new", methods={"POST"})
     */
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $unicorn = new unicorn();
        $unicorn->setName($request->request->get('name'));
        $unicorn->setColour($request->request->get('colour'));
        $unicorn->setAge($request->request->get('age'));
        $unicorn->setPrice($request->request->get('price'));
        $entityManager->persist($unicorn);
        $entityManager->flush();

        return $this->json('Created new unicorn successfully with id ' . $unicorn->getId());
    }

    /**
     * @Route("/unicorn/{id}", name="unicorn_show", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $unicorn = $doctrine->getRepository(unicorn::class)->find($id);

        if (!$unicorn) {

            return $this->json('No unicorn found for id' . $id, 404);
        }

        $data =  [
            'id' => $unicorn->getId(),
            'name' => $unicorn->getName(),
            'color' => $unicorn->getColour(),
            'age' => $unicorn->getAge(),
            'price' => $unicorn->getPrice(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/unicorn/{id}", name="unicorn_edit", methods={"PUT"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $unicorn = $entityManager->getRepository(unicorn::class)->find($id);

        if (!$unicorn) {
            return $this->json('No unicorn found for id' . $id, 404);
        }

        $unicorn->setName($request->request->get('name'));
        $unicorn->setPrice($request->request->get('price'));
        $unicorn->setAge($request->request->get('age'));
        $unicorn->setColour($request->request->get('colour'));
        $entityManager->flush();

        $data =  [
            'id' => $unicorn->getId(),
            'name' => $unicorn->getName(),
            'color' => $unicorn->getColour(),
            'age' => $unicorn->getAge(),
            'price' => $unicorn->getPrice(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/unicorn/{id}", name="unicorn_delete", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $unicorn = $entityManager->getRepository(unicorn::class)->find($id);

        if (!$unicorn) {
            return $this->json('No unicorn found for id ' . $id, 404);
        }

        $entityManager->remove($unicorn);
        $entityManager->flush();

        return $this->json('Deleted a unicorn successfully with id ' . $id);
    }

    /**
     * @Route("/unicorn/posts/{id}", name="unicorn_showposts")
     */

    public function showPosts(ManagerRegistry $doctrine, int $unicornId): Response
    {
        $posts = $doctrine
            ->getRepository(Post::class)
            ->findBy(
                ['unicorn_id' => $unicornId],
                ['unicorn_id' => 'desc']
            );

        if(!$posts){
            return $this->json('No posts found for unicorn_id ' . $unicornId, 404);
        }

        $data = [];

        foreach ($posts as $post)
        {
            $data[] = [
                'id' => $post->getId(),
                'author' => $post->getAuthor(),
                'category' =>$post->getCategory(),
                'text' => $post->getText(),
                'subject' => $post->getSubject()
            ];
        }

        return $this->json($data);
    }

    
}
