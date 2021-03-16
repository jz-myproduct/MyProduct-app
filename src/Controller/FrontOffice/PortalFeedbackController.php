<?php


namespace App\Controller\FrontOffice;


use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Form\AddFromFeatureType;
use App\Form\Portal\AddFeedbackType;
use App\Handler\Feedback\AddFromPortal;
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
     * @param \App\Handler\Insight\AddFromPortal $handler
     * @return Response
     */
    public function detail(
        Portal $portal,
        PortalFeature $portalFeature,
        Request $request,
        \App\Handler\Insight\AddFromPortal $handler)
    {
        if(! $this->isAllowToBeDisplayed($portalFeature, $portal)){
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(AddFromFeatureType::class, $insight = new Insight());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($insight, $portalFeature);

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
     * @param AddFromPortal $handler
     * @return RedirectResponse|Response
     */
    public function addFeedback(Portal $portal, Request $request, AddFromPortal $handler)
    {
        if (!$portal->getDisplay()) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(AddFeedbackType::class, $feedback = new Feedback());
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

    private function isAllowToBeDisplayed(PortalFeature $portalFeature, Portal $portal)
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