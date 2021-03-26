<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\FeatureTag;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Entity\InsightWeight;
use App\Form\Insight\AddFromFeatureType;
use App\Form\Insight\AddFromFeedbackType;
use App\Form\Insight\FilterOnFeedback;
use App\FormRequest\Insight\AddFromFeatureRequest;
use App\FormRequest\Insight\AddFromFeedbackRequest;
use App\FormRequest\Insight\FilterOnFeedbackRequest;
use App\Handler\Insight\Add;
use App\Handler\Insight\Delete;
use App\Handler\Insight\Edit;
use App\Handler\Insight\Redirect;
use App\Handler\Insight\Search;
use App\View\BackOffice\Feature\FilterFormView;
use App\View\BackOffice\Insight\AddEditView;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

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
     * @param Add $handler
     * @param AddEditView $view
     * @return RedirectResponse|Response
     */
    public function addFromFeedback(
        Company $company,
        Feedback $feedback,
        Feature $feature,
        Request $request,
        Add $handler,
        AddEditView $view)
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

            $handler->addFromFeedback($formRequest, $feedback, $feature);

            $this->addFlash('success', 'Featura připojena.');

            return $this->redirectToRoute('bo_insight_feedback_list',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->render('back_office/insight/add_edit.html.twig',
            $view->addFromFeedback($feedback, $feature, $form->createView())
        );
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/insights", name="bo_insight_feature_list")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param Add $handler
     * @param ListOnFeatureView $view
     * @return RedirectResponse|Response
     */
    public function listOnFeature(
        Company $company,
        Feature $feature,
        Request $request,
        Add $handler,
        ListOnFeatureView $view)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $form = $this->createForm(AddFromFeatureType::class, $formRequest = new AddFromFeatureRequest(), [
            'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->addFromFeature($formRequest, $feature);

            $this->addFlash('success', 'Insight přidán.');

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
     * @param Request $request
     * @param FilterFormView $formView
     * @param Search $handler
     * @return Response
     */
    public function listOnFeedback(
        Company $company,
        Feedback $feedback,
        ListOnFeedbackView $view,
        Request $request,
        FilterFormView $formView,
        Search $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $formRequest = FilterOnFeedbackRequest::fromArray([
          'fulltext' => $request->get('fulltext'),
          'tags' => $request->get('tags'),
          'state' => $request->get('state')
        ]);

        $form = $this->createForm(FilterOnFeedback::class, $formRequest, [
            'tags' => $formView->createTags($company),
            'states' => $formView->createStates()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return new RedirectResponse(
                $handler->handle($formRequest, $company, $feedback)
            );

        }

        return $this->render('back_office/insight/list_on_feedback.html.twig' ,
            $view->create(
                $company,
                $feedback, $form->createView(),
                $formRequest
            )
        );
    }

    /**
     * @Route("/admin/{company_slug}/upravit-insight/{insight_id}", name="bo_insight_edit")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("insight", options={"mapping": {"insight_id": "id"}})
     * @param Company $company
     * @param Insight $insight
     * @param Request $request
     * @param Edit $editHandler
     * @param Redirect $redirectHandler
     * @param AddEditView $view
     * @return RedirectResponse|Response
     */
    public function edit(
        Company $company,
        Insight $insight,
        Request $request,
        Edit $editHandler,
        Redirect $redirectHandler,
        AddEditView $view)
    {
        $this->denyAccessUnlessGranted('edit', $insight);

        $form = $this->createForm(AddFromFeedbackType::class,
            $formRequest = AddFromFeedbackRequest::fromInsight($insight), [
                'weights' => $this->manager->getRepository(InsightWeight::class)->findAll()
            ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $editHandler->handle($formRequest, $insight);

            $this->addFlash('success', 'Upraveno.');

            return new RedirectResponse(
                $redirectHandler->handle(
                    $insight,
                    $company,
                    $request->query->get('p')
                )
            );
        }

        return $this->render('back_office/insight/add_edit.html.twig',
            $view->edit($insight, $form->createView(), $request->query->get('p'))
        );
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

        $this->addFlash('success', 'Smazáno.');

        return new RedirectResponse(
            $redirectHandler->handle(
                $insight,
                $company,
                $request->query->get('p')
            )
        );
    }

}