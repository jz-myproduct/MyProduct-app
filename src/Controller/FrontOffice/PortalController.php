<?php


namespace App\Controller\FrontOffice;


use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use App\Form\AddFeedbackType;
use App\Form\AddFromFeatureType;
use App\Form\Feedback\AddEditType;
use App\FormRequest\Feedback\AddEditRequest;
use App\FormRequest\Insight\AddFromFeatureRequest;
use App\Handler\Feedback\AddFeatureFeedbackOnPortal;
use App\Handler\Feedback\AddFromPortal;
use App\Service\PortalFeatureService;
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
        if(! $portal->getDisplay()){
            throw new NotFoundHttpException();
        }

        return $this->render('front_office/portal/detail.html.twig',
            $view->create($portal->getCompany(), $state)
        );
    }

    /**
     * @Route("/portal/{portal_slug}/feature/{feature_id}", name="fo_portal_feature_detail")
     * @ParamConverter("portal", options={"mapping": {"portal_slug": "slug"}})
     * @ParamConverter("portalFeature", options={"mapping": {"feature_id": "id"}})
     * @param Portal $portal
     * @param PortalFeature $portalFeature
     * @param Request $request
     * @param \App\Handler\Insight\AddFromPortal $handler
     * @return Response
     */
    public function featureDetail(
        Portal $portal,
        PortalFeature $portalFeature,
        Request $request,
        \App\Handler\Insight\AddFromPortal $handler)
    {
        if(! $this->isAllowToBeDisplayed($portalFeature, $portal)){
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(AddFromFeatureType::class, $formRequest = new AddFromFeatureRequest(), [
            'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($formRequest, $portalFeature);

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
     * @Route("/portal/{slug}/feedback/pridat", name="fo_portal_feedback_add")
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

        $form = $this->createForm(AddEditType::class, $formRequest = new AddEditRequest());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handleGeneral($formRequest, $portal->getCompany());

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