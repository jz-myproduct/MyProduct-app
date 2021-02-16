<?php

namespace App\Controller;

use App\Services\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\Company;
use App\Form\RegisterCompanyType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontOfficeController extends AbstractController
{
    /* TEMPORARY */
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('front-office/home.html.twig');
    }

    /**
     * @Route("/zaregistrovat", name="register")
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
            $password = $passwordEncoder->encodePassword($company, $form->get('password')->getData());

            $company->setPassword( $password );
            $company->setEmail( $form->get('email')->getData() );
            $company->setName( $form->get('name')->getData() );
            $company->setSlug(
                $slugService->createCompanySlug($form->get('name')->getData())
            );

            $currentDateTime = new \DateTime();
            $company->setCreatedAt($currentDateTime);
            $company->setUpdatedAt($currentDateTime);
            $company->setRoles( $company->getRoles() );

            $entityManager->persist($company);
            $entityManager->flush();

            $this->loginCompanyAfterRegistration($company, $password);

            return $this->redirectToRoute('home');
        }


        return $this->render('front-office/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/prihlasit", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginCompany(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front-office/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    private function loginCompanyAfterRegistration(Company $company, $password)
    {
        $token = new UsernamePasswordToken(
            $company,
            $password,
            'main',
            $company->getRoles()
        );

        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main',serialize($token));
    }
}
