<?php

namespace App\Service;

use App\Entity\Reservation;
use Twilio\Rest\Client;

class SmsService
{
    private string $sid;
    private string $authToken;
    private string $twilioNumber;

    public function __construct(string $sid, string $authToken, string $twilioNumber)
    {
        $this->sid = $sid;
        $this->authToken = $authToken;
        $this->twilioNumber = $twilioNumber;
    }

    public function sendReservationSms(Reservation $reservation): void
    {
        $client = new Client($this->sid, $this->authToken);

        $message = "Votre réservation pour le terrain '" 
            . $reservation->getTerrain()->getNom() 
            . "' est confirmée pour le " 
            . $reservation->getDateHeure()->format('d/m/Y H:i') . ".";

        $client->messages->create(
            '+21655022717', 
            [
                'from' => $this->twilioNumber,
                'body' => $message
            ]
        );
    }
}
