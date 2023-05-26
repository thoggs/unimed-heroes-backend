<?php

namespace App\Http\Controllers;

use App\Models\FavoriteModel;
use App\Services\MarvelApiService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class HeroController extends Controller
{
    protected MarvelApiService $marvelApiService;
    protected FavoriteModel $favoriteModel;

    public function __construct(MarvelApiService $marvelApiService, FavoriteModel $favoriteModel)
    {
        $this->marvelApiService = $marvelApiService;
        $this->favoriteModel = $favoriteModel;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page');
            $currentPage = $request->input('current_page');

            $marvelHeroes = $this->marvelApiService->getMarvelHeroes();
            $combinedHeroes = $this->favoriteModel->combineWithMarvelHeroes($marvelHeroes, $currentPage, $perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Heroes retrieved successfully',
                'model' => $combinedHeroes
            ], ResponseAlias::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred',
                'error' => $e->getMessage()
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $heroId = $request->input('id');
            $saved = $this->favoriteModel->saveFavorite($heroId);

            if ($saved) {
                return response()->json([
                    'code' => 201,
                    'message' => 'Favorite saved successfully',
                    'data' => []
                ], 201);
            } else {
                return response()->json([
                    'code' => 500,
                    'message' => 'Failed to save favorite',
                    'data' => []
                ], 500);
            }
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
}
