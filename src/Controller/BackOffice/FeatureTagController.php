<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\FeatureTag;
use App\Form\FeatureTag\AddEditType;
use App\FormRequest\FeatureTag\AddEditRequest;
use App\Handler\FeatureTag\Add;
use App\Handler\FeatureTag\Delete;
use App\Handler\FeatureTag\Edit;
use App\Service\SlugService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


class FeatureTagController extends AbstractController
{

    /**
     * @Route("/admin/{slug}/tag/pridat", name="bo_feature_tag_add")
     * @param Company $company
     * @param Request $request
     * @param Add $handler
     * @return Response
     */
    public function add(Company $company, Request $request, Add $handler)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(AddEditType::class,  $formRequest = new AddEditRequest());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($formRequest, $company);

            $this->addFlash('success', 'Tag added.');

            return $this->redirectToRoute('bo_feature_tag_list', [
                'slug' => $company->getSlug()
            ]);
        }

        return $this->render('back_office/feature_tag/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/tag/{tag_id}/upravit", name="bo_feature_tag_edit")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("tag", options={"mapping": {"tag_id": "id"}} )
     * @param Company $company
     * @param Request $request
     * @param FeatureTag $tag
     * @param Edit $handler
     * @return Response
     */
    public function edit(Company $company, Request $request, FeatureTag $tag, Edit $handler)
    {
        $this->denyAccessUnlessGranted('edit', $tag);

        $form = $this->createForm(AddEditType::class, $formRequest = AddEditRequest::fromFeatureTag($tag));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($formRequest, $tag);

            $this->addFlash('success', 'Tag edited.');
        }

        return $this->render('back_office/feature_tag/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/tags", name="bo_feature_tag_list")
     * @param Company $company
     * @return Response
     */
    public function list(Company $company)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('back_office/feature_tag/list.html.twig', [
            'tags' => $company->getFeatureTags()
        ]);

    }

    /**
     * @Route("/admin/{company_slug}/tag/{tag_id}/smazat", name="bo_feature_tag_delete")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("tag", options={"mapping": {"tag_id": "id"}} )
     * @param Company $company
     * @param FeatureTag $tag
     * @param Delete $handler
     * @return RedirectResponse
     */
    public function delete(Company $company, FeatureTag $tag, Delete $handler)
    {
        $this->denyAccessUnlessGranted('edit', $tag);

        $handler->handle($tag);

        $this->addFlash('success', 'Tag deleted.');

        return $this->redirectToRoute('bo_feature_tag_list', [
            'slug' => $company->getSlug()
        ]);
    }

}
