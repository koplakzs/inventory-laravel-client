<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PaginatedResource;
use App\Interfaces\CategoryRepositoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $cateogries = $this->categoryRepository->getAll($request->search, $request->limit, true);
            return ResponseHelper::jsonResponse(true, 'Kategori Berhasil Diambil', CategoryResource::collection($cateogries), 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    public function getAllPaginated(Request $request)
    {
        $request = $request->validate([
            'search' => 'nullable|string',
            'row_per_page' => 'required|integer'
        ]);
        try {
            $cateogries = $this->categoryRepository->getAllPaginated($request['search'] ?? null, $request['row_per_page']);
            return ResponseHelper::jsonResponse(true, 'Kategori Berhasil Diambil', PaginatedResource::make($cateogries,  CategoryResource::class), 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $request = $request->validated();

        try {
            $cateogry = $this->categoryRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Kategori berhasil ditambahkan', new CategoryResource($cateogry), 201);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //     try {
        //         $cateogry = $this->categoryRepository->getById($id);
        //         if (!$cateogry) {
        //             return ResponseHelper::jsonResponse(false, 'Kategori tidak ditemukan', null, 404);
        //         }
        //         return ResponseHelper::jsonResponse(true, 'Kategori ditemukan', new CategoryResource($cateogry), 200);
        //     } catch (\Throwable $th) {
        //         return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        //         //throw $th;
        //     }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $cateogry = $this->categoryRepository->getById($id);
            if (!$cateogry) {
                return ResponseHelper::jsonResponse(false, 'Kategori tidak ditemukan', null, 404);
            }
            $cateogry = $this->categoryRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Kategori berhasil di update', new CategoryResource($cateogry), 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $cateogry = $this->categoryRepository->getById($id);
            if (!$cateogry) {
                return ResponseHelper::jsonResponse(false, 'Kategori tidak ditemukan', null, 404);
            }

            $this->categoryRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Kategori berhasil di hapus', null, 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }
}
