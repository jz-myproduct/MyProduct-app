<?php

namespace App\Tests;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
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

        $this->testLogin();
        $this->testRegister();
        $this->testForgottenPassword();

        if(! $this->company = $this->getCompany())
        {
            throw new AssertionFailedError('No company found');
        }

        $this->client->loginUser($this->company);
        $this->testPasswordChange();
        $this->testCompanyDelete();
    }

    private function testPasswordChange()
    {
        $this->client->request('GET', '/admin/' . $this->company->getSlug() . '/settings/change-password');
        $this->assertResponseStatusCodeSame('200');
    }

    private function testCompanyDelete()
    {
        $this->client->request('GET', '/admin/' . $this->company->getSlug() . '/settings/delete-company');
        $this->assertResponseStatusCodeSame('200');
    }

    private function testLogin()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Log in');
    }

    private function testRegister()
    {
        $this->client->request('GET', '/register');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Sign up to MyProduct');
    }

    private function testForgottenPassword()
    {
        $this->client->request('GET', '/forgotten-password');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Forgotten password');
    }

    private function getCompany()
    {
        $companyRepository = static::$container->get(CompanyRepository::class);

        return $companyRepository->findOneByEmail('h@h.hh');
    }
}
