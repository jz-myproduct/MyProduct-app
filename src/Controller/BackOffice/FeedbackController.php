<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Form\FeedbackFormType;
use App\Handler\Feedback\Add;
use App\Handler\Feedback\AddRelation;
use App\Handler\Feedback\Delete;
use App\Handler\Feedback\DeleteRelation;
use App\Handler\Feedback\DeleteRelationRedirect;
use App\Handler\Feedback\Edit;
use App\Handler\Feedback\SwitchStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FeedbackController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    private $manager;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $manager)
    {
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/{slug}/feedback/pridat", name="bo_feedback_add")
     * @param Company $company
     * @param Request $request
     * @param Add $handler
     * @return Response
     */
    public function add(Company $company, Request $request, Add $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(FeedbackFormType::class, $feedback = new Feedback(), [
           'featureChoices' => $company->getFeatures()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $company);

            $this->addFlash('success', 'Feedback přidán.');

            return $this->redirectToRoute('bo_feedback_list', [
                'slug' => $company->getSlug()
            ]);
        }

        return $this->render('back_office/feedback/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/upravit", name="bo_feedback_edit")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Request $request
     * @param Edit $handler
     * @return Response
     */
    public function edit(Company $company, Feedback $feedback, Request $request, Edit $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $form = $this->createForm(FeedbackFormType::class, $feedback, [
            'featureChoices' => $company->getFeatures()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback);

            $this->addFlash('success', 'Feedback upraven.');

            return $this->redirectToRoute('bo_feedback_detail', [
               'company_slug' => $company->getSlug(),
               'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->render('back_office/feedback/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/feedbacks", name="bo_feedback_list")
     * @param Company $company
     * @return Response
     */
    public function list(Company $company)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('back_office/feedback/list.twig', [
            'feedbacks' => $this->manager->getRepository(Feedback::class)
                ->findBy(['company' => $company], ['isNew' => 'DESC'])
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/smazat", name="bo_feedback_delete")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Delete $handler
     * @return RedirectResponse
     */
    public function delete(Company $company, Feedback $feedback, Delete $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $handler->delete($feedback);

        $this->addFlash('success', 'Feedback smazán.');

        return $this->redirectToRoute('bo_feedback_list', [
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/zmenit-status", name="bo_feedback_change_status")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param SwitchStatus $handler
     * @return RedirectResponse
     */
    public function switchStatus(Company $company, Feedback $feedback, SwitchStatus $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $handler->handle($feedback);

        $this->addFlash('success', 'Status upraven.');

        return $this->redirectToRoute('bo_feedback_list', [
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/detail", name="bo_feedback_detail")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @return Response
     */
    public function detail(Company $company, Feedback $feedback)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        return $this->render('back_office/feedback/detail.html.twig', [
            'feedback' => $feedback
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/features", name="bo_feedback_features")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @return Response
     */
    public function features(Company $company, Feedback $feedback)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $unrelatedFeatures = $this->getDoctrine()->getRepository(Feedback::class)
            ->getUnUsedFeaturesForFeedback($feedback, $company);

        return $this->render('back_office/feedback/features.html.twig', [
            'feedback' => $feedback,
            'relatedFeatures' => $feedback->getFeature(),
            'unrelatedFeatures' => $unrelatedFeatures
        ]);
    }


    /**
     * @Route("/admin/{company_slug}/pridat-propojeni/{feedback_id}/{feature_id}", name="bo_feedback_relation_add")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Feature $feature
     * @param AddRelation $handler
     * @return RedirectResponse
     */
    public function addFeedbackFeatureRelation(
        Company $company,
        Feedback $feedback,
        Feature $feature,
        AddRelation $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);
        $this->denyAccessUnlessGranted('edit', $feedback);

        // feedback already connected to feature
        if (in_array($feature, $feedback->getFeature()->toArray())) {

            $this->addFlash('info', 'Featura je již přidána.');

            return $this->redirectToRoute('bo_feedback_features',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);

        }

        $handler->handle($feedback, $feature);

        $this->addFlash('success', 'Featura připojena.');

        return $this->redirectToRoute('bo_feedback_features',[
            'company_slug' => $company->getSlug(),
            'feedback_id' => $feedback->getId()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/smazat-propojeni/{feedback_id}/{feature_id}", name="bo_feedback_relation_delete")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Feature $feature
     * @param Request $request
     * @param DeleteRelation $deleteHandler
     * @param DeleteRelationRedirect $redirectHandler
     * @return RedirectResponse
     */
    public function deleteFeedbackFeatureRelation(
        Company $company,
        Feedback $feedback,
        Feature $feature,
        Request $request,
        DeleteRelation $deleteHandler,
        DeleteRelationRedirect $redirectHandler)
    {

        $this->denyAccessUnlessGranted('edit', $feature);
        $this->denyAccessUnlessGranted('edit', $feedback);

        if (!in_array($feature, $feedback->getFeature()->toArray())) {
            throw new NotFoundHttpException();
        }

        $deleteHandler->handle($feedback, $feature);

        $this->addFlash('success', 'Spojení odebráno.');

        return new RedirectResponse(
            $redirectHandler->handle(
                $request->query->get('p'),
                $feedback,
                $feature,
                $company
            )
        );
    }
}
