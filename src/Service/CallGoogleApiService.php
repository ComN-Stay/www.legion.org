<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallGoogleApiService
{
    private $client;
    private $googleApiKey;

    public function __construct(HttpClientInterface $client, $googleApiKey)
    {
        $this->client = $client;
        $this->googleApiKey = $googleApiKey;
    }

    public function getGeolocalization($address): array
    {
        $response = $this->client->request(
            'GET',
            'https://maps.googleapis.com/maps/api/geocode/json?address=' . $address  . '&key=' . $this->googleApiKey
        );

        return $response->toArray();
    }
}