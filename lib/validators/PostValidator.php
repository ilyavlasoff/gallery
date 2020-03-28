<?php

namespace App\lib\validators;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

class PostValidator extends AbstractValidator
{

    protected function GetConstraints(): Collection
    {
        return new Collection([
            'photo' => $this->getFileRules(),
            'comment' => $this->getPasswdRules()
        ]);
    }

    private function getFileRules(): array
    {
        return [
            new Assert\NotBlank([
                'message' => 'Add png, jpg or jpeg picture'
            ]),
            new Assert\File([
                'maxSize' => '3M',
                'maxSizeMessage' => '{{ name }} Max file size - {{ limit }} {{ suffix }}',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/jpg'
                ],
                'mimeTypesMessage' => "{{ name }} is not acceptable type. Add png, jpg or jpeg file",
                'uploadErrorMessage' => "Error file loading"
            ])
        ];
    }

    private function getPasswdRules(): array
    {
        return [
            new Assert\Regex([
                'pattern' => '/^[ ]+$/ui',
                'match' => false,
                'message' => 'Blanks are not allowed'
            ])
        ];
    }
}