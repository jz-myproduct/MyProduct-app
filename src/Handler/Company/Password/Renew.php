<?php


namespace App\Handler\Company\Password;


use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class Renew
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

    public function __construct(
        EntityManagerInterface $manager,
        RouterInterface $router,
        MailerInterface $mailer,
        $fromMail)
    {
        $this->manager = $manager;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->fromMail = $fromMail;
    }

    public function handle(Company $company)
    {
        $company->setPasswordRenewHash(
            uuid_create(UUID_TYPE_RANDOM)
        );

        $company->setPasswordHashValidUntil(
            (new \DateTime())->add( new \DateInterval('PT1H'))
        );

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

        return (new TemplatedEmail())
                    ->from($this->fromMail)
                    ->to($company->getEmail())
                    ->subject('Obnovení hesla')
                    ->htmlTemplate('email/forgotten_password.html.twig')
                    ->textTemplate('email/forgotten_password.txt.twig')
                    ->context([
                        'link' => $url
                    ]);
    }

}