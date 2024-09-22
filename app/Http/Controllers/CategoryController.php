<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoryController extends Controller
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

        $queryBuilder = Category::with('products') // Charger la relation avec les produits
            ->orderBy('created_at', 'desc')
            ->where('title', 'like', '%' . $query . '%');

        $categories = $paginate ? $queryBuilder->paginate(10) : $queryBuilder->get();

        return CategoryResource::collection($categories);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {

        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('images', $imageName, 'public');

        $category = new Category();

        $category->title = $request->input('title');
        $category->description = $request->input('description');
        $category->slug = Str::slug($request->input('title'));
        $category->image = $imageName;
        $category->save();

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        $category = Category::with('products')->find($category)->first();
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $category)
    {
        $category = Category::findOrFail($category);

        $category->title = $request->input('title');
        $category->description = $request->input('description');
        $category->slug = Str::slug($request->input('title'));

        if ($request->hasFile('image')) {
            // Update the image if a new one is provided
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->storeAs('images', $imageName, 'public');
            $category->image = $imageName;
        }

        $category->save();

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(null, 204);
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->find($id);
        $category->restore();
        return new CategoryResource($category);
    }
}
