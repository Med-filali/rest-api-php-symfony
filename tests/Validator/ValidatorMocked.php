<?php

namespace App\Tests\Validator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * Description of ValidatorMockedContext
 *
 * @author mohammed
 */
class ValidatorMocked extends KernelTestCase {

    /**
     * la comparaison se fait sur la correspondance $invokedCount - nombre d'appels
     * @param bool $dataIsValid
     * @return ExecutionContextInterface
     */
    protected function getMockedValidationContext(bool $dataIsValid, ?ExecutionContextInterface $context = null)
    {
        $invokedCount = ($dataIsValid) ? $this->never() : $this->atLeast(1);

        // on prépare le mocke qui va être retourné par la méthode buildViolation du mocke de ExecutionContextInterface
        $violation = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)->getMock();
        // le test réussira si la methode addViolation est appelée $invokedCount fois
        $violation->expects($invokedCount)
        ->method("addViolation");

        if(!$context instanceof ExecutionContextInterface){
            // mocker un objet de type ExecutionContextInterface
            $context = $this->getMockBuilder(ExecutionContextInterface::class)->getMock();
        }
        // le test réussira si la methode addViolation est appelée $invokedCount fois
        $context->expects($invokedCount)
             ->method("buildViolation")
             ->willReturn($violation);

        return $context;
    }

}
