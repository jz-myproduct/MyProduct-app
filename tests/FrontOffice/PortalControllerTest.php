<?php

namespace App\Tests\FrontOffice;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PortalControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    /**
     * @var Company
     */
    private $company;

    public function testAll(): void
    {
        $this->client = static::createClient();

        /** @var Company $company */
        if(! $this->company = $this->getCompany())
        {
            throw new AssertionFailedError('Company not found');
        }

        $this->testPortal();
        $this->testFeedback();
        $this->testFeature();
    }

    private function testPortal()
    {
        $this->client->request('GET', '/portal/'.$this->company->getPortal()->getSlug());
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('a', $this->company->getPortal()->getName());
    }

    private function testFeedback()
    {
        $this->client->request('GET', '/portal/'.$this->company->getPortal()->getSlug().'/feedback/add');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h3', 'Add feedback');
    }

    private function testFeature()
    {

        foreach ($this->company->getFeatures() as $feature)
        {
            $portalFeature = $feature->getPortalFeature();

            if($portalFeature->getDisplay())
            {
                $this->client->request(
                    'GET',
                    '/portal/'.$this->company->getPortal()->getSlug().'/feature/'.$portalFeature->getId()
                );

                $this->assertResponseStatusCodeSame('200');
                $this->assertSelectorTextContains('h2', $portalFeature->getName());

                return 0;
            }
        }

        throw new AssertionFailedError('Company has no features');
    }

    private function getCompany()
    {
        $companyRepository =  static::$container->get(CompanyRepository::class);

        return $companyRepository->find(1);
    }




}
