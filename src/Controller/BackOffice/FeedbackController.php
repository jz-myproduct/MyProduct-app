<?php

namespace App\Controller\BackOffice;

use App\Entity\Company;
use App\Entity\Feedback;
use App\Form\Feedback\AddEditType;
use App\Form\Feedback\ListFilterType;
use App\FormRequest\Feedback\AddEditRequest;
use App\FormRequest\Feedback\ListFilterRequest;
use App\Handler\Feedback\Add;
use App\Handler\Feedback\Delete;
use App\Handler\Feedback\Edit;
use App\Handler\Feedback\Search;
use App\Handler\Feedback\SwitchStatus;
use App\Handler\Feedback\SwitchStatusRedirect;
use App\View\BackOffice\Feedback\DetailView;
use App\View\BackOffice\Feedback\ListView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class FeedbackController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    private $manager;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $manager)
    {
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/{slug}/feedback/add", name="bo_feedback_add")
     * @param Company $company
     * @param Request $request
     * @param Add $handler
     * @return Response
     */
    public function add(Company $company, Request $request, Add $handler): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(AddEditType::class, $formRequest = new AddEditRequest());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $feedback = $handler->add($formRequest, $company);

            $this->addFlash('success', 'Feedback added.');

            return $this->redirectToRoute('bo_feedback_detail', [
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()

            ]);
        }

        return $this->render('back_office/feedback/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/edit", name="bo_feedback_edit")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Request $request
     * @param Edit $handler
     * @return Response
     */
    public function edit(Company $company, Feedback $feedback, Request $request, Edit $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $form = $this->createForm(AddEditType::class, $formRequest = AddEditRequest::fromFeedback($feedback));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($feedback, $formRequest);

            $this->addFlash('success', 'Feedback edited.');

            return $this->redirectToRoute('bo_feedback_detail', [
               'company_slug' => $company->getSlug(),
               'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->render('back_office/feedback/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{slug}/feedback/list", name="bo_feedback_list")
     * @param Company $company
     * @param ListView $view
     * @param Request $request
     * @param Search $handler
     * @return Response
     */
    public function list(Company $company, ListView $view, Request $request, Search $handler)
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $form = $this->createForm(ListFilterType::class, $formRequest = ListFilterRequest::fromArray([
            'isNew' => $isNew = !is_null($request->get('isNew')) ? (int)$request->get('isNew') : null,
            'fulltext' => $fulltext = $request->get('fulltext')
        ]));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            return new RedirectResponse(
                $handler->handle($formRequest, $company)
            );

        }

        return $this->render(
            'back_office/feedback/list.html.twig',
            $view->create($company, $form->createView(), $isNew, $fulltext)
        );
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/delete", name="bo_feedback_delete", methods={"POST"})
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Delete $handler
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Company $company, Feedback $feedback, Delete $handler, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        if ($this->isCsrfTokenValid('delete-item', $request->request->get('token'))) {

            $handler->delete($feedback);

            $this->addFlash('success', 'Feedback deleted.');

        }

        return $this->redirectToRoute('bo_feedback_list', [
            'slug' => $company->getSlug()
        ]);
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/change-status", name="bo_feedback_change_status")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param SwitchStatus $switchHandler
     * @param SwitchStatusRedirect $redirectHandler
     * @param Request $request
     * @return RedirectResponse
     */
    public function switchStatus(
        Company $company,
        Feedback $feedback,
        SwitchStatus $switchHandler,
        SwitchStatusRedirect $redirectHandler,
        Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        $switchHandler->handle($feedback);

        $this->addFlash('success', 'Status edited.');

        return new RedirectResponse(
          $redirectHandler->handle(
              $feedback,
              $company,
              $request->query->get('p'),
              $request->query->get('isNew'),
              $request->query->get('fulltext'))
        );
    }

    /**
     * @Route("/admin/{company_slug}/feedback/{feedback_id}/description", name="bo_feedback_detail")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param DetailView $view
     * @return Response
     */
    public function detail(Company $company, Feedback $feedback, DetailView $view)
    {
        $this->denyAccessUnlessGranted('edit', $feedback);

        return $this->render('back_office/feedback/detail.html.twig', $view->create($feedback));
    }

}
