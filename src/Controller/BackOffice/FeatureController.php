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
use App\View\Feature\ListView;
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
     * @Route("/admin/{slug}/feature/pridat", name="bo_feature_add")
     * @param Company $company
     * @param Request $request
     * @param Add $handler
     * @return Response
     */
    public function add(Company $company, Request $request, Add $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(FeatureFormType::class, $feature = new Feature(), [
            'tags'=> $company->getFeatureTags()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feature, $company);

            $this->addFlash('success', 'Feature přidána.');

            return $this->redirectToRoute('bo_feature_list', [
                'slug' => $company->getSlug()
            ]);
        }

        return $this->render('back_office/feature/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/upravit", name="bo_feature_edit")
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

            $this->addFlash('success', 'Feature upravena.');

            return $this->redirectToRoute('bo_feature_detail', [
                'company_slug' => $company->getSlug(),
                'feature_id' => $feature->getId()
            ]);
        }

        return $this->render('back_office/feature/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/features", name="bo_feature_list")
     * @param Company $company
     * @param ListView $view
     * @return Response
     */
    public function list(Company $company, ListView $view)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        return $this->render('back_office/feature/list.html.twig', $view->create($company));
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/smazat", name="bo_feature_delete")
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

        $this->addFlash('success', 'Feature smazána.');

        return $this->redirectToRoute('bo_feature_list', [
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/detail", name="bo_feature_detail")
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

        // TODO tohle je zbytečné, jen získat počet
        $feedback = $this->getDoctrine()->getRepository(Feedback::class)
            ->getFeatureFeedback($feature);

        return $this->render('back_office/feature/detail.html.twig', [
            'feature' => $feature,
            'feedbacks' => $feedback
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/feedback", name="bo_feature_feedback")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param AddOnFeatureDetail $handler
     * @return RedirectResponse|Response
     */
    public function feedback(Company $company, Feature $feature, Request $request, AddOnFeatureDetail $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $form = $this->createForm(FeedbackFeatureDetailFormType::class, $feedback = new Feedback());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $company, $feature);

            $this->addFlash('success', 'Feedback přidán');

            return $this->redirectToRoute('bo_feature_feedback', [
                'company_slug' => $company->getSlug(),
                'feature_id' => $feature->getId(),
            ]);
        }

        $feedback = $this->getDoctrine()->getRepository(Feedback::class)
            ->getFeatureFeedback($feature);

        return $this->render('back_office/feature/feedback.html.twig', [
            'feature' => $feature,
            'feedbacks' => $feedback,
            'form' => $form->createView(),
        ]);
    }
}
