<?php

namespace App\Tests;

use App\DataFixtures\TestFixtures;
use App\Repository\ArticleRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleRepositoryTest extends KernelTestCase{

    /**
     * @var AbstractDatabaseTool
     */
    protected AbstractDatabaseTool $databaseTool;

    protected ArticleRepository $articleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->articleRepository = static::getContainer()->get(articleRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            TestFixtures::class
        ]);
    }

    /**
     * @test
     */
    public function findAllWithPagination() {
        // without pagination
        $articles = $this->articleRepository->findAllWithPagination(0,0);
        $this->assertEquals(6, count($articles));

        // test for limit
        $articles = $this->articleRepository->findAllWithPagination(1,3);
        $this->assertEquals(3, count($articles));

        // test getting the right article
        $articles = $this->articleRepository->findAllWithPagination(2,3);
        $label = $articles[0]->getLabel();
        $this->assertEquals("article_4", $label);
    }

}
