<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MarvelApiService
{
    public function getMarvelHeroes()
    {
        $cacheKey = 'marvelHeroes.all';

        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $publicKey = env('MARVEL_PUBLIC_KEY');
        $privateKey = env('MARVEL_PRIVATE_KEY');
        $ts = time();
        $stringToHash = $ts . $privateKey . $publicKey;
        $hash = md5($stringToHash);
        $basePath = env('MARVEL_BASE_PATH') . '/characters';
        $offset = 0;
        $limit = 100;
        $heroes = [];

        do {
            $params = [
                'ts' => $ts,
                'apikey' => $publicKey,
                'hash' => $hash,
                'limit' => $limit,
                'offset' => $offset
            ];

            $url = url($basePath) . '?' . http_build_query($params);

            $response = Http::get($url);
            $responseData = $response->json()['data'] ?? [];

            $heroes = array_merge($heroes, $responseData['results'] ?? []);

            $offset += $limit;

        } while ($offset < $responseData['total']);

        cache()->put($cacheKey, $heroes);

        return $heroes;
    }
}
