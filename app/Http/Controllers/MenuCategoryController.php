<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategory\MenuCategoryCollection;
use App\Http\Resources\MenuCategory\MenuCategoryResource;
use App\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as ValidationValidator;

class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        if (is_null($request->restaurant_id)) {
            return response()->json(['message' => 'restaurant_id route parameter is required'], 400);
        }

        $data = MenuCategory::where('restaurant_id', $request->restaurant_id)->get();

        if (count($data) == 0) {
            return response()->json(['message' => 'restaurant_id not found'], 404);
        }

        return new MenuCategoryCollection($data);
    }

    public function store(Request $request)
    {
        $validator1 = Validator::make($request->all(), [
            'restaurant_id' => ['required', 'numeric'],
            'name' => ['required', 'string']
        ]);
        if ($validator1->fails()) {
            return response()->json($validator1->errors(), 400);
        }

        $validator2 = MenuCategory::find($request->restaurant_id);
        if (is_null($validator2)) {
            return response()->json(['message' => "data id {$request->restaurant_id} not found"], 404);
        }

        MenuCategory::create($request->all());

        return response()->json(['message' => 'success'], 201);
    }

    public function show(MenuCategory $menuCategory, Request $request)
    {
        if (is_null($request->id)) {
            return response()->json(['message' => 'category_id route parameter is required'], 400);
        }
        
        $data = $menuCategory->find($request->id);

        if (is_null($data)) {
            return response()->json(['message' => 'data not found'], 404);
        }

        return new MenuCategoryResource($data);
    }

    public function update(Request $request, MenuCategory $menuCategory)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
            'restaurant_id' => ['required', 'numeric'],
            'name' => ['required', 'string']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $menuCategory->find($request->id);
        if (is_null($data)) {
            return response()->json(['message' => "id {$request->id} not found"], 404);
        }

        $data->update($request->all());

        return response()->json(['message' => 'success']);
    }

    public function destroy(Request $request, MenuCategory $menuCategory)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $menuCategory->find($request->id);

        if (is_null($data)) {
            return response()->json(['message' => "id {$request->id} not found"], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'success'
        ]);
    }
}
