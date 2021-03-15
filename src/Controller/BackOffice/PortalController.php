<?php


namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureState;
use App\Entity\Portal;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use App\Form\PortalFormType;
use App\Handler\Portal\Edit;
use App\Services\SlugService;
use App\View\Shared\PortalDetail;
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

    public function __construct(
        EntityManagerInterface $manager,
        SlugService $slugService)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
    }

    /**
     * @Route("/admin/{slug}/portal/{state?}", name="bo_portal_detail")
     * @ParamConverter("company", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("state", options={"mapping": {"state": "slug"}})
     * @param Company $company
     * @param Request $request
     * @param Edit $handler
     * @param PortalFeatureState|null $state
     * @param PortalDetail $view
     * @return Response
     */
    public function detail(Company $company, Request $request, Edit $handler, ?PortalFeatureState $state, PortalDetail $view)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $portal = $company->getPortal();

        $form = $this->createForm(PortalFormType::class, $portal);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $handler->handle($portal);

            $this->addFlash('success', 'DetailView upraven.');
        }

        return $this->render(
            'back_office/portal/detail.html.twig',
            $view->create(
                $company,
                $form->createView(),
                $state
            ));
    }

}