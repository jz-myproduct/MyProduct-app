<?php

namespace App\Tests\BackOffice;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @var Company
     */
    private $company;
    /**
     * @var KernelBrowser
     */
    private $client;

    public function testAll(): void
    {
        $this->client = static::createClient();
        $this->company = $this->getCompany();

        if(!$this->company)
        {
            throw new AssertionFailedError('Company not found in database.');
        }

        $this->client->loginUser($this->company);

        $this->testSettings();
        $this->testPortal();
    }

    private function testSettings()
    {
        $this->client->request('GET', '/admin/' . $this->company->getSlug() . '/settings/info');
        $this->assertResponseStatusCodeSame('200');
    }

    private function testPortal()
    {
        $this->client->request('GET', '/admin/' . $this->company->getSlug() . '/portal');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Portal');
    }

    private function getCompany()
    {
        $companyRepository = static::$container->get(CompanyRepository::class);

        return $companyRepository->findOneByEmail('h@h.hh');
    }
}
