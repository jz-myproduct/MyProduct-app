<?php


namespace App\DataFixtures;


use App\Entity\Company;
use App\Entity\Feedback;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FeedbackFixtures extends Fixture
{

    private static $description
        = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi scelerisque ipsum mi, at 
    dapibus risus auctor in. Pellentesque ac facilisis dui, in dictum odio. Nunc ac tellus id erat feugiat blandit.
     Nullam pharetra pellentesque ante at dapibus. Phasellus non luctus felis. Etiam vel mi auctor, hendrerit lacus aliquam, 
     feugiat nulla. Donec ut sem condimentum, rhoncus quam et, finibus quam. Aenean congue blandit gravida. Curabitur blandit 
     pellentesque commodo.';

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {

        foreach ($this->getData() as $data)
        {
            $feedback = new Feedback();
            $feedback->setDescription(self::$description);
            $feedback->setSource($data['source']);
            $feedback->setCreatedAt(new \DateTime());
            $feedback->setUpdatedAt(new \DateTime());
            $feedback->setIsNew(rand(0,1));
            $feedback->setFromPortal(rand(0,1));
            $feedback->setCompany($this->getReference('company-'.strtolower($data['company'])));

            $manager->persist($feedback);
            $this->setReference('feedback-'.strtolower($data['company']).'-'.strtolower($data['source']), $feedback);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            Company::class,
        ];
    }


    public function getData()
    {
        return [
            ['company' => 'Microsoft', 'source' => 'Honza'],
            ['company' => 'Microsoft', 'source' => 'Marek'],
            ['company' => 'Microsoft', 'source' => 'Filip'],
            ['company' => 'Microsoft', 'source' => 'Lukas'],
            ['company' => 'Microsoft', 'source' => 'Tereza'],
            ['company' => 'Microsoft', 'source' => 'Dominika'],
            ['company' => 'Apple', 'source' => 'Honza'],
            ['company' => 'Apple', 'source' => 'Marek'],
            ['company' => 'Apple', 'source' => 'Filip'],
            ['company' => 'Apple', 'source' => 'Lukas'],
            ['company' => 'Apple', 'source' => 'Tereza'],
            ['company' => 'Apple', 'source' => 'Dominika'],
        ];
    }


}