<?php

namespace App\lib\validators;

use http\Message;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class LoginValidator extends AbstractValidator {

    protected function GetConstraints(): Collection {
        return new Collection([
            'login' => $this->getLoginRules(),
            'passwd' => $this->getPasswdRules(),
            'submit' => $this->getSubmitRules()
        ]);
    }

    private function getLoginRules(): array {
        return [
            new Assert\NotBlank([
                'message' => 'Email can not be empty'
            ]),
            new Assert\Email([
                'message' => 'The email {{ value }} is not valid'
            ])
        ];
    }

    private function getPasswdRules(): array {
        return [
            new Assert\NotBlank([
                'message' => 'Field can not be empty'
            ]),
            new Assert\Regex([
                'pattern' => '/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/',
                'message' => 'Password may contains A-Z,a-z words, digits, symbols. Length 8+ symbols'
            ]),
            new Assert\NotCompromisedPassword([
                'message' => 'This password may be compromissed. Please change it'
            ])
        ];
    }

    private function getSubmitRules(): array {
        return [
            new Assert\Blank([
                'message' => 'Click submit button'
            ])
        ];
    }
}