<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ProductResource;
use App\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $products = $this->productRepository->getAll($request->search, $request->limit, true);

            return ResponseHelper::jsonResponse(true, 'Produk Berhasil Diambil', ProductResource::collection($products), 200);
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
            $products = $this->productRepository->getAllPaginated($request['search'] ?? null, $request['row_per_page']);

            return ResponseHelper::jsonResponse(true, 'Produk Berhasil Diambil', PaginatedResource::make($products,  ProductResource::class), 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductStoreRequest $request)
    {
        $request = $request->validated();

        try {
            $product = $this->productRepository->create($request);
            return ResponseHelper::jsonResponse(true, 'Produk berhasil ditambahkan', new ProductResource($product), 201);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // try {
        //     $product = $this->productRepository->getById($id);
        //     if (!$product) {
        //         return ResponseHelper::jsonResponse(false, 'Produk tidak ditemukan', null, 404);
        //     }
        //     return ResponseHelper::jsonResponse(true, 'Produk ditemukan', new ProductResource($product), 200);
        // } catch (\Throwable $th) {
        //     return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        //     //throw $th;
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductUpdateRequest $request, string $id)
    {
        $request = $request->validated();
        try {
            $product = $this->productRepository->getById($id);
            if (!$product) {
                return ResponseHelper::jsonResponse(false, 'Produk tidak ditemukan', null, 404);
            }

            $product = $this->productRepository->update($id, $request);

            return ResponseHelper::jsonResponse(true, 'Produk berhasil di update', new ProductResource($product), 200);
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
            $product = $this->productRepository->getById($id);
            if (!$product) {
                return ResponseHelper::jsonResponse(false, 'Produk tidak ditemukan', null, 404);
            }

            $this->productRepository->delete($id);

            return ResponseHelper::jsonResponse(true, 'Produk berhasil di hapus', null, 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }
}
