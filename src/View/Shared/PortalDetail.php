<?php


namespace App\View\Shared;


use App\Entity\Company;
use App\Entity\PortalFeature;
use App\Entity\PortalFeatureState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class PortalDetail
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $manager, RouterInterface $router)
    {
        $this->manager = $manager;
        $this->router = $router;
    }

    public function create(Company $company, $state = null, FormView $form = null)
    {
        $currentState = $state ?? $this->manager->getRepository(PortalFeatureState::class)
                                    ->findInitialState();

        $stateList = $this->manager->getRepository(PortalFeatureState::class)->findAll();

        $portalFeatureList = $this->manager->getRepository(PortalFeature::class)
                ->findFeaturesForPortalByState($company, $currentState);

        $array = [
            'scrollTo' => 'portalScroll',
            'currentState' => $currentState,
            'stateList' => $stateList,
            'portalFeatureList' => $portalFeatureList,
            'portal' => $company->getPortal(),
            'portalPublicUrl' => $this->router->generate('fo_portal_detail', [
                'slug' => $company->getPortal()->getSlug()
            ], UrlGeneratorInterface::ABSOLUTE_URL)
        ];

        if($form){
            $array['form'] = $form;
        }

        return $array;
    }

}