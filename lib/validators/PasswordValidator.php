<?php

namespace App\lib\validators;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class PasswordValidator extends AbstractValidator {

    protected function GetConstraints($param): Collection {
        return new Collection([
            'passwd' => $this->getPasswdRules(),
            'duplicate' => $this->getDuplicateRules($param),
            'old' => $this->getOldPasswdRules($param)
        ]);
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

    private function getDuplicateRules($param): array {
        return [
            new Assert\NotBlank([
                'message' => 'Field can not be empty'
            ]),
            new Assert\EqualTo([
                'value' => $param['passwd'],
                'message' => 'Duplicate password is not equal to first'
            ])
        ];
    }

    private function getOldPasswdRules($param): array {
        return [
            new Assert\NotBlank([
                'message' => 'Field can not be empty'
            ]),
            new Assert\NotEqualTo([
                'value' => $param['passwd'],
                'message' => 'New password can not be as same as old'
            ])
        ];
    }
}