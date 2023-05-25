<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MarvelApiService
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getMarvelHeroes(int | null $limit = 30, int | null $page = 1, int | null $id = null)
    {
        $cacheKey = 'marvelHeroes.' . $limit . '.' . $page . '.' . $id;

        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        $publicKey = env('MARVEL_PUBLIC_KEY');
        $privateKey = env('MARVEL_PRIVATE_KEY');
        $ts = time();
        $stringToHash = $ts . $privateKey . $publicKey;
        $hash = md5($stringToHash);
        $basePath = env('MARVEL_BASE_PATH') . '/characters';
        $offset = ($page - 1);

        $params = [
            'ts' => $ts,
            'apikey' => $publicKey,
            'hash' => $hash,
            'limit' => $limit,
            'offset' => $offset
        ];

        if ($id !== null) {
            $params['id'] = $id;
        }

        $url = url($basePath) . '?' . http_build_query($params);

        $response = Http::get($url);
        $data = $response->json()['data'] ?? [];

        cache()->put($cacheKey, $data, 1440);

        return $data;
    }
}
