<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;


class PhoneNumberValidator extends ConstraintValidator {

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * a simple rules
     * phonenumber must start either with a + or with 0
     * must consist of digits only
     * length between 5 and 24
     *
     * @param [type] $value
     * @param Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint) {
        try {
            if(empty($value)) return; // n'est pas censé vérifier le notBlank, il exist un autre validateur pour ça
            if (!preg_match("/^(\+|[0-9])[0-9]{5,24}$/", $value)){
                return $this->context
                ->buildViolation($this->translator->trans($constraint->message))
                ->addViolation()
                ;
            }
        }
        catch(\Exception $e){
            return $this->context
            ->buildViolation($this->translator->trans($constraint->message))
            ->addViolation()
            ;
        }
    }

}
