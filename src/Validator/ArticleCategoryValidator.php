<?php

namespace App\Validator;

use App\Entity\Category;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Repository\CategoryRepository;

/**
 * Validation of the category given when posting an article
 */
class ArticleCategoryValidator extends ConstraintValidator
{

    /**
     * ArticleCategoryValidator constructor.
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        if (! $value instanceof Category ){
            $this->context->buildViolation($constraint->message)
            ->addViolation();
        }
    }

}