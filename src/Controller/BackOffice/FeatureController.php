<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\PortalFeature;
use App\Events\FeedbackUpdatedEvent;
use App\Form\FeatureFormType;
use App\Form\FeedbackFeatureDetailFormType;
use App\Form\PortalFeatureFormType;
use App\Handler\Feature\Add;
use App\Handler\Feature\Delete;
use App\Handler\Feature\Edit;
use App\Handler\Feedback\AddOnFeatureDetail;
use App\Services\SlugService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FeatureController extends AbstractController
{
    
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @Route("/admin/{slug}/feature/pridat", name="add-feature")
     * @param Company $company
     * @param Request $request
     * @param Add $handler
     * @return Response
     */
    public function add(Company $company, Request $request, Add $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $feature = new Feature();
        $form = $this->createForm(FeatureFormType::class, $feature, [
            'tags'=> $company->getFeatureTags()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feature, $company);

            return $this->redirectToRoute('feature-list', [
                'slug' => $company->getSlug()
            ]);
        }

        return $this->render('backoffice/addEditFeature.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/upravit", name="edit-feature")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param Edit $handler
     * @return Response
     */
    public function edit(Company $company, Feature $feature, Request $request, Edit $handler)
    {

        $this->denyAccessUnlessGranted('edit', $feature);

        $form = $this->createForm(FeatureFormType::class, $feature, [
            'tags'=> $company->getFeatureTags()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feature);

            $this->addFlash('success', 'Feature updated');
        }

        return $this->render('backoffice/addEditFeature.html.twig', [
            'companySlug' => $company->getSlug(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/features", name="feature-list")
     * @param Company $company
     * @return Response
     */
    public function list(Company $company)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('backoffice/featureList.html.twig', [
            'features' => $company->getFeatures(),
            'companySlug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/smazat", name="delete-feature")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Delete $handler
     * @return RedirectResponse
     */
    public function delete(Company $company, Feature $feature, Delete $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $handler->handle($feature);

        return $this->redirectToRoute('feature-list', [
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/detail", name="feature-detail")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param AddOnFeatureDetail $handler
     * @return Response
     */
    public function detail(Company $company, Feature $feature, Request $request, AddOnFeatureDetail $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $feedback = new Feedback();
        $form = $this->createForm(FeedbackFeatureDetailFormType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $company, $feature);

            return $this->redirectToRoute('feature-detail', [
                'company_slug' => $company->getSlug(),
                'feature_id' => $feature->getId(),
            ]);
        }

        $feedback = $this->getDoctrine()->getRepository(Feedback::class)
            ->getFeatureFeedback($feature);

        return $this->render('backoffice/featureDetail.html.twig', [
            'feature' => $feature,
            'companySlug' => $company->getSlug(),
            'feedbackList' => $feedback,
            'form' => $form->createView(),
            'tags' => $feature->getTags()
        ]);
    }

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
