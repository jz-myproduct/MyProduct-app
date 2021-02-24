<?php


namespace App\Controller\FrontOffice;


use App\Entity\Company;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Services\PortalFeatureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'portalName' => $portal->getName(),
            'featuresByState' => $this->portalFeatureService->getArray($portal->getCompany())
        ]);
    }
}