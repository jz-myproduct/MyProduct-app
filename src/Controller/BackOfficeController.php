<?php

namespace App\Controller;

use App\Entity\Company;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeController extends AbstractController
{

    /**
     * @Route("/admin/{slug}", name="back-office-home")
     * @param Company $company
     * @return Response
     */
    public function index(Company $company): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);
        dump($company);

        return $this->render('back_office/home.html.twig', [
            'controller_name' => 'BackOfficeController',
        ]);
    }

    /**
     * @Route("/admin", name="after-login-route")
     * @return Response
     */
    public function redirectToAdmin(): Response
    {
        $company = $this->getUser();
        if(is_null($company)){
            return $this->redirectToRoute('login');
        }

        $this->denyAccessUnlessGranted('edit', $company);

        return $this->redirectToRoute('back-office-home',[
            'slug' => $company->getSlug()
        ]);
    }

}
