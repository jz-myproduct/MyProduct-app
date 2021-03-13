<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Portal;
use App\Form\PasswordChangeType;
use App\Form\RegisterCompanyFormType;
use App\Form\SettingsInfoType;
use App\Handler\Company\Add;
use App\Handler\Company\Edit;
use App\Security\LoginFormAuthenticator;
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
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SecurityController extends AbstractController
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
     * @var GuardAuthenticatorHandler
     */
    private $guardHandler;
    /**
     * @var LoginFormAuthenticator
     */
    private $loginFormAuthenticator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;


    public function __construct(
        EntityManagerInterface $manager,
        SlugService $slugService,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
        $this->guardHandler = $guardHandler;
        $this->loginFormAuthenticator = $loginFormAuthenticator;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/zaregistrovat", name="fo_register")
     * @param Request $request
     * @param Add $handler
     * @return Response
     */
    public function register(Request $request, Add $handler): Response
    {
        if ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('back-office-home',[
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        $form = $this->createForm(RegisterCompanyFormType::class, $company = new Company());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $company = $handler->handle($company);

            return $this->guardHandler->authenticateUserAndHandleSuccess(
                   $company,
                   $request,
                   $this->loginFormAuthenticator,
                    'main' // firewall name in security.yaml
            );
        }

        return $this->render('front_office/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/prihlasit", name="fo_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('bo_home',[
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        return $this->render('front_office/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/admin", name="fo_after_login")
     * @return Response
     */
    public function redirectToAdmin(): Response
    {
        if(! $this->isGranted('ROLE_USER') ){
            return $this->redirectToRoute('fo_login');
        }

        return $this->redirectToRoute('bo_home', [
            'slug' => $this->getUser()->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{slug}/nastaveni/zmenit-heslo", name="bo_settings_password")
     * @param Company $company
     * @param Request $request
     * @return Response
     */
    public function changePassowrd(Company $company, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(PasswordChangeType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(! $this->passwordEncoder->isPasswordValid(
                $company,
                $form->get('oldPassword')->getData()))
            {
                $this->addFlash('error', 'Zadejte správné současné heslo.');

            } else {

                $company->setPassword(
                    $this->passwordEncoder->encodePassword(
                        $company,
                        $form->get('password')->getData()
                    )
                );

                $this->manager->flush();

                $this->addFlash('success', 'Heslo úspěšně změněno');
            }
        }

        return $this->render('back_office/company/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }


}
