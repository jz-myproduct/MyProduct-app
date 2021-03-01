<?php


namespace App\Controller\FrontOffice;


use App\Entity\Feedback;
use App\Entity\Portal;
use App\Entity\PortalFeature;
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

    public function __construct(PortalFeatureService $portalFeatureService)
    {
        $this->portalFeatureService = $portalFeatureService;
    }

    /**
     * @Route("/portal/{slug}", name="fo_portal_detail")
     * @param Portal $portal
     * @return Response|NotFoundHttpException
     */
    public function detail(Portal $portal)
    {
        if(! $portal->getDisplay()){
            throw new NotFoundHttpException();
        }

        return $this->render('front_office/portal/detail.html.twig', [
            'portal' => $portal,
            'featuresByState' => $this->portalFeatureService->getArray($portal->getCompany())
        ]);
    }

}