<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategorieController extends AbstractController
{
    // READ — liste
    #[Route('/categories', name: 'app_categories')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    // CREATE — utilise maintenant CategorieType
    #[Route('/categories/nouvelle', name: 'app_categorie_nouvelle')]
    public function nouvelle(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie "' . $categorie->getNom() . '" créée !');

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categorie/nouvelle.html.twig', [
            'formulaire' => $form,
        ]);
    }

    // READ — détail + ses articles
    #[Route('/categories/{id}', name: 'app_categorie_detail', requirements: ['id' => '\d+'])]
    public function detail(Categorie $categorie): Response
    {
        return $this->render('categorie/detail.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    // UPDATE
    #[Route('/categories/{id}/modifier', name: 'app_categorie_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès !');

            return $this->redirectToRoute('app_categorie_detail', ['id' => $categorie->getId()]);
        }

        return $this->render('categorie/modifier.html.twig', [
            'formulaire' => $form,
            'categorie' => $categorie,
        ]);
    }

    // DELETE (POST + jeton CSRF)
    #[Route('/categories/{id}/supprimer', name: 'app_categorie_supprimer', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function supprimer(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('supprimer_categorie_' . $categorie->getId(), $request->request->get('_token'))) {
            // Les articles de la catégorie deviennent « sans catégorie »
            // (sinon la clé étrangère empêcherait la suppression)
            foreach ($categorie->getArticles() as $article) {
                $article->setCategorie(null);
            }

            $em->remove($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie supprimée avec succès.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('app_categories');
    }
}
