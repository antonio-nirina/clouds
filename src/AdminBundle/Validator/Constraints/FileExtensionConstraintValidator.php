<?php
namespace AdminBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class FileExtensionConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!is_null($value)) {
            if ($value->getClientOriginalExtension() != $constraint->getExtension()) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}
