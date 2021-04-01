<?php


namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\PortalFeatureState;
use App\Form\Portal\SettingsFormType;
use App\FormRequest\Portal\SettingsRequest;
use App\Handler\Portal\Edit;
use App\Service\SlugService;
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

        /*
         * In case there is no state, portal cannot be displayed
         */
        if(! $this->manager->getRepository(PortalFeatureState::class)->findAll())
        {
            return $this->render('back_office/portal/denied.html.twig');
        }

        $portal = $company->getPortal();

        $form = $this->createForm(SettingsFormType::class, $formRequest = SettingsRequest::fromPortal($portal));
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $handler->handle($formRequest, $portal);

            $this->addFlash('success', 'Portal upraven.');
        }

        return $this->render(
            'back_office/portal/detail.html.twig',
            $view->create(
                $company,
                $state,
                $form->createView()
            ));
    }

}