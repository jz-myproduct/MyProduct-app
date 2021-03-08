<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Form\InsightFormType;
use App\Handler\Insight\Add;
use App\Handler\Insight\Delete;
use App\Handler\Insight\Edit;
use App\Handler\Insight\Redirect;
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
     * @Route("/admin/{company_slug}/pridat-insight/{feedback_id}/{feature_id}", name="bo_insight_add")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feedback", options={"mapping": {"feedback_id": "id"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feedback $feedback
     * @param Feature $feature
     * @param Request $request
     * @param Add $handler
     * @return RedirectResponse|Response
     */
    public function add(
        Company $company,
        Feedback $feedback,
        Feature $feature,
        Request $request,
        Add $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);
        $this->denyAccessUnlessGranted('edit', $feedback);

        if($this->manager->getRepository(Insight::class)
            ->findBy([
                'feedback' => $feedback,
                'feature' => $feature])
        )
        {
            $this->addFlash('info', 'Featura je již přidána.');

            return $this->redirectToRoute('bo_feedback_features',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        $form = $this->createForm(InsightFormType::class, $insight = new Insight());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($insight, $feedback, $feature);

            $this->addFlash('success', 'Featura připojena.');

            return $this->redirectToRoute('bo_feedback_features',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $feedback->getId()
            ]);
        }

        return $this->render('back_office/insight/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
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

        $form = $this->createForm(InsightFormType::class, $insight);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $handler->handle($insight);

            $this->addFlash('success', 'Spojení upraveno.');

            return $this->redirectToRoute('bo_feedback_features',[
                'company_slug' => $company->getSlug(),
                'feedback_id' => $insight->getFeedback()->getId()
            ]);
        }

        return $this->render('back_office/insight/add_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

}