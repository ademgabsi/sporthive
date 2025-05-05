<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
    private function sendSms(Reservation $reservation): void
    {
        // Configuration Twilio
        $sid = $_ENV['TWILIO_SID'];
        $authToken = $_ENV['TWILIO_AUTH_TOKEN'];
        $twilioNumber = $_ENV['TWILIO_PHONE_NUMBER'];

        // Créer un client Twilio
        $client = new Client($sid, $authToken);

        // Message SMS
        $smsMessage = sprintf(
            "Réservation confirmée pour le terrain %s le %s.",
            $reservation->getTerrain()->getNom(),
            $reservation->getDateHeure()->format('Y-m-d H:i')
        );

        // Envoi du SMS à l'utilisateur
        $client->messages->create(
            $reservation->getPhoneNumber(), // Utiliser le phoneNumber directement de la réservation
            [
                'from' => $twilioNumber,
                'body' => $smsMessage,
            ]
        );
    }
    // Tu peux ajouter des méthodes personnalisées ici
}
