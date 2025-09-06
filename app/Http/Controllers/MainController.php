<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\ProductResource;
use App\Interfaces\MainRepositoryInterface;
use Illuminate\Http\Request;

class MainController extends Controller
{
    private MainRepositoryInterface $mainRepository;

    public function __construct(MainRepositoryInterface $mainRepository)
    {
        $this->mainRepository = $mainRepository;
    }
    public function index()
    {
        try {
            $data = $this->mainRepository->getDashboard();
            return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', $data, 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }

    public function report(Request $request)
    {
        try {
            $start = $request->query('start');
            $end   = $request->query('end');

            $products = $this->mainRepository->getReport($start, $end);

            return ResponseHelper::jsonResponse(true, 'Data Berhasil Diambil', ProductResource::collection($products), 200);
        } catch (\Throwable $th) {
            return ResponseHelper::jsonResponse(false, $th->getMessage(), null, 500);
        }
    }
}
