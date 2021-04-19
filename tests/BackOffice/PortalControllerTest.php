<?php

namespace App\Tests\BackOffice;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use PHPUnit\Framework\AssertionFailedError;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PortalControllerTest extends WebTestCase
{
    public function testPortal(): void
    {
        $client = static::createClient();

        /** @var Company $company */

        if(!$company = $this->getCompany())
        {
            throw new AssertionFailedError('Company not found');
        }

        $client->loginUser($company);

        $client->request('GET', '/admin/' . $company->getSlug() . '/portal');
        $this->assertResponseStatusCodeSame('200');
        $this->assertSelectorTextContains('h1', 'Portal');
    }

    private function getCompany()
    {
        $companyRepository = static::$container->get(CompanyRepository::class);

        return $companyRepository->findOneByEmail('h@h.hh');
    }
}
