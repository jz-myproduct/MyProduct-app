<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Portal;
use App\Form\RegisterCompanyFormType;
use App\Handler\Company\Add;
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


    public function __construct(
        EntityManagerInterface $manager,
        SlugService $slugService,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $loginFormAuthenticator)
    {
        $this->manager = $manager;
        $this->slugService = $slugService;
        $this->guardHandler = $guardHandler;
        $this->loginFormAuthenticator = $loginFormAuthenticator;
    }

    /**
     * @Route("/zaregistrovat", name="register")
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

        return $this->render('frontoffice/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
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

        return $this->redirectToRoute('back-office-home', [
            'slug' => $this->getUser()->getSlug()
        ]);
    }


}
