<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $posts = $doctrine
            ->getRepository(Post::class)
            ->findAll();

        $data = [];

        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->getId(),
                'author' => $post->getAuthor(),
                'subject' => $post->getSubject(),
                'text' => $post->getText(),
                'category' => $post->getCategory(),
            ];

        }
        return $this->json($data);
    }

    /**
     * @Route("/post", name="post_new", methods={"POST"})
     */
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $post = new post();
        $post->setAuthor($request->request->get('author'));
        $post->setCategory($request->request->get('category'));
        $post->setSubject($request->request->get('subject'));
        $post->setText($request->request->get('text'));
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->json('Created new post successfully with id ' . $post->getId());
    }

    /**
     * @Route("/post/{id}", name="post_show", methods={"GET"})
     */
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $post = $doctrine->getRepository(post::class)->find($id);

        if (!$post) {

            return $this->json('No post found for id' . $id, 404);
        }

        $data =  [
            'id' => $post->getId(),
            'author' => $post->getAuthor(),
            'category' => $post->getCategory(),
            'subject' => $post->getSubject(),
            'text' => $post->getText(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/post/{id}", name="post_edit", methods={"PUT"})
     */
    public function edit(ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(post::class)->find($id);

        if (!$post) {
            return $this->json('No post found for id' . $id, 404);
        }

        $post->setAuthor($request->request->get('author'));
        $post->setCategory($request->request->get('category'));
        $post->setSubject($request->request->get('subject'));
        $post->setText($request->request->get('text'));
        $entityManager->flush();

        $data =  [
            'id' => $post->getId(),
            'author' => $post->getAuthor(),
            'category' => $post->getCategory(),
            'subject' => $post->getSubject(),
            'text' => $post->getText(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("/post/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(post::class)->find($id);

        if (!$post) {
            return $this->json('No post found for id ' . $id, 404);
        }

        $entityManager->remove($post);
        $entityManager->flush();

        return $this->json('Deleted a post successfully with id ' . $id);
    }
}
