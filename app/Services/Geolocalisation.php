<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Geolocalisation
{
    /**
     * Transforme une adresse en coordonnées GPS (latitude et longitude)
     *
     * @param string $address
     * @return array
     */
    public function geocode(string $address): array
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY'); 
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        
        $response = Http::get($url, [
            'address' => $address,
            'key' => $apiKey
        ]);

        $data = $response->json();

        // DEBUG : voir le status et le message d'erreur éventuel
        if (!empty($data)) {
            info('Google Geocode response: ', $data); // écrit dans laravel.log
        }

        if (($data['status'] ?? null) !== 'OK') {
            // Tu peux utiliser dd() temporairement pour debugger
            // dd([
            //     'status' => $data['status'] ?? null,
            //     'error_message' => $data['error_message'] ?? null,
            //     'address' => $address
            // ]);
        }

        if (isset($data['results'][0]['geometry']['location'])) {
            $location = $data['results'][0]['geometry']['location'];
            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng']
            ];
        }

        return [
            'latitude' => null,
            'longitude' => null
        ];
    }
}
