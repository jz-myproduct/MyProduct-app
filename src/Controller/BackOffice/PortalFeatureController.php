<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\PortalFeature;
use App\Form\PortalFeatureFormType;
use App\Handler\PortalFeature\Add;
use App\Handler\PortalFeature\Edit;
use App\View\BackOffice\PortalFeature\FeedbackListView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param FeedbackListView $view
     * @return Response
     */

    public function detail(
        Company $company,
        Feature $feature,
        Request $request,
        Add $addHandler,
        Edit $editHandler,
        FeedbackListView $view)
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

            $this->addFlash('success', 'Featura na portálu upravena.');
        }

        return $this->render('back_office/portal_feature/detail.html.twig',[
            'form' => $form->createView(),
            'feature' => $feature,
            'feedbackList' => $view->create($feature)
        ]);
    }


}