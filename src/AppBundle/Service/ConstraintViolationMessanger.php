<?php

namespace AppBundle\Service;

use AppBundle\Exception\ResourceValidationException;
use Symfony\Component\Validator\ConstraintViolationList;

class ConstraintViolationMessanger
{

    public function messageDisplay(ConstraintViolationList $violations)
    {
        if (count($violations)) {
            $message = 'The JSON sent contains invalid data. Here are the errors you need to correct: ';
            foreach ($violations as $violation) {
                $message .= sprintf("Field %s: %s ", $violation->getPropertyPath(), $violation->getMessage());
            }
            
            throw new ResourceValidationException($message);
        }
    }
}
