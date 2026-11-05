<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AccueilController extends AbstractController
{
    // Page d'accueil (générée par make:controller)
    #[Route('/accueil', name: 'app_accueil')]
    public function index(): Response
    {
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    // Exercice 2.1 — route avec paramètre
    #[Route('/bonjour/{prenom}', name: 'app_bonjour')]
    public function bonjour(string $prenom): Response
    {
        return new Response("<h1>Bonjour $prenom ! Bienvenue sur Symfony 7.4</h1>");
    }

    // Exercice 2.2 — valeur par défaut + contrainte (chiffres uniquement)
    #[Route('/profil/{id}', name: 'app_profil', requirements: ['id' => '\d+'], defaults: ['id' => 1])]
    public function profil(int $id): Response
    {
        return new Response("<h1>Profil de l'utilisateur n°$id</h1>");
    }
}
