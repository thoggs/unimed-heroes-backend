<?php

namespace App\Console\Commands;

use App\Services\MarvelApiService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Psr\SimpleCache\InvalidArgumentException;

class FetchMarvelHeroes extends Command
{
    protected $signature = 'marvel:fetch-heroes';
    protected CacheRepository $cache;
    protected $description = 'Fetches Marvel heroes and stores them in cache';

    public function __construct(CacheRepository $cache)
    {
        parent::__construct();
        $this->cache = $cache;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function handle(): void
    {
        $cacheKey = 'marvelHeroes.all';
        $marvelApiService = new MarvelApiService($this->cache);

        $heroes = $marvelApiService->getMarvelHeroes();

        $expiration = Carbon::now()->endOfDay();

        $this->cache->put($cacheKey, $heroes, $expiration);

        $this->info('Marvel heroes fetched and stored in cache.');
    }
}
