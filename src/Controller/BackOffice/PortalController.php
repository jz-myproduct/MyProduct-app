<?php


namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\Portal;
use App\Form\PortalFormType;
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

    public function __construct(EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
    }

    /**
     * @Route("/admin/{slug}/portal", name="backoffice-portal")
     * @param Company $company
     * @param Request $request
     * @return Response
     */
    public function edit(Company $company, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $portal = $company->getPortal();
        $form = $this->createForm(PortalFormType::class, $portal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $name = $form->get('name')->getData();
            $portal->setName($name);
            $portal->setSlug(
                $this->slugService->createPortalSlug($name, $portal)
            );
            $portal->setDisplay(
                $form->get('display')->getData()
            );

            $this->manager->flush();
        }

        return $this->render('backoffice/portal.html.twig', [
           'companySlug' => $company->getSlug(),
           'form' => $form->createView(),
           'portal' => $portal
        ]);
    }

}