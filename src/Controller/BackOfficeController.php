<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Form\FeatureFormType;
use App\Form\FeedbackType;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * @throws Exception
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
            $feedback->setNewStatus();

            $currentDateTime = new DateTime();
            $feedback->setCreatedAt( $currentDateTime );
            $feedback->setUpdatedAt( $currentDateTime );

            $entityManager->persist($feedback);
            $entityManager->flush();

            return $this->redirectToRoute('feedback-list',[
                'slug' => $company->getSlug()
            ]);

        }

        return $this->render('back_office/addEditFeedback.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/upravit", name="edit-feedback")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function editFeedback(Company $company, Feedback $feedback, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

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
            $feedback->setUpdatedAt( new DateTime() );

            $entityManager->flush();

            $this->addFlash('success', 'Feedback updated');
        }

        return $this->render('back_office/addEditFeedback.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/feedbacks", name="feedback-list")
     * @param Company $company
     * @return Response
     */
    public function feedbackList(Company $company)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('back_office/feedbackList.html.twig',[
            'feedbacks' => $company->getFeedbacks(),
            'companySlug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/smazat", name="delete-feedback")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @return RedirectResponse
     */
    public function feedbackDelete(Company $company, Feedback $feedback)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($feedback);
        $entityManager->flush();

        return $this->redirectToRoute('feedback-list',[
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/zmenit-status", name="change-status-feedback")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @return RedirectResponse
     */
    public function switchFeedbackStatus(Company $company, Feedback $feedback)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $entityManager = $this->getDoctrine()->getManager();

        $feedback->switchStatus();

        $entityManager->flush();


        return $this->redirectToRoute('feedback-list',[
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}", name="feedback-detail")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @return Response
     */
    public function feedbackDetail(Company $company, Feedback $feedback)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        return $this->render('back_office/feedbackDetail.html.twig',[
            'feedback' => $feedback,
            'companySlug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{slug}/feature/pridat", name="add-feature")
     * @param Company $company
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function addFeature(Company $company, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $entityManager = $this->getDoctrine()->getManager();

        $feature = new Feature();
        $form = $this->createForm(FeatureFormType::class, $feature);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $feature = new Feature();
            $feature->setName( $form->get('name')->getData() );
            $feature->setDescription( $form->get('description')->getData() );
            $feature->setCompany( $company );

            $currentDateTime = new \DateTime();
            $feature->setCreatedAt( $currentDateTime );
            $feature->setUpdatedAt( $currentDateTime );

            $entityManager->persist($feature);
            $entityManager->flush();

            return $this->redirectToRoute('feature-list',[
                'slug' => $company->getSlug()
            ]);

        }

        return $this->render('back_office/addEditFeature.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/upravit", name="edit-feature")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function editFeature(Company $company, Feature $feature, Request $request)
    {

        $this->denyAccessUnlessGranted('edit', $feature);

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(FeatureFormType::class, $feature);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

           $feature->setName(
               $form->get('name')->getData()
           );
           $feature->setDescription(
               $form->get('description')->getData()
           );
           $feature->setUpdatedAt( new \DateTime() );

           $entityManager->flush();

            $this->addFlash('success', 'Feature updated');
        }

        return $this->render('back_office/addEditFeature.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/features", name="feature-list")
     * @param Company $company
     * @return Response
     */
    public function featureList(Company $company)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('back_office/featureList.html.twig',[
            'features' => $company->getFeatures(),
            'companySlug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/smazat", name="delete-feature")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @return RedirectResponse
     */
    public function featureDelete(Company $company, Feature $feature)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($feature);
        $entityManager->flush();

        return $this->redirectToRoute('feature-list',[
            'slug' => $company->getSlug()
        ]);
    }




}
