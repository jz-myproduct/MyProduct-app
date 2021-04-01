<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Form\Settings\InfoType;
use App\FormRequest\Settings\InfoRequest;
use App\Handler\Settings\EditCompany;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;


class DefaultController extends AbstractController
{

    /**
     * @Route("/admin/{slug}/nastaveni/info", name="bo_settings_info")
     * @param Company $company
     * @param Request $request
     * @param EditCompany $handler
     * @return Response
     */
    public function settings(Company $company, Request $request, EditCompany $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(InfoType::class, $formRequest = InfoRequest::fromCompany($company));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $updatedCompany = $handler->handle($formRequest, $company);

            $this->addFlash('success', 'Nastavení aktualizováno.');

            return $this->redirectToRoute('bo_settings_info', [
                'slug' => $updatedCompany->getSlug()
            ]);
        }

        return $this->render('back_office/settings/settings.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
