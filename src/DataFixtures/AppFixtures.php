<?php

namespace App\DataFixtures;


use Faker\Factory;
use Faker\Generator;
use App\Entity\Incredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    private Generator $faker;
    public function __construct( )
    {
        $this->faker = Factory::create('fr-FR');
    }
    public function load(ObjectManager $manager): void
    {
        // for ($i = 0; $i<50;$i++){
        //     $incredient = new Incredient();
        //     $incredient->setName($this->faker->word())
        //                 ->setPrice(mt_rand(1,100));
        //     $manager->persist($incredient);
        // }
        
        $manager->flush();
    }
}
