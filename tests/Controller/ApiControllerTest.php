<?php

namespace Tests\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\ArticleRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Response;


class ApiControllerTest extends WebTestCase
{

    /**
     * @var AbstractDatabaseTool
     */
    protected static AbstractDatabaseTool $databaseTool;
    protected static $token;
    protected static $articleRepository;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        self::$databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        self::$databaseTool->loadFixtures([
            TestFixtures::class
        ]);
        $em = $kernel->getContainer()->get('doctrine')->getManager();
        self::$articleRepository = $em->getRepository(Article::class);
    }

    /**
     * return a client with an authentication request
     * @param array $credentials
     * @return KernelBrowser
     */
    private function createClientForAuthentication(array $credentials) : KernelBrowser
    {
        self::ensureKernelShutdown();
        $client  = self::createClient();
        $client->request(
          'POST',
          '/api/login_check',
          [],
          [],
          ['CONTENT_TYPE' => 'application/json'],
          json_encode([
            'username' => $credentials["username"],
            'password' => $credentials["password"]
          ])
        );
        return $client;
    }

    /**
     * @test
     */
    public function connectionAction()
    {
        // Invalid credentials.
        $credentials = ["username" => "user1@my-super-api.com", "password" => "wrongPassword" ];
        $client = $this->createClientForAuthentication($credentials);
        $this->assertResponseStatusCodeSame(401);

        // Valid credentials.
        $credentials = ["username" => "user1@my-super-api.com", "password" => "123456" ];
        $client = $this->createClientForAuthentication($credentials);
        $this->assertTrue($client->getResponse()->isSuccessful());

        $data = json_decode($client->getResponse()->getContent(), true);
        self::$token = $data['token'];

    }

    /**
     * @test
     * @depends connectionAction
     */
    public function apiPostArticle()
    {
        $client  = self::createClient();

        $client->setServerParameter('HTTP_AUTHORIZATION', sprintf('Bearer %s', self::$token));

        // le cas réussit
        $client->request('POST','/api/articles', [], [], ['CONTENT_TYPE' => 'application/json'],
            json_encode([
              'label' => "article_new_new",
              'color' => "red ",
              'price' => 100,
              'created_at' => "2023-17-28T02:07:31+02:00",
              'idCategory' => "2",
            ])
          );
          // test the right response status code
          $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

          // find the new article
          $article = self::$articleRepository->findOneBy(['label' => 'article_new_new']);
          $this->assertInstanceOf(Article::class, $article);

    }

}