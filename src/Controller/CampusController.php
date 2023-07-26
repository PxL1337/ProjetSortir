<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/campus', name: 'admin_')]
class CampusController extends AbstractController
{
    #[Route('/', name: 'campus_index', methods: ['GET'])]
    public function index(CampusRepository $campusRepository): Response
    {
        return $this->render('admin/campus/index.html.twig', [
            'campuses' => $campusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'campus_new', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('admin_campus_index');
        }

        return $this->render('admin/campus/new.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'campus_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_campus_index');
        }

        return $this->render('admin/campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'campus_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Campus $campus): Response
    {
        return $this->render('admin/campus/delete.html.twig', [
            'campus' => $campus,
        ]);
    }

    #[Route('/confirm_delete/{id}', name: 'campus_confirm_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function confirmDelete(Request $request, Campus $campus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_campus_index');
    }
}