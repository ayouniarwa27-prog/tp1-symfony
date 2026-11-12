<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType; // 👉 vérifie le nom de ton formulaire
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class CategorieController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [ // 👉 dossier des templates
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/categories/nouvelle', name: 'app_categorie_nouvelle')]
    public function nouvelle(Request $request, EntityManagerInterface $em): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($categorie);
            $em->flush();

            $this->addFlash('success', 'Catégorie créée avec succès !');
            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categorie/nouvelle.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/categories/{id}', name: 'app_categorie_detail', requirements: ['id' => '\d+'])]
    public function detail(Categorie $categorie): Response
    {
        return $this->render('categorie/detail.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/categories/{id}/modifier', name: 'app_categorie_modifier', requirements: ['id' => '\d+'])]
    public function modifier(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès !');
            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categorie/modifier.html.twig', [
            'form' => $form,
            'categorie' => $categorie,
        ]);
    }

    #[Route('/categories/{id}/supprimer', name: 'app_categorie_supprimer', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function supprimer(Categorie $categorie, EntityManagerInterface $em): Response
    {
        if (!$categorie->getArticles()->isEmpty()) {
            $this->addFlash('danger', 'Impossible de supprimer une catégorie qui contient des articles.');
            return $this->redirectToRoute('app_categories');
        }

        $em->remove($categorie);
        $em->flush();

        $this->addFlash('success', 'Catégorie supprimée.');
        return $this->redirectToRoute('app_categories');
    }
}
