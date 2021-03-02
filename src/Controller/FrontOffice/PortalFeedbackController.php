<?php


namespace App\Controller\FrontOffice;


use App\Entity\Feedback;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Form\PortalFeedbackFormType;
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

class PortalFeedbackController extends AbstractController
{

    /**
     * @Route("/portal/{portal_slug}/feature/{feature_id}", name="fo_portal_feedback_feature")
     * @ParamConverter("portal", options={"mapping": {"portal_slug": "slug"}})
     * @ParamConverter("portalFeature", options={"mapping": {"feature_id": "id"}})
     * @param Portal $portal
     * @param PortalFeature $portalFeature
     * @param Request $request
     * @param AddFeatureFeedbackOnPortal $handler
     * @return Response
     */
    public function detail(
        Portal $portal,
        PortalFeature $portalFeature,
        Request $request,
        AddFeatureFeedbackOnPortal $handler)
    {
        if(! $this->isAllowToBeDisplayed($portalFeature, $portal)){
            throw new NotFoundHttpException();

        }

        $form = $this->createForm(PortalFeedbackFormType::class, $feedback = new Feedback());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $portalFeature, $portal->getCompany());

            $this->addFlash('success', 'Děkujeme za feeedback!');

            return $this->redirectToRoute('fo_portal_detail', [
                'slug' => $portal->getSlug(),
            ]);
        }


        return $this->render('front_office/portal/feedback.html.twig', [
            'form' => $form->createView(),
            'portal' => $portal,
            'feature' => $portalFeature
        ]);
    }


    /**
     * @Route("/portal/{slug}/feedback/pridat", name="fo_portal_feedback_general")
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

        $form = $this->createForm(PortalFeedbackFormType::class, $feedback = new Feedback());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $portal->getCompany());

            $this->addFlash('success', 'Děkujeme za feeedback!');

            return $this->redirectToRoute('fo_portal_detail', [
                'slug' => $portal->getSlug()
            ]);
        }

        return $this->render('front_office/portal/feedback.html.twig', [
            'portal' => $portal,
            'form' => $form->createView()
        ]);
    }

    public function isAllowToBeDisplayed(PortalFeature $portalFeature, Portal $portal)
    {

        if(! $portal->getDisplay())
        {
            return false;
        }

        if(! $portalFeature->getDisplay())
        {
            return false;
        }

        if($portalFeature->getFeature()->getCompany() !== $portal->getCompany())
        {
            return false;
        }

        return true;
    }

}