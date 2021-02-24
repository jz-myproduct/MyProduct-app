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
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EntityManager
     */
    private $manager;
    /**
     * @var SlugService
     */
    private $slugService;

    public function __construct(EventDispatcherInterface $dispatcher, EntityManagerInterface $manager, SlugService $slugService)
    {
        $this->dispatcher = $dispatcher;
        $this->manager = $manager;
        $this->slugService = $slugService;
    }


    /**
     * @Route("/admin/{slug}/feature/pridat", name="add-feature")
     * @param Company $company
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function add(Company $company, Request $request): Response
    {
        $this->denyAccessUnlessGranted('edit', $company);

        $feature = new Feature();
        $form = $this->createForm(FeatureFormType::class, $feature, [
            'tags'=> $company->getFeatureTags()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $feature = new Feature();
            $feature->setName(
                $form->get('name')->getData()
            );
            $feature->setDescription(
                $form->get('description')->getData()
            );
            $feature->setCompany($company);
            $feature->setState(
                $form->get('state')->getData()
            );
            $feature->setInitialScore();

            foreach ($form->get('tags')->getData() as $tag){
                $feature->addTag($tag);
            }

            $currentDateTime = new DateTime();
            $feature->setCreatedAt($currentDateTime);
            $feature->setUpdatedAt($currentDateTime);

            $this->manager->persist($feature);
            $this->manager->flush();

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
     * @return Response
     * @throws Exception
     */
    public function edit(Company $company, Feature $feature, Request $request)
    {

        $this->denyAccessUnlessGranted('edit', $feature);

        $form = $this->createForm(FeatureFormType::class, $feature, [
            'tags'=> $company->getFeatureTags()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $feature->setName(
                $form->get('name')->getData()
            );
            $feature->setDescription(
                $form->get('description')->getData()
            );
            $feature->setUpdatedAt(new DateTime());
            $feature->setState(
                $form->get('state')->getData()
            );

            $this->manager->flush();

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
     * @return RedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Company $company, Feature $feature)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $this->manager->remove($feature);
        $this->manager->flush();

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
     * @return Response
     * @throws Exception
*/
    public function detail(Company $company, Feature $feature, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $feedback = new Feedback();
        $form = $this->createForm(FeedbackFeatureDetailFormType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $feedback->setDescription(
                $form->get('description')->getData()
            );
            $feedback->setSource(
                $form->get('source')->getData()
            );
            $feedback->setCompany($company);
            $feedback->setActiveStatus();

            $currentDateTime = new DateTime();
            $feedback->setCreatedAt($currentDateTime);
            $feedback->setUpdatedAt($currentDateTime);
            $feedback->addFeature($feature);

            $feature->setScoreUpByOne();

            $this->manager->persist($feedback);
            $this->manager->flush();

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
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function portal(Company $company, Feature $feature, Request $request)
    {
        $this->denyAccessUnlessGranted('edit', $feature);

        $portalFeature = $feature->getPortalFeature() ?? new PortalFeature();
        $currentFeedbackCount = $feature->getPortalFeature() ?
             $feature->getPortalFeature()->getFeedbackCount()
           : 0;

        $form = $this->createForm(PortalFeatureFormType::class, $portalFeature);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $name = $form->get('name')->getData();
            $currentDateTime = new \DateTime();

            if($currentFeedbackCount === 0)
            {
                $portalFeature->setFeedbackCount(0);
            }
            $portalFeature->setName($name);
            $portalFeature->setSlug(
                $this->slugService->createCommonSlug($name)
            );
            $portalFeature->setDescription(
                $form->get('description')->getData()
            );
            $portalFeature->setDisplay(
                $form->get('display')->getData()
            );
            $portalFeature->setCreatedAt($currentDateTime);
            $portalFeature->setUpdatedAt($currentDateTime);
            $portalFeature->setFeature($feature);


            $this->manager->persist($portalFeature);
            $this->manager->flush();

            $this->addFlash('success', 'Portal feature updated');
        }

        return $this->render('backoffice/featurePortal.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
