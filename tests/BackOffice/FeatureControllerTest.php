<?php

namespace App\Tests;

use App\Entity\Company;
use App\Entity\Feature;
use App\Repository\CompanyRepository;
use App\Repository\FeatureRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeatureControllerTest extends WebTestCase
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
     * @var Feature
     */
    private $feature;

    public function testAll(): void
    {
        $this->client = static::createClient();
        $this->company = $this->getCompany();
        $this->feature = $this->getFeature();

        if(! $this->feature || ! $this->company)
        {
            throw new AssertionFailedError('Feature/Company not found in database');
        }

        $this->client->loginUser($this->company);

        $this->testList();
        $this->testRoadmap();
        $this->testAdd();
        $this->testEdit();
        $this->testPortalFeature();
        $this->testDelete();

    }

    private function testList()
    {
        $this->client->request('GET', $this->getCommonUrl().'features/list');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Features');
    }

    private function testRoadmap()
    {
        $this->client->request('GET', $this->getCommonUrl().'features/roadmap');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Features');
    }

    private function testAdd()
    {
        $this->client->request('GET', $this->getCommonUrl().'feature/add');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Add feature');
    }

    private function testEdit()
    {
        $this->client->request('GET', $this->getCommonUrl().'feature/'.$this->feature->getId().'/edit');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorExists('h1');
    }

    private function testPortalFeature()
    {
        $this->client->request('GET', $this->getCommonUrl().'feature/'.$this->feature->getId().'/portal');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorExists('h1');
    }

    private function testDelete()
    {
        $this->client->request('GET', $this->getCommonUrl().'feature/'.$this->feature->getId().'/delete');
        $this->assertResponseStatusCodeSame('302');
    }

    private function getCommonUrl()
    {
        return '/admin/'.$this->company->getSlug().'/';
    }

    private function getFeature()
    {
        $featureRepository = static::$container->get(FeatureRepository::class);

        return $featureRepository->find(1);
    }

    private function getCompany()
    {
        $companyRepository = static::$container->get(CompanyRepository::class);

        return $companyRepository->findOneByEmail('h@h.hh');
    }
}
