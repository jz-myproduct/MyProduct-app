<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;

use App\Entity\Feedback;
use App\Entity\Portal;
use App\Form\FeedbackFormType;
use App\Form\SettingsInfoType;
use App\Handler\Company\Edit;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
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
     * @Route("/admin/{slug}", name="bo_home")
     * @param Company $company
     * @return Response
     */
    public function index(Company $company): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);


        return $this->render('back_office/home.html.twig');
    }

    /**
     * @Route("/admin/{slug}/nastaveni/info", name="bo_settings_info")
     * @param Company $company
     * @param Request $request
     * @param Edit $handler
     * @return Response
     */
    public function settings(Company $company, Request $request, Edit $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(SettingsInfoType::class, $company);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($company);

            $this->addFlash('success', 'Nastavení aktualizováno.');
        }

        return $this->render('back_office/company/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
