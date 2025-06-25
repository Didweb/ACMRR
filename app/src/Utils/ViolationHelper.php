<?php
namespace App\Utils;

use App\Exception\ValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ViolationHelper
{
    public static function add(ConstraintViolationList $list, string $message, string $propertyPath, $invalidValue = null): void
    {
        $list->add(new ConstraintViolation(
            $message,
            null,
            [],
            null,
            $propertyPath,
            $invalidValue
        ));
    }

    public static function result($violations): void
    {
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }
    }
}