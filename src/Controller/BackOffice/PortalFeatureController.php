<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\PortalFeature;
use App\Form\PortalFeatureFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PortalFeatureController extends AbstractController
{

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/portal", name="feature-portal")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param \App\Handler\PortalFeature\Add $addHandler
     * @param \App\Handler\PortalFeature\Edit $editHandler
     * @return Response
     */
    public function portal(
        Company $company,
        Feature $feature,
        Request $request,
        \App\Handler\PortalFeature\Add $addHandler,
        \App\Handler\PortalFeature\Edit $editHandler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $portalFeature = $feature->getPortalFeature() ?? new PortalFeature();

        $form = $this->createForm(PortalFeatureFormType::class, $portalFeature);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // portal feature already exists
            if( $feature->getPortalFeature() ){
                $editHandler->handle($portalFeature);
            } else {
                $addHandler->handle($portalFeature, $feature);
            }

            $this->addFlash('success', 'Portal feature updated');
        }

        return $this->render('backoffice/featurePortal.html.twig',[
            'form' => $form->createView()
        ]);
    }

}