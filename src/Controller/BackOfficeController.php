<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Feedback;
use App\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BackOfficeController extends AbstractController
{

    /**
     * @Route("/admin/{slug}", name="back-office-home")
     * @param Company $company
     * @return Response
     */
    public function index(Company $company): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);
        dump($company);

        return $this->render('back_office/home.html.twig', [
            'companySlug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin", name="after-login-route")
     * @return Response
     */
    public function redirectToAdmin(): Response
    {
        $company = $this->getUser();
        if(is_null($company)){
            return $this->redirectToRoute('login');
        }

        $this->denyAccessUnlessGranted('edit', $company);

        return $this->redirectToRoute('back-office-home',[
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{slug}/feedback/pridat", name="add-feedback")
     * @param Company $company
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addFeedback(Company $company, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $entityManager = $this->getDoctrine()->getManager();

        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $feedback->setDescription(
                $form->get('description')->getData()
            );
            $feedback->setSource(
                $form->get('source')->getData()
            );
            $feedback->setCompany( $company );

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt( $currentDateTime );
            $feedback->setUpdatedAt( $currentDateTime );

            $entityManager->persist($feedback);
            $entityManager->flush();

        }

        return $this->render('back_office/addFeedback.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/{company_slug}/feedback/upravit/{feedback_id}", name="edit-feedback")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Request $request
     * @return Response
     */
    public function editFeedback(Company $company, Feedback $feedback, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $feedback->setDescription(
                $form->get('description')->getData()
            );
            $feedback->setSource(
                $form->get('source')->getData()
            );
            $feedback->setUpdatedAt( new \DateTime() );

            $entityManager->flush();

            $this->addFlash('success', 'Feedback updated');
        }

        return $this->render('back_office/addFeedback.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }
}
