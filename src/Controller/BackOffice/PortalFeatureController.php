<?php


namespace App\Controller\BackOffice;


use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use App\Form\PortalFeature\AddEditFormType;
use App\FormRequest\PortalFeature\AddEditRequest;
use App\Handler\PortalFeature\AddEdit;
use App\Handler\PortalFeature\DeleteImage;
use App\View\BackOffice\PortalFeature\DetailView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PortalFeatureController extends AbstractController
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
     * @Route("/admin/{company_slug}/feature/{feature_id}/portal", name="bo_feature_portal")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param Request $request
     * @param AddEdit $handler
     * @param DetailView $view
     * @return Response
     */
    public function detail(
        Company $company,
        Feature $feature,
        Request $request,
        AddEdit $handler,
        DetailView $view)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $portalFeature = $feature->getPortalFeature() ?? new PortalFeature();

        $formRequest = $feature->getPortalFeature() ? AddEditRequest::fromPortalFeature($portalFeature)
                                                    : new AddEditRequest();

        $form = $this->createForm(AddEditFormType::class, $formRequest, [
            'states' => $this->manager->getRepository(PortalFeatureState::class)->findAll()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            if(! $handler->handle(
                $formRequest,
                $portalFeature,
                $feature,
                $form->get('image')->getData() ?? null
            ))
            {
                $this->addFlash('error', 'Error occurs, try it later please.');

            } else {

                $this->addFlash('success', 'Feature edited.');

                return $this->redirectToRoute('bo_feature_portal', [
                    'company_slug' => $company->getSlug(),
                    'feature_id' => $feature->getId()
                ]);
            }
        }

        return $this->render('back_office/portal_feature/detail.html.twig',
            $view->create($form->createView(), $feature));
    }

    /**
     * @Route("/admin/{company_slug}/feature/{feature_id}/portal/smazat-obrazek/{file_id}", name="bo_feature_portal_image_delete")
     * @ParamConverter("company", options={"mapping": {"company_slug": "slug"}})
     * @ParamConverter("feature", options={"mapping": {"feature_id": "id"}})
     * @ParamConverter("file", options={"mapping": {"file_id": "id"}})
     * @param Company $company
     * @param Feature $feature
     * @param \App\Entity\File $file
     * @param DeleteImage $handler
     * @return RedirectResponse
     */
    public function deleteImage(
        Company $company,
        Feature $feature,
        \App\Entity\File $file,
        DeleteImage $handler)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        // allow to delete only file related to feature, because we are sure, that user has access to feature
        if($feature->getPortalFeature()->getImage() !== $file)
        {
            throw new NotFoundHttpException();
        }

        $handler->handle($file, $feature);

        $this->addFlash('success', 'Picture deleted.');

        return $this->redirectToRoute('bo_feature_portal', [
            'company_slug' => $company->getSlug(),
            'feature_id' => $feature->getId()
        ]);
    }

}