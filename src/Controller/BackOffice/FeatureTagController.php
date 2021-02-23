<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\FeatureTag;
use App\Form\FeatureTagFormType;
use App\Services\SlugService;
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
     * @Route("/admin/{slug}/tag/pridat", name="add-feature-tag")
     * @param Company $company
     * @param Request $request
     * @param SlugService $slugService
     * @return Response
     */
    public function add(Company $company, Request $request, SlugService $slugService)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $entityManager = $this->getDoctrine()->getManager();

        $tag = new FeatureTag();
        $form = $this->createForm(FeatureTagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();

            $tag->setName($name);
            $tag->setSlug($slugService->createCommonSlug($name));
            $tag->setCompany( $company );

            $entityManager->persist($tag);
            $entityManager->flush();

            return $this->redirectToRoute('feature-tag-list', [
                'slug' => $company->getSlug()
            ]);
        }

        return $this->render('backoffice/addEditFeatureTag.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/tag/{tag_id}/upravit", name="edit-feature-tag")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("tag", options={"mapping": {"tag_id": "id"}} )
     * @param Company $company
     * @param Request $request
     * @param SlugService $slugService
     * @param FeatureTag $tag
     * @return Response
     */
    public function edit(Company $company, Request $request, SlugService $slugService, FeatureTag $tag)
    {
        $this->denyAccessUnlessGranted('edit', $tag);

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(FeatureTagFormType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name = $form->get('name')->getData();

            $tag->setName($name);
            $tag->setSlug($slugService->createCommonSlug($name));

            $entityManager->persist($tag);
            $entityManager->flush();

            $this->addFlash('success', 'Tag updated');
        }

        return $this->render('backoffice/addEditFeatureTag.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/tags", name="feature-tag-list")
     * @param Company $company
     * @return Response
     */
    public function list(Company $company)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('backoffice/featureTagsList.html.twig', [
            'tags' => $company->getFeatureTags(),
            'slug' => $company->getSlug()
        ]);

    }

    /**
     * @Route("/admin/{company_slug}/tag/{tag_id}/smazat", name="delete-feature-tag")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("tag", options={"mapping": {"tag_id": "id"}} )
     * @param Company $company
     * @param FeatureTag $tag
     * @return RedirectResponse
     */
    public function delete(Company $company, FeatureTag $tag)
    {
        $this->denyAccessUnlessGranted('edit', $tag);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($tag);
        $entityManager->flush();

        return $this->redirectToRoute('feature-tag-list', [
            'slug' => $company->getSlug()
        ]);
    }

}
