<?php

namespace App\DataFixtures;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;
use Faker\Factory;



class AppFixtures extends Fixture
{
    private $encoder;
    private Generator $faker;
    private ObjectManager $manager;

    private array $data =
    [
        // "user" =>
        // [
        //     "1" => "user1@ssi-soft.fr",
        //     "2" => "user2@ssi-soft.fr",
        //     "3" => "user3@ssi-soft.fr"
        // ],
        "article" =>
        [
            "1" => "article_1",
            "2" => "article_2",
            "3" => "article_3"
        ],
        "category" =>
        [
            "1" => "category_1",
            "2" => "category_2",
            "3" => "category_3"
        ]
    ];

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Load data fixtures with the passed ObjectManager
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $this->manager = $manager;
        $this->faker = $faker;

        //$this->loadUser();
        $this->loadCategory();
        $this->loadArticle();

    }

    /*
     * for entity User - load a set of data into a database that can then be used for testing
     */
    // private function loadUser(): void
    // {
    //     $users = $this->data["user"];
    //     foreach ($users as $userData) {

    //         $user = new User();
    //         $user->setEmail($userData);

    //         $user->setFirstName($this->faker->firstName());
    //         $user->setLastName($this->faker->lastName());
    //         $password = $this->passwordHasher->hashPassword($user, '123456');
    //         $user->setAgreeCguAt((new \DateTimeImmutable()));
    //         $user->setAgreePrivacyAt((new \DateTimeImmutable()));
    //         $user->setPassword($password);
    //         $roles[] = 'ROLE_USER_FRONT';
    //         $user->setRoles($roles);
    //         $user->setPhoneNumber($this->faker->phoneNumber());
    //         $this->manager->persist($user);
    //     }
    //     $this->manager->flush();
    // }

    /*
     * for entity Article - load a set of data into a database that can then be used for testing
     */
    private function loadArticle(): void
    {
        $articles = $this->data["article"];
        foreach ($articles as $key => $articleData) {

            $article = new Article();
            $article->setLabel($articleData);

            $color = $this->faker->colorName();
            $article->setColor($color);
            $article->setPrice($this->faker->randomNumber(2));
            $date = $this->faker->datetime();
            $article->setCreatedAt(\DateTimeImmutable::createFromMutable( $date ));

            $repoCategory = $this->manager->getRepository("App\Entity\Category");
            $category = $repoCategory->findOneBy(['name' => sprintf('category_%s', $key)]);
            $article->setCategory($category);

            $this->manager->persist($article);
        }
        $this->manager->flush();
    }

    /*
     * for entity Category - load a set of data into a database that can then be used for testing
     */
    private function loadCategory(): void
    {
        $categories = $this->data["category"];
        foreach ($categories as $categoryData) {

            $category = new Category();
            $category->setName($categoryData);

            $category->setDescription($this->faker->realText($maxNbChars = 200, $indexSize = 2));
            $category->setLabel($this->faker->word);

            $this->manager->persist($category);
        }
        $this->manager->flush();
    }

}
