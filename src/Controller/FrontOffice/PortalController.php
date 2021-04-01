<?php


namespace App\Controller\FrontOffice;

use App\Entity\InsightWeight;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use App\Form\Insight\AddFromFeatureType;
use App\Form\Feedback\AddEditType;
use App\FormRequest\Feedback\AddEditRequest;
use App\FormRequest\Insight\AddFromFeatureRequest;
use App\Handler\Feedback\Add;
use App\View\Shared\PortalDetail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PortalController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/portal/{slug}/{state?}", name="fo_portal_detail")
     * @ParamConverter("portal", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("state", options={"mapping": {"state": "slug"}})
     * @param Portal $portal
     * @param PortalFeatureState|null $state
     * @param PortalDetail $view
     * @return Response|NotFoundHttpException
     */
    public function detail(Portal $portal, ?PortalFeatureState $state, PortalDetail $view)
    {

        $this->isAllowToBeDisplayed($portal);

        return $this->render('front_office/portal/detail.html.twig',
            $view->create($portal->getCompany(), $state)
        );
    }

    /**
     * @Route("/portal/{portal_slug}/feature/{feature_id}", name="fo_portal_insight_add")
     * @ParamConverter("portal", options={"mapping": {"portal_slug": "slug"}})
     * @ParamConverter("portalFeature", options={"mapping": {"feature_id": "id"}})
     * @param Portal $portal
     * @param PortalFeature $portalFeature
     * @param Request $request
     * @param \App\Handler\Insight\Add $handler
     * @return Response
     */
    public function addInsight(
        Portal $portal,
        PortalFeature $portalFeature,
        Request $request,
        \App\Handler\Insight\Add $handler)
    {
        if(! $this->isFeatureAllowToBeDisplayed($portalFeature, $portal))
        {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(AddFromFeatureType::class, $formRequest = new AddFromFeatureRequest(), [
            'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->addFromPortal($formRequest, $portalFeature);

            $this->addFlash('success', 'Thank you for the feedback!');

            return $this->redirectToRoute('fo_portal_detail', [
                'slug' => $portal->getSlug(),
            ]);
        }


        return $this->render('front_office/portal/feedback_insight.html.twig', [
            'form' => $form->createView(),
            'portal' => $portal,
            'feature' => $portalFeature
        ]);
    }

    /**
     * @Route("/portal/{slug}/feedback/add", name="fo_portal_feedback_add")
     * @param Portal $portal
     * @param Request $request
     * @param Add $handler
     * @return RedirectResponse|Response
     */
    public function addFeedback(Portal $portal, Request $request, Add $handler)
    {
        $this->isAllowToBeDisplayed($portal);

        $form = $this->createForm(AddEditType::class, $formRequest = new AddEditRequest());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->addFromPortal($formRequest, $portal->getCompany());

            $this->addFlash('success', 'Thank you for the feedback!');

            return $this->redirectToRoute('fo_portal_detail', [
                'slug' => $portal->getSlug()
            ]);
        }

        return $this->render('front_office/portal/feedback_insight.html.twig', [
            'portal' => $portal,
            'form' => $form->createView()
        ]);
    }

    private function isAllowToBeDisplayed(Portal $portal)
    {

        if (!$portal->getDisplay())
        {
            throw new NotFoundHttpException();
        }

        if(! $this->manager->getRepository(PortalFeatureState::class)->findAll())
        {
            throw new NotFoundHttpException();
        }

    }

    private function isFeatureAllowToBeDisplayed(PortalFeature $portalFeature, Portal $portal)
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

        if(! $this->manager->getRepository(PortalFeatureState::class)->findAll())
        {
            return false;
        }

        return true;
    }
}