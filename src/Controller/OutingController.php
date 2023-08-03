<?php

namespace App\Controller;

use App\Entity\Outing;
use App\Form\CancelOutingType;
use App\Form\OutingsFilterType;
use App\Form\OutingType;
use App\Data\SearchData;
use App\Repository\OutingRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use App\Service\OutingStatusUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class OutingController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'outing_list')]
    public function list(OutingRepository $sortieRepository, Request $request, OutingStatusUpdater $outingStatusUpdater): Response
    {
        // Update Outing Status
        $outingStatusUpdater->updateAllOutingsStatuses();

        $data = new SearchData();
        $data->setUser($this->getUser());

        $form = $this->createForm(OutingsFilterType::class, $data);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $outings = $sortieRepository->findWithFilters($data);
        } else {
            $outings = $sortieRepository->findWithFilters($data);
        }

        return $this->render('outing/outing.html.twig', [
            "outings" => $outings,
            'form'=> $form->createView()
        ]);
    }

    #[Route('/create', name: 'outing_create')]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function create(Request $request, PlaceRepository $placeRepository, StatusRepository $statusRepository, EntityManagerInterface $entityManager): Response
    {
        $outing = new Outing();
        $outingForm = $this->createForm(OutingType::class, $outing);

        $outingForm->handleRequest($request);
        if ($outingForm->isSubmitted() && $outingForm->isValid()) {
            $outing->setCampus($this->getUser()->getCampus());
            $outing->setOrganizer($this->getUser());

            if ($request->request->has('publish')) {
                $status = $statusRepository->findOneBy(['libelle' => 'Ouverte']);
                $outing->setStatus($status);
                $this->addFlash('success', 'La sortie a été créée et publiée avec succès !');
            } elseif ($request->request->has('save')) {
                $status = $statusRepository->findOneBy(['libelle' => 'Créée']);
                $outing->setStatus($status);
                $this->addFlash('success', 'La sortie a été créée avec succès !');
            } elseif ($request->request->has('cancel')) {
                return $this->redirectToRoute('outing_list');
            }

            $entityManager->persist($outing);
            $entityManager->flush();

            // Redirect to the outing list page
            return $this->redirectToRoute('outing_list');
        }

        $places = $placeRepository->findAll();

        return $this->render('outing/create.html.twig', [
            "outingForm" => $outingForm->createView(),
            "places" => $places
        ]);
    }

    #[Route('/outing/{id}/edit', name: 'outing_edit')]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function edit(Outing $outing, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $outing->getOrganizer() || $outing->getStatus()->getLibelle() !== 'Créée') {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette sortie.');
        }

        $form = $this->createForm(OutingType::class, $outing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été modifiée avec succès !');

            $referer = $request->get('referer');
            return $this->redirect($referer);
        }

        $referer = $this->requestStack->getCurrentRequest()->headers->get('referer');

        return $this->render('outing/edit.html.twig', [
            'outing' => $outing,
            'form' => $form->createView(),
            'referer' => $referer,
        ]);
    }

    #[Route('/outing/{id}/register', name: 'outing_register')]
    public function register(Outing $outing, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$request->getSession()->has('confirm_register')) {
            return $this->redirectToRoute('outing_confirm_register', ['id' => $outing->getId()]);
        }
        $request->getSession()->remove('confirm_register');

        $user = $this->getUser();

        if ($outing->getStatus()->getLibelle() === 'Ouverte' &&
            count($outing->getAttendees()) < $outing->getNbInscriptionMax() &&
            $outing->getDateLimiteInscription() > new \DateTime() &&
            !$outing->getAttendees()->contains($user)) {
            $outing->addAttendee($user);
            $user->addOutingsPlanned($outing);
            $entityManager->persist($outing);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes maintenant inscrit à cette sortie !');

            $referer = $request->get('referer', $this->generateUrl('outing_list'));
            return $this->redirect($referer);
        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas vous inscrire à cette sortie.');

            $referer = $request->get('referer', $this->generateUrl('outing_list'));
            return $this->redirect($referer);
        }
    }

    #[Route('/outing/{id}', name: 'outing_detail')]
    public function detail(Outing $outing): Response
    {
        return $this->render('outing/detail.html.twig', [
            'outing' => $outing,
        ]);
    }

    #[Route('/outing/{id}/confirm_register', name: 'outing_confirm_register')]
    public function confirmRegister(Outing $outing, Request $request): Response
    {
        $user = $this->getUser();

        if ($outing->getStatus()->getLibelle() === 'Ouverte' &&
            count($outing->getAttendees()) < $outing->getNbInscriptionMax() &&
            $outing->getDateLimiteInscription() > new \DateTime() &&
            !$outing->getAttendees()->contains($user)) {

            $request->getSession()->set('confirm_register', true);
            $request->getSession()->set('referer', $request->headers->get('referer'));

            return $this->render('outing/confirm_register.html.twig', [
                'outing' => $outing,
            ]);
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas autorisé à vous inscrire à cette sortie.');
            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }
    }

    #[Route('/outing/{id}/unregister', name: 'outing_unregister')]
    public function unregister(Outing $outing, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$request->getSession()->has('confirm_unregister')) {
            return $this->redirectToRoute('outing_confirm_unregister', ['id' => $outing->getId()]);
        }
        $request->getSession()->remove('confirm_unregister');

        $user = $this->getUser();

        if ($outing->getDateHeureDebut() > new \DateTime() && $outing->getAttendees()->contains($user)) {
            $outing->removeAttendee($user);
            $user->removeOutingsPlanned($outing);
            $entityManager->persist($outing);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes maintenant désinscrit de cette sortie !');

            $referer = $request->getSession()->get('referer', $this->generateUrl('outing_list'));
            $request->getSession()->remove('referer');
            return $this->redirect($referer);
        } else {
            $this->addFlash('warning', 'Vous ne pouvez pas vous désinscrire de cette sortie.');

            $referer = $request->getSession()->get('referer', $this->generateUrl('outing_list'));
            $request->getSession()->remove('referer');
            return $this->redirect($referer);
        }
    }

    #[Route('/outing/{id}/confirm_unregister', name: 'outing_confirm_unregister')]
    public function confirmUnregister(Outing $outing, Request $request): Response
    {
        $user = $this->getUser();

        if ($outing->getDateHeureDebut() > new \DateTime() && $outing->getAttendees()->contains($user)) {
            $request->getSession()->set('confirm_unregister', true);
            $request->getSession()->set('referer', $request->headers->get('referer'));

            return $this->render('outing/confirm_unregister.html.twig', [
                'outing' => $outing,
            ]);
        } else {
            $this->addFlash('warning', 'Vous n\'êtes pas inscrit à cette sortie.');
            return $this->redirectToRoute('outing_detail', ['id' => $outing->getId()]);
        }
    }

    #[Route('/outing/{id}/publish', name: 'outing_publish')]
    public function publish(Outing $outing, Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        if (!$request->getSession()->has('confirm_publish')) {
            return $this->redirectToRoute('outing_confirm_publish', ['id' => $outing->getId()]);
        }
        $request->getSession()->remove('confirm_publish');

        // Set the status to "Ouverte"
        $status = $statusRepository->findOneBy(['libelle' => 'Ouverte']);
        $outing->setStatus($status);

        // Save the changes to the database
        $entityManager->persist($outing);
        $entityManager->flush();

        // Add a flash message
        $this->addFlash('success', 'La sortie a été publiée avec succès !');

        // Get the referer from the session and redirect to it
        $referer = $request->getSession()->get('referer');
        $request->getSession()->remove('referer');
        return $this->redirect($referer);
    }

    #[Route('/outing/{id}/confirm_publish', name: 'outing_confirm_publish')]
    public function confirmPublish(Outing $outing, Request $request): Response
    {
        // Check if the user is the organizer of the outing
        if ($this->getUser() !== $outing->getOrganizer()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas l\'organisateur de cette sortie.');
        }

        // Check if the outing is in "Créée" status
        if ($outing->getStatus()->getLibelle() !== 'Créée') {
            throw $this->createAccessDeniedException('Cette sortie n\'est pas dans l\'état "Créée".');
        }

        // Store the referer in the session
        $request->getSession()->set('referer', $request->headers->get('referer'));

        $request->getSession()->set('confirm_publish', true);

        return $this->render('outing/confirm_publish.html.twig', [
            'outing' => $outing,
        ]);
    }

    #[Route('/outing/{id}/cancel', name: 'outing_cancel', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ORGANISATEUR')]
    public function cancel(Outing $outing, Request $request, StatusRepository $statusRepository, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $outing->getOrganizer() || $outing->getStatus()->getLibelle() !== 'Créée') {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à annuler cette sortie.');
        }

        $form = $this->createForm(CancelOutingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $statusRepository->findOneBy(['libelle' => 'Annulée']);
            $outing->setStatus($status);
            $outing->setCancellationReason($form->get('cancellationReason')->getData());
            $entityManager->persist($outing);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a été annulée avec succès !');

            $referer = $request->getSession()->get('referer');
            $request->getSession()->remove('referer');
            return $this->redirect($referer);
        }

        return $this->render('outing/cancel.html.twig', [
            'outing' => $outing,
            'form' => $form->createView(),
        ]);
    }
}
