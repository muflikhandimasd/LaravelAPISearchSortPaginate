<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

use function GuzzleHttp\Promise\all;

class ProductController extends Controller
{
    public function frontend()
    {
        $products = Product::all();
        return response()->json([
            'message' => 'Success get all products',
            'products' => $products
        ], 200);
    }

    public function backend(Request $request)
    {
        $query = Product::query();
        if ($search = $request->input('search')) {
            $query->whereRaw("title LIKE '%" . $search . "%'")
                ->orWhereRaw("description LIKE '%" . $search . "%'");
        }
        if ($sort = $request->input('sort')) {
            $query->orderBy('price', $sort);
        }
        $limit = $request->input('limit', 20);
        $page = $request->input('page', 1);
        $total = $query->count();
        $result = $query->offset(($page - 1) * $limit)->limit($limit)->get();
        $lastPage = ceil($total / $limit);
        return response()->json([
            'data' => $result,
            'limit' => $limit,
            'total' => $total,
            'page' => $page,
            'last_page' => $lastPage
        ], 200);
    }
}
