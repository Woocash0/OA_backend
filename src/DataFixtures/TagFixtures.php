<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tag = new Tag();
        $tag->setName('personal');
        $manager->persist($tag);

        $tag2 = new Tag();
        $tag2->setName('house');
        $manager->persist($tag2);

        $tag3 = new Tag();
        $tag3->setName('service');
        $manager->persist($tag3);

        $manager->flush();

        $this->addReference('tag_1', $tag);
        $this->addReference('tag_2', $tag2);
        $this->addReference('tag_3', $tag3);
    }
}
