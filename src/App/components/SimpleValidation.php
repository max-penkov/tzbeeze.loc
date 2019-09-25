<?php

namespace App\components;


use DomainException;

/**
 * Class Validation
 * @package App\components
 */
class SimpleValidation
{
    public function validate(array $items)
    {
        foreach ($items as $k => $value) {
            // check empty fields
            if ($value == '') {
                throw new DomainException("{$k} must be filled");
            }
            if (($k == 'email') && (!filter_var($value, FILTER_VALIDATE_EMAIL))) {
                throw new DomainException("{$value} is not a valid");
            }
        }
    }
}