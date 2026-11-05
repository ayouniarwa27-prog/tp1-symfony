<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TacheController extends AbstractController
{
    // Liste : non terminées en premier (bonus), puis les plus récentes
    #[Route('/taches', name: 'app_taches')]
    public function index(TacheRepository $tacheRepository): Response
    {
        $taches = $tacheRepository->findBy([], ['terminee' => 'ASC', 'dateCreation' => 'DESC']);

        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
        ]);
    }

    // Créer une tâche « en dur »
    #[Route('/taches/ajouter', name: 'app_tache_ajouter')]
    public function ajouter(EntityManagerInterface $em): Response
    {
        $tache = new Tache();
        $tache->setTitre('Réviser le TP Symfony');
        $tache->setDescription('Relire les parties Twig et Doctrine.');
        $tache->setTerminee(false);
        $tache->setDateCreation(new \DateTime());

        $em->persist($tache);
        $em->flush();

        $this->addFlash('success', 'Tâche n°' . $tache->getId() . ' créée.');

        return $this->redirectToRoute('app_taches');
    }

    // Détail d'une tâche
    #[Route('/taches/{id}', name: 'app_tache_detail', requirements: ['id' => '\d+'])]
    public function detail(Tache $tache): Response
    {
        return $this->render('tache/detail.html.twig', [
            'tache' => $tache,
        ]);
    }

    // Bonus : marquer une tâche comme terminée
    #[Route('/taches/{id}/terminer', name: 'app_tache_terminer', requirements: ['id' => '\d+'])]
    public function terminer(Tache $tache, EntityManagerInterface $em): Response
    {
        $tache->setTerminee(true);
        $em->flush();

        return $this->redirectToRoute('app_taches');
    }
}
