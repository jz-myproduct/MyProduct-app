<?php

namespace App\Tests\BackOffice;

use App\Entity\Company;
use App\Entity\Feature;
use App\Entity\Feedback;
use App\Entity\Insight;
use App\Repository\CompanyRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InsightControllerTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    /**
     * @var Company
     */
    private $company;

    /**
     * @var Insight
     */
    private $insight;
    /**
     * @var Feedback|null
     */
    private $feedback;
    /**
     * @var Feature|null
     */
    private $feature;

    public function testAll(): void
    {
        $this->client = static::createClient();
        $this->company = $this->getCompany();
        $this->insight = $this->getInsight();

        if(! $this->company || ! $this->insight)
        {
            throw new AssertionFailedError('Insight/Company not found in database.');
        }

        $this->feedback = $this->insight->getFeedback();
        $this->feature = $this->insight->getFeature();

        $this->client->loginUser($this->company);

        $this->testEdit();
        $this->testDelete();
        $this->testAdd();
    }

    private function testEdit()
    {
        $this->client->request('GET', $this->getCommonUrl().'edit-insight/'.$this->insight->getId());
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Edit insight');
    }

    private function testDelete()
    {
        $this->client->request('GET', $this->getCommonUrl().'delete-insight/'.$this->insight->getId());
        $this->assertResponseStatusCodeSame('302');
    }

    private function testAdd()
    {
        $this->client->request(
            'GET',
            $this->getCommonUrl().'add-insight/'.$this->feedback->getId().'/'.$this->feature->getId()
        );
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Add insight');
    }

    private function getInsight()
    {

        foreach ($this->company->getFeatures() as $feature)
        {
            foreach ($feature->getInsights() as $insight)
            {
                return $insight;
            }
        }

        throw new AssertionFailedError('No insight');
    }

    private function getCommonUrl()
    {
        return '/admin/'.$this->company->getSlug().'/';
    }

    private function getCompany()
    {
        $companyRepository = static::$container->get(CompanyRepository::class);

        return $companyRepository->findOneByEmail('h@h.hh');
    }
}
