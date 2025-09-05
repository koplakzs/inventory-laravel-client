<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(?string $search, ?int $limit, bool $execute)
    {
        $query = Category::where(function ($query) use ($search) {
            if ($search) {
                $query->search($search);
            }
            $query->orderBy('created_at', 'desc');
        });
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
        $query = Category::where('id', $id);
        return $query->first();
    }

    public function create(array $data)
    {

        DB::beginTransaction();

        try {
            $category = new Category();
            $category->name = $data['name'];
            $category->save();
            DB::commit();

            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }

    public function update(string $id, array $data)
    {
        DB::beginTransaction();

        try {
            $category = Category::find($id);
            $category->name = $data['name'];
            $category->save();

            DB::commit();

            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }

    public function delete(string $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::find($id);
            $category->delete();
            DB::commit();

            return $category;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw new Exception($th->getMessage());
        }
    }
}
