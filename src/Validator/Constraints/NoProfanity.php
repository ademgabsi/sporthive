<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
class NoProfanity extends Constraint
{
    public $message = 'Le texte contient des mots inappropriés: {{ words }}';
    
    public function __construct(string $message = null, array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
        
        if ($message !== null) {
            $this->message = $message;
        }
    }
    
    public function validatedBy()
    {
        return NoProfanityValidator::class;
    }
}
