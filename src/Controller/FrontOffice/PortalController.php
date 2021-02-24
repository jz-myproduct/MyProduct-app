<?php


namespace App\Controller\FrontOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Form\FeedbackFormType;
use App\Form\PortalGeneralFeedbackFormType;
use App\Services\PortalFeatureService;
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
    /**
     * @var PortalFeatureService
     */
    private $portalFeatureService;

    public function __construct(EntityManagerInterface $manager, PortalFeatureService $portalFeatureService)
    {
        $this->manager = $manager;
        $this->portalFeatureService = $portalFeatureService;
    }

    /**
     * @Route("/portal/{slug}", name="front-office-portal")
     * @param Portal $portal
     * @return Response|NotFoundHttpException
     */
    public function index(Portal $portal)
    {
        if(! $portal->getDisplay()){
            throw new NotFoundHttpException();
        }

        return $this->render('frontoffice/portal.html.twig', [
            'portal' => $portal,
            'featuresByState' => $this->portalFeatureService->getArray($portal->getCompany())
        ]);
    }

    /**
     * @Route("/portal/{slug}/pridat-feedback", name="front-office-portal-add-feedback")
     * @param Portal $portal
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function addGeneralFeedback(Portal $portal, Request $request)
    {
        if (!$portal->getDisplay()) {
            throw new NotFoundHttpException();
        }

        $feedback = new Feedback();
        $form = $this->createForm(PortalGeneralFeedbackFormType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback->setDescription(
                $form->get('description')->getData()
            );
            $feedback->setSource(
                $form->get('source')->getData()
            );
            $feedback->setCompany(
                $portal->getCompany()
            );
            $feedback->setIsNew(true);

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt($currentDateTime);
            $feedback->setUpdatedAt($currentDateTime);
            $feedback->setFromPortal(true);

            $this->manager->persist($feedback);
            $this->manager->flush();

            return $this->redirectToRoute('front-office-portal', [
                'slug' => $portal->getSlug()
            ]);
        }

        return $this->render('frontoffice/portalAddGeneralFeedback.html.twig', [
            'portalName' => $portal->getName(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/portal/{portal_slug}/feature/{feature_id}", name="front-office-portal-feature-detail")
     * @ParamConverter("portal", options={"mapping": {"portal_slug": "slug"}})
     * @ParamConverter("portalFeature", options={"mapping": {"feature_id": "id"}})
     * @param Portal $portal
     * @param PortalFeature $portalFeature
     * @return Response
     */
    public function featureDetail(Portal $portal, PortalFeature $portalFeature)
    {
        if(! $this->checkAccess($portal, $portalFeature) ){
            throw new NotFoundHttpException();
        }

        return $this->render('frontoffice/portalFeatureDetail.html.twig', [
           'portalName' => $portal->getName(),
           'feature' => $portalFeature
        ]);
    }

    /**
     * @Route("/portal/{portal_slug}/feature/{feature_id}/pridat-feedback", name="front-office-portal-add-feature-feedback")
     * @ParamConverter("portal", options={"mapping": {"portal_slug": "slug"}})
     * @ParamConverter("portalFeature", options={"mapping": {"feature_id": "id"}})
     * @param Portal $portal
     * @param PortalFeature $portalFeature
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function addFeatureFeedback(Portal $portal, PortalFeature $portalFeature, Request $request)
    {

        if(! $this->checkAccess($portal, $portalFeature) ){
            throw new NotFoundHttpException();
        }

        $feedback = new Feedback();
        $form = $this->createForm(PortalGeneralFeedbackFormType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback->setDescription(
                $form->get('description')->getData()
            );
            $feedback->setSource(
                $form->get('source')->getData()
            );
            $feedback->setCompany(
                $portal->getCompany()
            );
            $feedback->setIsNew(true);

            $currentDateTime = new \DateTime();
            $feedback->setCreatedAt($currentDateTime);
            $feedback->setUpdatedAt($currentDateTime);
            $feedback->setFromPortal(true);

            $feedback->addFeature(
                $portalFeature->getFeature()
            );
            $portalFeature->getFeature()->setScoreUpByOne();

            $this->manager->persist($feedback);
            $this->manager->flush();

            return $this->redirectToRoute('front-office-portal', [
                'slug' => $portal->getSlug(),
            ]);
        }


        return $this->render('frontoffice/portalAddFeatureFeedback.html.twig', [
            'form' => $form->createView(),
            'portalName' => $portal->getName(),
            'featureName' => $portalFeature->getName()
        ]);
    }

    private function checkAccess(Portal $portal, PortalFeature $portalFeature)
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