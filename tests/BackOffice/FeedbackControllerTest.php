<?php

namespace App\Tests\BackOffice;

use App\Entity\Company;
use App\Entity\Feedback;
use App\Repository\CompanyRepository;
use App\Repository\FeedbackRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedbackControllerTest extends WebTestCase
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
     * @var Feedback
     */
    private $feedback;


    public function testAll(): void
    {
        $this->client = static::createClient();
        $this->company = $this->getCompany();
        $this->feedback = $this->getFeedback();

        if(! $this->feedback || ! $this->company)
        {
            throw new AssertionFailedError('Feedback/Company not found in database');
        }

        $this->client->loginUser($this->company);

        $this->testList();
        $this->testAdd();
        $this->testEdit();
        $this->testDelete();
    }

    private function testList()
    {
        $this->client->request('GET', $this->getCommonUrl().'list');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Feedback');
    }

    private function testAdd()
    {
        $this->client->request('GET', $this->getCommonUrl().'add');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Add feedback');

        $this->getCommonUrl();
    }

    private function testEdit()
    {
        $this->client->request('GET', $this->getCommonUrl().$this->feedback->getId().'/edit'
        );
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorExists('h1');
    }

    private function testDelete()
    {
        $this->client->request('GET', $this->getCommonUrl().$this->feedback->getId().'/delete'
        );
        $this->assertResponseStatusCodeSame('302');
    }

    private function getCommonUrl()
    {
        return '/admin/'.$this->company->getSlug().'/feedback/';
    }

    private function getFeedback()
    {
        $feedbackRepository = static::$container->get(FeedbackRepository::class);

        return $feedbackRepository->find(1);
    }

    private function getCompany()
    {
        $companyRepository = static::$container->get(CompanyRepository::class);

        return $companyRepository->findOneByEmail('h@h.hh');
    }
}
