<?php

namespace App\lib\validators;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractValidator {
    private $validator;
    public function __construct()
    {
        $this->validator = Validation::createValidator();
    }
    abstract protected function getConstraints($param): Collection;
    public function validate(array $requests): array {
        $err = [];
        foreach ($this->validator->validate($requests, $this->getConstraints($requests)) as $violation) {
            $field = preg_replace(['/\]\[/', '/\[|\]/'], ['.', ''], $violation->getPropertyPath());
            $err[$field] = $violation->getMessage();
        }
        return $err;
    }
}

