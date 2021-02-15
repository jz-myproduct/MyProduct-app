<?php

namespace App\Controller;

use App\Services\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Company;
use App\Form\RegisterCompanyType;

class FrontOfficeController extends AbstractController
{
    /**
     * @Route("/zaregistrovat", name="registerCompany")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SlugService $slugService
     * @return Response
     * @throws \Exception
     */
    public function registerCompany(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                                    SlugService $slugService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $company = new Company();
        $form = $this->createForm(RegisterCompanyType::class, $company);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $company->setPassword(
                $passwordEncoder->encodePassword($company, $form->get('password')->getData())
            );
            $company->setEmail( $form->get('email')->getData() );
            $company->setName( $form->get('name')->getData() );
            $company->setSlug(
                $slugService->createSlug($form->get('name')->getData())
            );

            $currentDateTime = new \DateTime();
            $company->setCreatedAt($currentDateTime);
            $company->setUpdatedAt($currentDateTime);

            $entityManager->persist($company);
            $entityManager->flush();
        }


        return $this->render('front-office/registration/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
