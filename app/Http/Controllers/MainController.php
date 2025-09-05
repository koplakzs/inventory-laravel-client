<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        $countProducts = Product::count();
        $countCategories = Category::count();
        $countStocks = Stock::sum('stock');
        $products = Product::latest()
            ->take(5)
            ->get();

        $rawMonthly = Product::selectRaw('MONTH(created_at) as month, COUNT(*) as countProduk')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->pluck('countProduk', 'month'); // hasil: [1 => 10, 2 => 5, dst]

        // Generate data bulan Januari–Desember
        $monthlyProduct = collect(range(1, 12))->map(function ($month) use ($rawMonthly) {
            return [
                'month'        => Carbon::create()->month($month)->translatedFormat('F'),
                'countProduct' => $rawMonthly->get($month, 0), // default 0 jika tidak ada data
            ];
        });

        $data = [
            "countProducts"    => $countProducts,
            "countCategories"  => $countCategories,
            "countStocks"      => $countStocks,
            "products"         => ProductResource::collection($products),
            "monthlyProduct" => $monthlyProduct,
        ];
        return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', $data, 200);
    }
}
