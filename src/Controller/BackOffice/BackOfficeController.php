<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BackOfficeController extends AbstractController
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/admin/{slug}", name="back-office-home")
     * @param Company $company
     * @return Response
     */
    public function index(Company $company): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('back_office/home.html.twig', [
            'companySlug' => $company->getSlug()
        ]);
    }

}
