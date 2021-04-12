<?php


namespace App\Handler\Security;


use App\Entity\Company;
use App\FormRequest\Security\RenewPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;

class RenewPassword
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var MailerInterface
     */
    private $mailer;

    private $fromMail;

    private $domainUrl;

    private $appName;

    public function __construct(
        EntityManagerInterface $manager,
        RouterInterface $router,
        MailerInterface $mailer,
        $fromMail,
        $domainUrl,
        $appName)
    {
        $this->manager = $manager;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->fromMail = $fromMail;
        $this->domainUrl = $domainUrl;
        $this->appName = $appName;
    }

    public function handle(RenewPasswordRequest $request)
    {
        if(! $company = $this->manager->getRepository(Company::class)
            ->findOneBy(['email' => $request->email]))
        {
            return false;
        }

        $company->setPasswordRenewHash(
            uuid_create(UUID_TYPE_RANDOM)
        );

        $company->setPasswordHashValidUntil(
            (new \DateTime())->add( new \DateInterval('PT1H'))
        );

        $company->setUpdatedAt(new \DateTime());

        if(! $this->sendMail($company)){
            return false;
        }

        $this->manager->flush();

        return $company;
    }

    private function sendMail(Company $company)
    {
        try {

            $this->mailer->send(
                $this->prepareMail($company)
            );

        } catch (TransportExceptionInterface $e) {
            return false;
        }

        return true;
    }

    private function prepareMail(Company $company)
    {
        $url = $this->router->generate('fo_set_new_password', [
            'hash' => $company->getPasswordRenewHash()
        ]);

        $url = $this->domainUrl.$url;

        return (new TemplatedEmail())
                    ->from($this->fromMail)
                    ->to($company->getEmail())
                    ->subject('Password renewal | '.$this->appName)
                    ->htmlTemplate('email/forgotten_password.html.twig')
                    ->textTemplate('email/forgotten_password.txt.twig')
                    ->context([
                        'link' => $url
                    ]);
    }

}