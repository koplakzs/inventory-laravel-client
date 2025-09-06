<?php

namespace  App\Repositories;

use App\Http\Resources\ProductResource;
use App\Interfaces\MainRepositoryInterface;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Exception;

class MainRepository implements MainRepositoryInterface
{
    public function getDashboard()
    {
        try {
            $countProducts = Product::count();
            $countCategories = Category::count();
            $countStocks = Stock::sum('stock');
            $products = Product::latest()
                ->take(5)
                ->get();

            $rawMonthly = Product::selectRaw('MONTH(created_at) as month, COUNT(*) as countProduk')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('countProduk', 'month');

            $monthlyProduct = collect(range(1, 12))->map(function ($month) use ($rawMonthly) {
                return [
                    'month'        => Carbon::create()->month($month)->translatedFormat('F'),
                    'countProduct' => $rawMonthly->get($month, 0),
                ];
            });

            $data = [
                "countProducts"    => $countProducts,
                "countCategories"  => $countCategories,
                "countStocks"      => $countStocks,
                "products"         => ProductResource::collection($products),
                "monthlyProduct" => $monthlyProduct,
            ];
            return $data;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function getReport(string $start, string $end)
    {
        try {
            $products = Product::report($start, $end)->get();
            return $products;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }
}
