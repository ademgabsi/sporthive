<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReCaptchaValidator
{
    private $httpClient;
    private $params;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params)
    {
        $this->httpClient = $httpClient;
        $this->params = $params;
    }

    public function verify(string $recaptchaResponse, string $remoteIp = null): bool
    {
        if (empty($recaptchaResponse)) {
            return false;
        }

        $response = $this->httpClient->request(
            'POST',
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'body' => [
                    'secret' => $this->params->get('google_recaptcha_secret_key'),
                    'response' => $recaptchaResponse,
                    'remoteip' => $remoteIp,
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            return false;
        }

        $content = $response->toArray();
        return $content['success'] ?? false;
    }
}
