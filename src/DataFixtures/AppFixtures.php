<?php

namespace App\DataFixtures;


use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Incredient;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $hasher;
    public function __construct( )
    {
        $this->faker = Factory::create('fr-FR');
      
    }
    public function load(ObjectManager $manager): void
    {
        //Users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User;
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) == 1 ? $this->faker->firstName() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');
            $users[] = $user;
            $manager->persist($user);
        }
        //ingredients
        $ingredients = [];
        for ($i = 0; $i<50;$i++){
            $incredient = new Incredient();
            $incredient->setName($this->faker->word())
                        ->setPrice(mt_rand(1,100));
            $incredient->setUser($users[mt_rand(0,count($users) - 1)]);
            $ingredients[] = $incredient;
            $manager->persist($incredient);
        }
        
        //recettes
        for($j=0;$j<25;$j++){
            $recipe = new Recipe;
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0,1) == 1 ? mt_rand(1,1440) : null)
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : 0)
                ->setDescription($this->faker->text(300))
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null);
           for($k = 0 ; $k < mt_rand(5,15); $k++)
               { $recipe->addIngredient($ingredients[mt_rand(0,count($ingredients)-1)]);
           };
            $recipe->setUser($users[mt_rand(0, count($users) - 1)]);
          
            $manager->persist($recipe);
        }

        
     
        $manager->flush();
    }
}
