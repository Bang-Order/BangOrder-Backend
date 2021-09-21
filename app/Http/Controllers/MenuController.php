<?php

namespace App\Http\Controllers;

use App\Http\Resources\Menu\MenuCollection;
use App\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Menu::all();
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
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu, Request $request)
    {
        if (is_null($request->restaurant_id)) {
            return response()->json(
                ['message' => 'restaurant_id route parameter is required', 400],
                400
            );
        }
        if (is_null($request->category_id)) {
            return response()->json(
                ['message' => 'category_id route parameter is required', 400],
                400
            );
        }

        $data = $menu->where([
            ['restaurant_id', $request->restaurant_id],
            ['category_id', $request->category_id],
        ])->get();

        if (count($data) == 0) {
            return response()->json(['message' => 'restaurant_id or category_id is invalid'], 404);
        }

        return new MenuCollection($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        //
    }
}
