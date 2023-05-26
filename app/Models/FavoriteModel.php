<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FavoriteModel extends Model
{
    use HasFactory;

    protected $fillable = ['hero_id', 'votes'];
    protected $guarded = ['id'];

    private function mountResponse(?array $marvelHeroes): array
    {
        $favoriteHeroes = [];

        if ($marvelHeroes !== null) {
            foreach ($marvelHeroes as $marvelHero) {
                $id = $marvelHero['id'];
                $heroName = $marvelHero['name'];
                $description = $marvelHero['description'];
                $thumbnailPath = $marvelHero['thumbnail']['path'];
                $thumbnailExtension = $marvelHero['thumbnail']['extension'];
                $thumbnailUrl = $thumbnailPath . '.' . $thumbnailExtension;

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
            }

            usort($favoriteHeroes, function ($a, $b) {
                return $b['votes'] - $a['votes'];
            });
        }

        return $favoriteHeroes;
    }

    private function paginateHeroes(array $favoriteHeroes, int $perPage, int $currentPage): LengthAwarePaginator
    {
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($favoriteHeroes, $offset, $perPage);
        $total = count($favoriteHeroes);

        return new LengthAwarePaginator(
            new Collection($items),
            $total,
            $perPage,
            $currentPage,
            ['path' => url()->current()]
        );
    }

    public function combineWithMarvelHeroes(array $marvelHeroes, int $currentPage, int $perPage): LengthAwarePaginator
    {
        $favoriteHeroes = $this->mountResponse($marvelHeroes);
        return $this->paginateHeroes($favoriteHeroes, $perPage, $currentPage);
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
