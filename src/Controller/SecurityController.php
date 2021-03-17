<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Portal;
use App\Form\Settings\ChangePasswordType;
use App\Form\Security\RegisterCompanyFormType;
use App\Form\Security\RenewPassword;
use App\Form\Security\SetNewPassword;
use App\FormRequest\Settings\ChangePasswordRequest;
use App\FormRequest\Security\RenewPasswordRequest;
use App\FormRequest\Security\RegisterRequest;
use App\FormRequest\Security\SetNewPasswordRequest;
use App\Handler\Company\Add;
use App\Handler\Company\Edit;
use App\Handler\Company\Password\Change;
use App\Handler\Company\Password\Renew;
use App\Handler\Company\Password\SetForgotten;
use App\Security\LoginFormAuthenticator;
use App\Services\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\DateTime;

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

        $form = $this->createForm(RegisterCompanyFormType::class, $formRequest = new RegisterRequest());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $company = $handler->handle($formRequest);

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
     * @param Change $handler
     * @return Response
     */
    public function changePassowrd(Company $company, Request $request, Change $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(ChangePasswordType::class, $formRequest = new ChangePasswordRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(! $this->passwordEncoder->isPasswordValid(
                $company,
                $formRequest->password))
            {
                $this->addFlash('error', 'Zadejte správné současné heslo.');

            } else {

                $handler->handle(
                    $company,
                    $formRequest->newPassword
                );

                $this->addFlash('success', 'Heslo úspěšně změněno');
            }
        }

        return $this->render('back_office/company/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/zapomenute-heslo", name="fo_renew_password")
     * @param Request $request
     * @param Renew $handler
     * @return RedirectResponse|Response
     */
    public function renewPassword(Request $request, Renew $handler)
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('bo_home', [
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        $form = $this->createForm(RenewPassword::class, $formRequest = new RenewPasswordRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($handler->handle(
                    $this->manager->getRepository(Company::class)
                    ->findOneBy(['email' => $formRequest->email]
                    )
                )
            ){
                $this->addFlash('success', 'Následujte instrukce ve svém emailu.');

                return $this->redirectToRoute('fo_renew_password');
            }

            $this->addFlash('error', 'Email se nepodařilo odeslat. Zkuste to později');
        }

        return $this->render('front_office/password/renew.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/nastavit-heslo/{hash}", name="fo_set_new_password")
     * @ParamConverter("company", options={"mapping": {"hash": "passwordRenewHash"}})
     * @param Company $company
     * @param Request $request
     * @param SetForgotten $handler
     * @return Response
     * @throws \Exception
     */
    public function setNewPassword(Company $company, Request $request, SetForgotten $handler)
    {
        if($company->getPasswordHashValidUntil() < new \DateTime()){

            return $this->render('front_office/password/expired_hash.html.twig');
        }

        $form = $this->createForm(SetNewPassword::class, $formRequest = new SetNewPasswordRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle(
                $company,
                $formRequest->password
            );

            $this->addFlash('success', 'Heslo bylo úspěšně změněno.');

            return $this->redirectToRoute('fo_login');
        }

        return $this->render('front_office/password/set_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
