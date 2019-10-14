<?php

namespace AppBundle\Service\ExternalAPI;

use GuzzleHttp\Client;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;

class Weather
{
    private $weatherClient;
    private $serializer;
    private $apiKey;

    public function __construct(Client $weatherClient, Serializer $serializer, $apiKey)
    {
        $this->weatherClient = $weatherClient;
        $this->serializer = $serializer;
        $this->apiKey = $apiKey;
    }

    public function getCurrent($city)
    {
        if(!$city)
        {
            return ['error' => 'Need a target city to display weather.'];
        }
        $uri = '/data/2.5/weather?q='. $city .'&APPID='.$this->apiKey;

        try {
            $response = $this->weatherClient->get($uri);
        } catch (\Exception $e) {
            // Penser à logger l'erreur.
            
            return ['error' => 'Les informations ne sont pas disponibles pour le moment.'];
        }
        
        $response = $this->weatherClient->get($uri);

        $data = $this->serializer->deserialize($response->getBody()->getContents(), 'array', 'json');

        return [
            'city' => $data['name'],
            'description' => $data['weather'][0]['main']
        ];
    }
}