<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Portal;
use App\Form\RegisterCompanyFormType;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

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

        $company = new Company();
        $form = $this->createForm(RegisterCompanyFormType::class, $company);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /* COMPANY */
            $password = $passwordEncoder->encodePassword($company, $form->get('password')->getData());
            $name = $form->get('name')->getData();

            $company->setPassword( $password );
            $company->setEmail( $form->get('email')->getData() );
            $company->setName($name);
            $company->setSlug(
                $slugService->createCompanySlug($name)
            );

            $currentDateTime = new \DateTime();
            $company->setCreatedAt($currentDateTime);
            $company->setUpdatedAt($currentDateTime);
            $company->setRoles( $company->getRoles() );

            $this->manager->persist($company);

            /* PORTAL */
            $portal = new Portal();
            $portal->setName($name);
            $portal->setSlug(
                $slugService->createInitialPortalSlug($name)
            );
            $portal->setDisplay(false);
            $portal->setCreatedAt($currentDateTime);
            $portal->setUpdatedAt($currentDateTime);
            $company->setPortal($portal);

            $this->manager->persist($portal);
            $this->manager->flush();

            $this->loginAfterRegistration($company, $password);

            return $this->redirectToRoute('back-office-home',[
                'slug' => $company->getSlug()
            ]);
        }


        return $this->render('frontoffice/registration.html.twig', [
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

        return $this->render('frontoffice/login.html.twig', [
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
