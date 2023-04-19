<?php

namespace App\Tests\Validator;
use App\Tests\Validator\ValidatorMocked;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use App\DataFixtures\TestFixtures;
use App\Validator\ArticleCategoryValidator;
use App\Validator\ArticleCategory;
use App\Entity\Category;

class ArticleCategoryValidatorTest extends ValidatorMocked
{

    /**
     * @var AbstractDatabaseTool
     */
    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([
            TestFixtures::class
        ]);
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->categoryRepo = $this->em->getRepository(Category::class);
    }

    /**
     * @test
     * category does not exist
     */
    public function singleAssignmentNotExistingCategory() {
        $dataEntryIsValid = false;
        $context = $this->getMockedValidationContext($dataEntryIsValid);
         $this->playArticleCategoryValidator($context, "notExisting");
    }

    /**
     * @test
     * existing category
     */
    public function singleAssignmentMaxUserIsNotReached() {
        $dataEntryIsValid = true;
        $category1 = $this->categoryRepo->findOneBy(['name' => 'category_1']);
        $context = $this->getMockedValidationContext($dataEntryIsValid);
        $this->playArticleCategoryValidator($context, $category1);
    }

    /**
     * call to the validate method of ArticleCategoryValidator
     * @param ExecutionContextInterface $context
     * @param string|null $value
     */
    private function playArticleCategoryValidator(ExecutionContextInterface $context, string | Category $value) {
        $validator = new ArticleCategoryValidator($this->categoryRepo);
        $validator->initialize($context);
        $validator->validate($value, ( new ArticleCategory() ) );
    }

}
