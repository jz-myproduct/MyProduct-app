<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use App\Form\AddFromFeatureType;
use App\Form\AddFromFeedbackType;
use App\FormRequest\Insight\AddFromFeatureRequest;
use App\FormRequest\Insight\AddFromFeedbackRequest;
use App\Handler\Insight\AddFromFeature;
use App\Handler\Insight\AddFromFeedback;
use App\Handler\Insight\Delete;
use App\Handler\Insight\Edit;
use App\Handler\Insight\Redirect;
use App\View\BackOffice\Insight\ListOnFeatureView;
use App\View\BackOffice\Insight\ListOnFeedbackView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class InsightController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/{company_slug}/pridat-insight/{feedback_id}/{feature_id}", name="bo_insight_feedback_add")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Feature $feature
     * @param Request $request
     * @param AddFromFeedback $handler
     * @return RedirectResponse|Response
     */
    public function addFromFeedback(
        Company $company,
        Feedback $feedback,
        Feature $feature,
        Request $request,
        AddFromFeedback $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);
        $this->denyAccessUnlessGranted('edit', $feedback);

        if($this->manager->getRepository(Insight::class)->findBy([
                'feedback' => $feedback,
                'feature' => $feature]))
        {
            $this->addFlash('info', 'Featura je již přidána.');

            return $this->redirectToRoute('bo_insight_feedback_list',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        $form = $this->createForm(AddFromFeedbackType::class, $formRequest = new AddFromFeedbackRequest(), [
            'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($formRequest, $feedback, $feature);

            $this->addFlash('success', 'Featura připojena.');

            return $this->redirectToRoute('bo_insight_feedback_list',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->render('back_office/insight/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/insights", name="bo_insight_feature_list")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param AddFromFeature $handler
     * @param ListOnFeatureView $view
     * @return RedirectResponse|Response
     */
    public function listOnFeature(
        Company $company,
        Feature $feature,
        Request $request,
        AddFromFeature $handler,
        ListOnFeatureView $view)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $form = $this->createForm(AddFromFeatureType::class, $formRequest = new AddFromFeatureRequest(), [
            'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($formRequest, $feature);

            $this->addFlash('success', 'Feedback přidán');

            return $this->redirectToRoute('bo_insight_feature_list', [
                'company_slug' => $company->getSlug(),
                'feature_id' => $feature->getId(),
            ]);
        }

        return $this->render(
            'back_office/insight/list_on_feature.html.twig',
            $view->create($feature, $form->createView()));
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/features", name="bo_insight_feedback_list")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param ListOnFeedbackView $view
     * @return Response
     */
    public function listOnFeedback(Company $company, Feedback $feedback, ListOnFeedbackView $view)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        return $this->render('back_office/insight/list_on_feedback.html.twig' , $view->create($company, $feedback));
    }

    /**
     * @Route("/admin/{company_slug}/upravit-insight/{insight_id}", name="bo_insight_edit")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("insight", options={"mapping": {"insight_id": "id"}})
     * @param Company $company
     * @param Insight $insight
     * @param Request $request
     * @param Edit $handler
     * @return RedirectResponse|Response
     */
    public function edit(
        Company $company,
        Insight $insight,
        Request $request,
        Edit $handler)
    {
        $this->denyAccessUnlessGranted('edit', $insight);

        $form = $this->createForm(AddFromFeedbackType::class,
            $formRequest = AddFromFeedbackRequest::fromInsight($insight), [
                'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($formRequest, $insight);

            $this->addFlash('success', 'Spojení upraveno.');

            return $this->redirectToRoute('bo_insight_feedback_list',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $insight->getFeedback()->getId()
            ]);
        }

        return $this->render('back_office/insight/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/smazat-insight/{insight_id}", name="bo_insight_delete")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("insight", options={"mapping": {"insight_id": "id"}})
     * @param Company $company
     * @param Insight $insight
     * @param Delete $deleteHandler
     * @param Redirect $redirectHandler
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(
        Company $company,
        Insight $insight,
        Delete $deleteHandler,
        Redirect $redirectHandler,
        Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $insight);

        $deleteHandler->handle($insight);

        $this->addFlash('success', 'Spojení odebráno.');

        return new RedirectResponse(
            $redirectHandler->handle(
                $request->query->get('p'),
                $insight,
                $company
            )
        );
    }

}