<?php

namespace App\Tests\Validator;

use App\Tests\Validator\ValidatorMocked;
use App\Validator\PhoneNumber;
use App\Validator\PhoneNumberValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Description of PhoneNumberValidatorTest
 *
 * @author mohammed
 */
class PhoneNumberValidatorTest extends ValidatorMocked
{

    protected static TranslatorInterface $translator;

    public static function setUpBeforeClass(): void
    {
        self::$translator = static::getContainer()->get(TranslatorInterface::class);

    }

    /**
     * @test
     */
    public function validPhoneNumber_validate() {

        // on s'attend à ce que les test soit réussit
        $validator = new PhoneNumberValidator( self::$translator );
        $validator->initialize($this->getMockedValidationContext(true));
        $validator->validate("+0033659125348", ( new PhoneNumber() ) );

        $validator2 = new PhoneNumberValidator( self::$translator );
        $validator2->initialize($this->getMockedValidationContext(true));
        $validator2->validate("0769234519", ( new PhoneNumber() ) );
    }

    /**
     * @test
     */
    public function misplacedPlus_validate() {

        $validator = new PhoneNumberValidator( self::$translator );
        $validator->initialize($this->getMockedValidationContext(false));
        // validate va se servir ou pas, du context avec le quel le validator est initialisé (initialisation pour remplacer l'injection)
        $validator->validate("0121245454+", ( new PhoneNumber() ) );
    }

    /**
     * @test
     */
    public function notAllowedCharacter_validate() {

        $validator = new PhoneNumberValidator( self::$translator );
        $validator->initialize($this->getMockedValidationContext(false));
        $validator->validate("#0145782356", ( new PhoneNumber() ) );
    }

    /**
     * @test
     */
    public function spaceKey_alidate() {

        $validator = new PhoneNumberValidator( self::$translator );
        $validator->initialize($this->getMockedValidationContext(false));
        $validator->validate("0 121 2454 54", ( new PhoneNumber() ) );
    }

    /**
     * @test
     */
    public function dashKey_validate() {

        $validator = new PhoneNumberValidator( self::$translator );
        $validator->initialize($this->getMockedValidationContext(false));
        $validator->validate("0-12-24-54-54", ( new PhoneNumber() ) );
    }

    /**
     * @test
     */
    public function alphabetical_validate() {

        $validator = new PhoneNumberValidator( self::$translator );
        $validator->initialize($this->getMockedValidationContext(false));
        $validator->validate("0a12245454", ( new PhoneNumber() ) );
    }

}

