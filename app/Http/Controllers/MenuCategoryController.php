<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategory\MenuCategoryCollection;
use App\Http\Resources\MenuCategory\MenuCategoryResource;
use App\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MenuCategory  $menuCategory
     * @return \Illuminate\Http\Response
     */
    public function show(MenuCategory $menuCategory, Request $request)
    {
        if (is_null($request->restaurant_id)) {
            return response()->json(['message' => 'restaurant_id route parameter is required'], 400);
        }

        $data = $menuCategory->where('restaurant_id', $request->restaurant_id)->get();

        if (count($data) == 0) {
            return response()->json(['message' => 'restaurant_id is invalid'], 404);
        }

        return new MenuCategoryCollection($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MenuCategory  $menuCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(MenuCategory $menuCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuCategory  $menuCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MenuCategory $menuCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuCategory  $menuCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(MenuCategory $menuCategory)
    {
        //
    }
}
