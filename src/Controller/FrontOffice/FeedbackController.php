<?php


namespace App\Controller\FrontOffice;


use App\Entity\Feedback;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Form\PortalGeneralFeedbackFormType;
use App\Handler\Feedback\AddFeatureFeedbackOnPortal;
use App\Handler\Feedback\AddGeneralOnPortal;
use App\Services\PortalFeatureService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/portal/{slug}/pridat-feedback", name="fo_portal_general_feedback_add")
     * @param Portal $portal
     * @param Request $request
     * @param AddGeneralOnPortal $handler
     * @return RedirectResponse|Response
     */
    public function addGeneralFeedback(Portal $portal, Request $request, AddGeneralOnPortal $handler)
    {
        if (!$portal->getDisplay()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(PortalGeneralFeedbackFormType::class, $feedback = new Feedback());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $portal->getCompany());

            return $this->redirectToRoute('front-office-portal', [
                'slug' => $portal->getSlug()
            ]);
        }

        return $this->render('front_office/add_general.twig', [
            'portalName' => $portal->getName(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/portal/{portal_slug}/feature/{feature_id}/pridat-feedback", name="front_office_portal_add-feature_feedback")
     * @ParamConverter("portal", options={"mapping": {"portal_slug": "slug"}})
     * @ParamConverter("portalFeature", options={"mapping": {"feature_id": "id"}})
     * @param Portal $portal
     * @param PortalFeature $portalFeature
     * @param Request $request
     * @param AddFeatureFeedbackOnPortal $handler
     * @param PortalFeatureService $portalFeatureService
     * @return Response
     */
    public function addFeatureFeedback(
        Portal $portal,
        PortalFeature $portalFeature,
        Request $request,
        AddFeatureFeedbackOnPortal $handler,
        PortalFeatureService $portalFeatureService)
    {
        if(! $portalFeatureService->isAllowToBeDisplayed($portalFeature, $portal)){
            throw new NotFoundHttpException();

        }

        $form = $this->createForm(PortalGeneralFeedbackFormType::class, $feedback = new Feedback());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $portalFeature, $portal->getCompany());

            return $this->redirectToRoute('front-office-portal', [
                'slug' => $portal->getSlug(),
            ]);
        }


        return $this->render('front_office/add_feature.html.twig', [
            'form' => $form->createView(),
            'portalName' => $portal->getName(),
            'featureName' => $portalFeature->getName()
        ]);
    }

}