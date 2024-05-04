<?php

namespace App\DataFixtures;

use App\Entity\Warranty;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WarrantyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $warranty = new Warranty();
        $warranty->setCategory('Phone');
        $warranty->setProductName('iPhone 14 Pro');

        $date = new DateTimeImmutable();
        $date1 = $date->setDate(2020, 10, 15);

        $warranty->setPurchaseDate($date1);
        $warranty->setWarrantyPeriod(2);
        $warranty->setReceipt("receipt.jpg");

        $date1 = $date->setDate(2022, 10, 15);
        $warranty->setWarrantyEndDate($date1);
        $warranty->setActive(1);
        $warranty->setIdUser(2);
        $warranty->addTag($this->getReference('tag_1'));
        $warranty->addTag($this->getReference('tag_3'));

        $manager->persist($warranty);

        $warranty2 = new Warranty();
        $warranty2->setCategory('Bike');
        $warranty2->setProductName('Kross Evado 2.0');
        

        $date2 = new DateTimeImmutable();
        $date3 = $date2->setDate(2019, 1, 17);

        $warranty2->setPurchaseDate($date3);
        $warranty2->setWarrantyPeriod(5);
        $warranty2->setReceipt("receipt.jpg");

        $date4 = $date2->setDate(2024, 1, 17);
        $warranty2->setWarrantyEndDate($date4);
        $warranty2->setActive(0);
        $warranty2->setIdUser(3);
        $warranty2->addTag($this->getReference('tag_2'));
        $warranty2->addTag($this->getReference('tag_3'));

        $manager->persist($warranty2);

        $manager->flush();
    }
}
