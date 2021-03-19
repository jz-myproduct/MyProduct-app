<?php


namespace App\DataFixtures;


use App\Entity\Insight;
use App\Entity\InsightWeight;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class InsightWeightFixtures extends Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as $data)
        {
            $insightWeight = new InsightWeight();
            $insightWeight->setName($data['name']);
            $insightWeight->setNumber($data['number']);

            $manager->persist($insightWeight);
            $this->setReference('insightWeight-'.strtolower($data['name']), $insightWeight);
        }

        $manager->flush();
    }

    private function getData()
    {
        return [
            ['name' => 'Not important', 'number' => 1],
            ['name' => 'Nice-to-have', 'number' => 2],
            ['name' => 'Important', 'number' => 3],
            ['name' => 'Critical', 'number' => 4]
        ];
    }

}