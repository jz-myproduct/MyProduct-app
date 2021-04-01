<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\Settings\ChangePasswordType;
use App\Form\Security\RegisterCompanyFormType;
use App\Form\Security\RenewPasswordType;
use App\Form\Security\SetNewPasswordType;
use App\Form\Settings\DeleteCompanyType;
use App\FormRequest\Settings\ChangePasswordRequest;
use App\FormRequest\Security\RenewPasswordRequest;
use App\FormRequest\Security\RegisterCompanyRequest;
use App\FormRequest\Security\SetNewPasswordRequest;
use App\FormRequest\Settings\DeleteCompanyRequest;
use App\Handler\Security\RegisterCompany;
use App\Handler\Security\RenewPassword;
use App\Handler\Settings\ChangePassword;
use App\Handler\Security\SetForgottenPassword;
use App\Handler\Settings\DeleteCompany;
use App\Security\LoginFormAuthenticator;
use App\Service\SlugService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

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
     * @param RegisterCompany $handler
     * @return Response
     */
    public function register(Request $request, RegisterCompany $handler): Response
    {
        if ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('bo_feedback_list',[
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        $form = $this->createForm(RegisterCompanyFormType::class, $formRequest = new RegisterCompanyRequest());
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
            return $this->redirectToRoute('bo_feedback_list',[
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

        return $this->redirectToRoute('bo_feedback_list', [
            'slug' => $this->getUser()->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{slug}/nastaveni/zmenit-heslo", name="bo_settings_password")
     * @param Company $company
     * @param Request $request
     * @param ChangePassword $handler
     * @return Response
     */
    public function changePassowrd(Company $company, Request $request, ChangePassword $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(ChangePasswordType::class, $formRequest = new ChangePasswordRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(! $this->passwordEncoder->isPasswordValid(
                $company,
                $formRequest->password))
            {
                $this->addFlash('error', 'Wrong current password.');

            } else {

                $handler->handle(
                    $company,
                    $formRequest->newPassword
                );

                $this->addFlash('success', 'Password successfully changed.');
            }
        }

        return $this->render('back_office/settings/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/zapomenute-heslo", name="fo_renew_password")
     * @param Request $request
     * @param RenewPassword $handler
     * @return RedirectResponse|Response
     */
    public function renewPassword(Request $request, RenewPassword $handler)
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('bo_feedback_list', [
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        $form = $this->createForm(RenewPasswordType::class, $formRequest = new RenewPasswordRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($handler->handle(
                    $this->manager->getRepository(Company::class)
                    ->findOneBy(['email' => $formRequest->email]
                    )
                )
            ){
                $this->addFlash('success', 'Follow instructions in your email inbox.');

                return $this->redirectToRoute('fo_renew_password');
            }

            $this->addFlash('error', 'Email cannot be sent, try it later please.');
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
     * @param SetForgottenPassword $handler
     * @return Response
     * @throws \Exception
     */
    public function setNewPassword(Company $company, Request $request, SetForgottenPassword $handler)
    {
        if($this->isGranted('ROLE_USER')){
            return $this->redirectToRoute('bo_feedback_list', [
                'slug' => $this->getUser()->getSlug()
            ]);
        }

        if($company->getPasswordHashValidUntil() < new \DateTime()){

            return $this->render('front_office/password/expired_hash.html.twig');
        }

        $form = $this->createForm(SetNewPasswordType::class, $formRequest = new SetNewPasswordRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle(
                $company,
                $formRequest->password
            );

            $this->addFlash('success', 'Password successfully changed.');

            return $this->redirectToRoute('fo_login');
        }

        return $this->render('front_office/password/set_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/nastaveni/smazat-firmu", name="bo_settings_delete")
     * @param Company $company
     * @param Request $request
     * @param DeleteCompany $handler
     * @return Response
     */
    public function delete(Company $company, Request $request, DeleteCompany $handler)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(DeleteCompanyType::class, $formRequest = new DeleteCompanyRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(! $this->passwordEncoder->isPasswordValid(
                $company,
                $formRequest->password))
            {
                $this->addFlash('error', 'Wrong current password.');

            } else {
                $handler->handle($company);

                $this->addFlash('success','Company deleted.');
                return $this->redirectToRoute('fo_home');
            }
        }

        return $this->render('back_office/settings/delete_company.html.twig',[
           'form'=> $form->createView()
        ]);
    }

}
