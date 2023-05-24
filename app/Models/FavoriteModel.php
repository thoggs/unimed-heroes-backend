<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FavoriteModel extends Model
{
    use HasFactory;
    protected $fillable = ['hero_id', 'votes'];
    protected $guarded = ['id'];

    private function mountResponse(?array $marvelHeroes, ?int $heroId = null): array
    {
        $favoriteHeroes = [];
        $offset = 0;
        $limit = 0;
        $total = 0;
        $count = 0;

        if ($marvelHeroes !== null && isset($marvelHeroes['results'])) {
            $offset = $marvelHeroes['offset'] + 1;
            $limit = $marvelHeroes['limit'];
            $total = $marvelHeroes['total'];
            $count = $marvelHeroes['count'];

            foreach ($marvelHeroes['results'] as $marvelHero) {
                $id = $marvelHero['id'];
                $heroName = $marvelHero['name'];
                $description = $marvelHero['description'];
                $thumbnailPath = $marvelHero['thumbnail']['path'];
                $thumbnailExtension = $marvelHero['thumbnail']['extension'];
                $thumbnailUrl = $thumbnailPath . '.' . $thumbnailExtension;

                if ($id === $heroId) {
                    $favoriteHero = $this::all()->firstWhere('hero_id', $heroId);

                    if ($favoriteHero) {
                        $votes = $favoriteHero->votes;
                    } else {
                        $votes = 0;
                    }

                    return [
                        'id' => $id,
                        'name' => $heroName,
                        'thumbnail' => $thumbnailUrl,
                        'votes' => $votes,
                        'description' => $description,
                    ];
                }

                $favoriteHero = $this::all()->firstWhere('hero_id', $id);

                if ($favoriteHero) {
                    $votes = $favoriteHero->votes;
                } else {
                    $votes = 0;
                }

                $favoriteHeroes[] = [
                    'id' => $id,
                    'name' => $heroName,
                    'thumbnail' => $thumbnailUrl,
                    'votes' => $votes,
                    'description' => $description,
                ];

                usort($favoriteHeroes, function ($a, $b) {
                    return $b['votes'] - $a['votes'];
                });
            }
        }

        return [
            'page' => $offset,
            'limit' => $limit,
            'total' => $total,
            'count' => $count,
            'data' => $favoriteHeroes
        ];
    }

    public function combineWithMarvelHeroes(array $marvelHeroes): array
    {
        return $this->mountResponse($marvelHeroes);
    }

    public function getHero(array $marvelHeroes, int $heroId): array
    {
        return $this->mountResponse($marvelHeroes, $heroId);
    }

    public function saveFavorite(int $heroId): bool
    {
        $favoriteHero = $this::all()->firstWhere('hero_id', $heroId);

        if ($favoriteHero) {
            $favoriteHero->votes++;
            $favoriteHero->save();
        } else {
            DB::table($this->getTable())->insert([
                'hero_id' => $heroId,
                'votes' => 1
            ]);
        }

        return true;
    }
}
