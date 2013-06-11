<?php

namespace Pim\Bundle\ProductBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\File as FileObject;
use Symfony\Component\Validator\Constraints\FileValidator as BaseFileValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author    Gildas Quemener <gildas.quemener@gmail.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FileValidator extends BaseFileValidator
{
    public function validate($value, Constraint $constraint)
    {
        parent::validate($value, $constraint);

        if ($constraint->allowedExtensions) {
            $file = !$value instanceof FileObject ? new FileObject($value) : $value;
            if (!in_array($file->getExtension(), $constraint->allowedExtensions)) {
                $this->context->addViolation($constraint->allowedExtensionsMessage, array(
                    '{{ extensions }}' => $constraint->allowedExtensions,
                ));
            }
        }
    }
}

