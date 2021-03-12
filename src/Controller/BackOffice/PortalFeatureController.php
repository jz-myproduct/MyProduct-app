<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\PortalFeature;
use App\Form\PortalFeatureFormType;
use App\Handler\PortalFeature\Add;
use App\Handler\PortalFeature\Edit;
use App\Services\FileUploader;
use App\View\BackOffice\PortalFeature\DetailView;
use PhpParser\Node\Scalar\MagicConst\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PortalFeatureController extends AbstractController
{

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/portal", name="bo_feature_portal")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param Add $addHandler
     * @param Edit $editHandler
     * @param DetailView $view
     * @return Response
     */
    public function detail(
        Company $company,
        Feature $feature,
        Request $request,
        Add $addHandler,
        Edit $editHandler,
        DetailView $view)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $portalFeature = $feature->getPortalFeature() ?? new PortalFeature();

        $form = $this->createForm(PortalFeatureFormType::class, $portalFeature);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            // portal feature already exists
            if( $feature->getPortalFeature() ){

                $editHandler->handle(
                    $portalFeature,
                    $form->get('image')->getData()
                );

            } else {
                $addHandler->handle(
                    $portalFeature,
                    $feature,
                    $form->get('image')->getData()
                );
            }


            $this->addFlash('success', 'Featura na portálu upravena.');
        }

        return $this->render('back_office/portal_feature/detail.html.twig',
            $view->create($form->createView(), $feature));
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/portal/smazat-obrazek/{file_id}", name="bo_feature_portal_image_delete")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param \App\Entity\File $file
     * @param FileUploader $fileUploader
     */
    public function deleteImage(Company $company, Feature $feature, \App\Entity\File $file, FileUploader $fileUploader)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        // allow to delete only file related to feature, because we are sure, that user has access to feature
        // TODO maybe make a voter?
        if($feature->getPortalFeature()->getImage() !== $file)
        {
            throw new NotFoundHttpException();
        }

        //TODO
    }


}