<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;

class ImportController extends Controller
{
//    private $application;
//
//    public function __construct(Request $request)
//    {
//        if (!$apikey = $request->header('X-api-key', false)) {
//
//            if (!$apikey = $request->get('api_key', false)) {
//                abort(401, 'API key is not provided', ['Content-Type' => 'application/json']);
//            }
//        }
//
//        if (!$apikey = Apikey::where('key', $apikey)->where('active', true)->first()) {
//            abort(403, 'API key cannot be found or is no longer active', ['Content-Type' => 'application/json']);
//        }
//
//        $this->application = $apikey->application;
//    }

    public function index(Request $request)
    {
        return response()->json(['status' => 'ok']);
    }

    public function product(Request $request)
    {
        $product = new Product($request->all());
        $product->save();
        $product->categories()->attach($request->get('category_id'));

        $name = $request->get("image");
        if ($name != null) {
            $image = new Image([
                'title' => $product->title,
                'name' => $name,
                'thumbnail_name' => $name,
                'product_id' => $product->id
            ]);
            $image->save();
        }

        return response()->json(['product' => $request->all()]);
    }

    public function category(Request $request)
    {
        $category = new Category($request->all());
        $category->save();

        return response()->json([$category]);
    }
}
