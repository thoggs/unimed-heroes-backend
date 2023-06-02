<?php

namespace App\Console\Commands;

use App\Services\MarvelApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class FetchMarvelHeroes extends Command
{
    protected $signature = 'marvel:fetch-heroes';
    protected $description = 'Fetches Marvel heroes and stores them in cache';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $cacheKey = 'marvelHeroes.all';
        $marvelApiService = new MarvelApiService();

        $heroes = $marvelApiService->getMarvelHeroes();

        $expiration = Carbon::now()->endOfDay();

        cache()->put($cacheKey, $heroes, $expiration);

        $this->info('Marvel heroes fetched and stored in cache.');
    }
}
