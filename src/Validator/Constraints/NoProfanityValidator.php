<?php

namespace App\Validator\Constraints;

use App\Service\PurgoMalumService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoProfanityValidator extends ConstraintValidator
{
    private $purgoMalumService;

    public function __construct(PurgoMalumService $purgoMalumService)
    {
        $this->purgoMalumService = $purgoMalumService;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NoProfanity) {
            throw new UnexpectedTypeException($constraint, NoProfanity::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // Vérifier si le texte contient des mots inappropriés
        if ($this->purgoMalumService->containsProfanity($value)) {
            // Récupérer la liste des mots inappropriés
            $profanityList = $this->purgoMalumService->getProfanityList($value);
            
            // Ajouter la violation avec la liste des mots inappropriés
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ words }}', implode(', ', $profanityList))
                ->addViolation();
        }
    }
}
