<?php

namespace App\lib\validators;

use http\Message;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class AuthorizeValudator extends AbstractValidator {

    protected function GetConstraints(): Collection {
        return new Collection([
            'login' => $this->getLoginRules(),
            'username' => $this->getUsernameRules(),
            'nick' => $this->getNickRules(),
            'passwd' => $this->getPasswdRules(),
            'passwdRep' => $this->getPasswdRepRules(),
            'rulesAgree' => $this->getConfRules(),
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

    private function getUsernameRules(): array {
        return [
            new Assert\NotBlank([
                'message' => 'Field can not be empty'
            ]),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Zа-яёА-ЯЁ\s\-]+$/u',
                'message' => 'Input real first name and last name'
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

    private function getPasswdRepRules(): array {
        return [
            new Assert\Regex([
                'pattern' => '/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/',
                'message' => 'Password may contains A-Z,a-z words, digits, symbols. Length 8+ symbols'
            ]),
        ];
    }

    private function getNickRules(): array {
        return [
            new Assert\NotBlank([
                'message' => 'Field can not be empty'
            ]),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$/',
                'message' => 'Nickname must contains 2-20 symbols, first symbol is word'
            ])
        ];
    }

    private function getConfRules(): array {
        return [
            new Assert\EqualTo([
                'value' => true,
                'message' => 'You should confirm our terms and conditions of use'
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