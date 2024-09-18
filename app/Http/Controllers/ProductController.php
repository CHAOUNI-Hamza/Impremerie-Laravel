<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Image;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = $request->input('search'); 
        $paginate = $request->filled('paginate');

        $queryBuilder = Product::with('category', 'user') // Charger la relation avec les produits
            ->orderBy('created_at', 'desc')
            ->where('title', 'like', '%' . $query . '%');

        $produits = $paginate ? $queryBuilder->paginate(10) : $queryBuilder->get();

        return ProductResource::collection($produits);
    }

    public function topSellingProducts()
    {
        $produits = Product::select('products.id', 'products.title', 'products.images', 'products.description', DB::raw('COUNT(commande_details.id) as total_sold'))
        ->join('commande_details', 'products.id', '=', 'commande_details.product_id')
        ->join('commandes', 'commande_details.commande_id', '=', 'commandes.id')
        ->whereNull('products.deleted_at')
        ->groupBy('products.id', 'products.title', 'products.images', 'products.description')
        ->orderByDesc('total_sold')
        ->limit(5) // Limiter aux 5 produits les plus vendus
        ->get();

        return ProductResource::collection($produits);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    $auth = auth()->user();

    $productData = $request->only(['title', 'description', 'price', 'qt', 'category_id']);
    $productData['user_id'] = $auth->id;
    $productData['slug'] = Str::slug($request->input('title'));

    /*$json = json_decode($request->input('json'), true);

    $imagePaths = [];

    foreach ($json as $item) {
        foreach ($item['image'] as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('images', $imageName, 'public');
                $imagePaths[] = $imageName;
            }
        }
    }*/

    if (is_string($request->input('json'))) {
        $productData['json'] = $request->input('json');
    } else {
        $productData['json'] = json_encode($request->input('json'));
    }
    

    if ($request->hasFile('images')) {
        $externalImages = [];
        foreach ($request->file('images') as $image) {
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('images', $imageName, 'public');
            $externalImages[] = $imageName;
        }
        $productData['images'] = $externalImages;
    } else {
        $productData['images'] = null; 
    }

    $product = Product::create($productData);
    return new ProductResource($product);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $product)
    {
        $auth = auth()->user();
        $product = Product::findOrFail($product);
        $productData = $request->only(['title', 'description', 'json', 'price', 'qt','category_id']);
        $productData['user_id'] = $auth->id;
        $productData['slug'] = Str::slug($request->input('title'));

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('images', $imageName, 'public');
                $images[] = $imageName;
            }
            $productData['images'] = $images;
        }

        // Assign the new data to the model
        $product->fill($productData);

        // Save the changes
        $product->save();

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->find($id);
        $product->restore();
        return new ProductResource($product);
    }
}
