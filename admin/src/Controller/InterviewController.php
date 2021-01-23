<?php

namespace App\Controller;

use App\Entity\Interview;
use App\Form\InterviewType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/interview")
 */
class InterviewController extends AbstractController
{
    /**
     * @Route("/", name="interview_index", methods={"GET"})
     */
    public function index(): Response
    {
        $interviews = $this->getDoctrine()
            ->getRepository(Interview::class)
            ->findAll();

        return $this->render('interview/index.html.twig', [
            'interviews' => $interviews,
        ]);
    }

    /**
     * @Route("/new", name="interview_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $interview = new Interview();
        $form = $this->createForm(InterviewType::class, $interview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($interview);
            $entityManager->flush();

            return $this->redirectToRoute('interview_index');
        }

        return $this->render('interview/new.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="interview_show", methods={"GET"})
     */
    public function show(Interview $interview): Response
    {
        return $this->render('interview/show.html.twig', [
            'interview' => $interview,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="interview_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Interview $interview): Response
    {
        $form = $this->createForm(InterviewType::class, $interview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('interview_index');
        }

        return $this->render('interview/edit.html.twig', [
            'interview' => $interview,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="interview_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Interview $interview): Response
    {
        if ($this->isCsrfTokenValid('delete'.$interview->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($interview);
            $entityManager->flush();
        }

        return $this->redirectToRoute('interview_index');
    }
}
