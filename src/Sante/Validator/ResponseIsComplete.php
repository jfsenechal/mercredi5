<?php

namespace AcMarche\Mercredi\Sante\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
final class ResponseIsComplete extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ value }}" is not valid.';
    public $message_question = '{{ string }} (Indiquez la réponse dans le champ remarque à côté de la question)';

    /**
     * @return array|string
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
