<?php


namespace App\Controller\FrontOffice;


use App\Entity\Feedback;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use App\Form\PortalFeedbackFormType;
use App\Handler\Feedback\AddFeatureFeedbackOnPortal;
use App\Handler\Feedback\AddGeneralOnPortal;
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
     * @var PortalFeatureService
     */
    private $portalFeatureService;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(PortalFeatureService $portalFeatureService, EntityManagerInterface $manager)
    {
        $this->portalFeatureService = $portalFeatureService;
        $this->manager = $manager;
    }

    /**
     * @Route("/portal/{slug}/{state?}", name="fo_portal_detail")
     * @ParamConverter("portal", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("state", options={"mapping": {"state": "slug"}})
     * @param Portal $portal
     * @param PortalFeatureState|null $state
     * @return Response|NotFoundHttpException
     */
    public function detail(Portal $portal, ?PortalFeatureState $state)
    {
        if(! $portal->getDisplay()){
            throw new NotFoundHttpException();
        }

        $state = $state ?? $this->manager->getRepository(PortalFeatureState::class)->findInitialState();

        $states = $this->manager->getRepository(PortalFeatureState::class)->findAll();

        $features = $this->manager->getRepository(PortalFeature::class)
            ->findFeaturesForPortalByState($portal->getCompany(), $state);


        return $this->render('front_office/portal/detail.html.twig', [
            'portal' => $portal,
            'states' => $states,
            'currentState' => $state,
            'portalFeatures' => $features
        ]);
    }

}