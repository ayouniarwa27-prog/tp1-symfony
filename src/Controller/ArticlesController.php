<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticlesController extends AbstractController
{
    #[Route('/articles/nouveau', name: 'app_article_nouveau')]
    public function nouveau(EntityManagerInterface $em): Response
    {
        $article = new Article();
        $article->setTitre('Mon premier article');
        $article->setContenu('Ceci est le contenu de mon premier article créé avec Doctrine.');
        $article->setAuteur('Étudiant');
        $article->setDateCreation(new \DateTime());
        $article->setPublie(true);

        $em->persist($article);
        $em->flush();

        return new Response("Article créé avec l'id : " . $article->getId());
    }

    #[Route('/articles', name: 'app_articles')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('articles/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/articles/{id}', name: 'app_article_detail', requirements: ['id' => '\d+'])]
    public function detail(Article $article): Response
    {
        return $this->render('articles/detail.html.twig', [
            'article' => $article,
        ]);
    }
}
