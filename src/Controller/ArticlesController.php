<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'app_articles')]
    public function index(): Response
    {
        $articles = [
            ['titre' => 'Introduction à Symfony',   'auteur' => 'Alice',  'publie' => true],
            ['titre' => 'Les bases de Twig',        'auteur' => 'Bob',    'publie' => true],
            ['titre' => 'Doctrine ORM en pratique', 'auteur' => 'Claire', 'publie' => false],
            ['titre' => 'Sécurité avec Symfony',    'auteur' => 'David',  'publie' => true],
            ['titre' => 'API Platform (brouillon)', 'auteur' => 'Eve',    'publie' => false],
        ];

        return $this->render('articles/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
