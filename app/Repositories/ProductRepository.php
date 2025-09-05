<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Product::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
        });
        $query->orderBy('created_at', 'desc');
        if ($limit) {
            $query->take($limit);
        }
        if ($execute) {
            return $query->get();
        }

        return $query;
    }

    public function getAllPaginated(?string $search, ?int $rowPerPage)
    {

        $query = $this->getAll($search, $rowPerPage, false);
        return $query->paginate($rowPerPage);
    }

    public function getById(string $id)
    {
        $query = Product::where('id', $id);

        return $query->first();
    }
    public function create(array $data)
    {

        DB::beginTransaction();

        try {
            $product = new Product();
            $product->name = $data['name'];
            $product->code = $data['code'];
            $product->category_id = $data['category_id'];
            $product->save();
            if (!empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $path = $image->store('assets/images', 'public');
                    $product->productImages()->create([
                        'path' => $path,
                    ]);
                }
            }
            $product->stock()->create([
                'stock' => $data['stock']
            ]);
            $product->save();

            DB::commit();

            return $product;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $product = product::find($id);
            $product->name = $data['name'];
            $product->code = $data['code'];
            $product->category_id = $data['category_id'];

            $product->stock()->updateOrCreate(
                ['product_id' => $product->id],
                ['stock' => $data['stock']]
            );

            if (!empty($data['images'])) {
                foreach ($product->productImages as $oldImage) {
                    if (Storage::disk('public')->exists($oldImage->path)) {
                        Storage::disk('public')->delete($oldImage->path);
                    }
                }
                $product->productImages()->delete();

                foreach ($data['images'] as $image) {
                    $path = $image->store('assets/images', 'public');
                    $product->productImages()->create([
                        'path' => $path
                    ]);
                }
            }
            $product->save();

            DB::commit();

            return $product;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $product = product::find($id);
            foreach ($product->productImages as $image) {
                if (Storage::disk('public')->exists($image->path)) {
                    Storage::disk('public')->delete($image->path);
                }
            }
            $product->delete();

            DB::commit();

            return $product;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }
}
