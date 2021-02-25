<?php


namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use App\Form\PortalFormType;
use App\Handler\Portal\Edit;
use App\Services\PortalFeatureService;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @var SlugService
     */
    private $slugService;
    /**
     * @var PortalFeatureService
     */
    private $portalFeatureService;

    public function __construct(
        EntityManagerInterface $manager,
        SlugService $slugService,
        PortalFeatureService $portalFeatureService)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
        $this->portalFeatureService = $portalFeatureService;
    }

    /**
     * @Route("/admin/{slug}/portal", name="backoffice-portal")
     * @param Company $company
     * @param Request $request
     * @param Edit $handler
     * @return Response
     */
    public function edit(Company $company, Request $request, Edit $handler)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $portal = $company->getPortal();

        $form = $this->createForm(PortalFormType::class, $portal);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $handler->handle($portal);
        }

        return $this->render('backoffice/portal.html.twig', [
           'companySlug' => $company->getSlug(),
           'form' => $form->createView(),
           'portal' => $portal,
           'featuresByState' => $this->portalFeatureService->getArray($company)
        ]);
    }

}