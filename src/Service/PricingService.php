<?php

namespace App\Service;

use App\Entity\Assurance;

class PricingService
{
    /**
     * Calculate the total price for an insurance based on its type and duration
     * 
     * @param Assurance $assurance
     * @return int The total price in cents
     */
    public function calculatePrice(Assurance $assurance): int
    {
        $prixBase = $this->getBasePriceForCoverageType($assurance->getTypeDeCouverture());
        return $prixBase * $assurance->getDuree();
    }

    /**
     * Get the base price for a specific coverage type
     * 
     * @param string $typeDeCouverture
     * @return int The base price in cents
     */
    private function getBasePriceForCoverageType(string $typeDeCouverture): int
    {
        switch ($typeDeCouverture) {
            case 'Basique':
                return 1000; // 10€ par mois
            case 'Standard':
                return 2000; // 20€ par mois
            case 'Premium':
                return 3000; // 30€ par mois
            default:
                return 1500; // Prix par défaut
        }
    }
}
