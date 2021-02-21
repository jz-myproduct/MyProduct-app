<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\RegisterCompanyFormType;
use App\Services\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SecurityController extends AbstractController
{

    /**
     * @Route("/zaregistrovat", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param SlugService $slugService
     * @return Response
     * @throws \Exception
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                             SlugService $slugService): Response
    {

        if ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('back-office-home',[
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();

        $company = new Company();
        $form = $this->createForm(RegisterCompanyFormType::class, $company);
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

            $this->loginAfterRegistration($company, $password);

            return $this->redirectToRoute('back-office-home',[
                'slug' => $company->getSlug()
            ]);
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
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('back-office-home',[
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('front-office/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    private function loginAfterRegistration(Company $company, $password)
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

    /**
     * @Route("/admin", name="after-login-route")
     * @return Response
     */
    public function redirectToAdmin(): Response
    {
        if(! $this->isGranted('ROLE_USER') ){
            return $this->redirectToRoute('login');
        }
        $company = $this->getDoctrine()->getRepository(Company::class)->getCompanyByEmail(
            $this->getUser()->getUsername());

        return $this->redirectToRoute('back-office-home', [
            'slug' => $company->getSlug()
        ]);
    }


}
