<?php

namespace App\Policies;

use App\Menu;
use App\Restaurant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Restaurant  $restaurant
     * @return mixed
     */
    public function viewAny(Restaurant $restaurant)
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param Restaurant|null $restaurant
     * @param \App\Menu $menu
     * @param $id
     * @return mixed
     */
    public function view(?Restaurant $restaurant, Menu $menu, $id)
    {
        return $menu->restaurant_id == $id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Restaurant $restaurant
     * @param $id
     * @return mixed
     */
    public function create(Restaurant $restaurant, $id)
    {
        return $restaurant->id == $id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Restaurant $restaurant
     * @param \App\Menu $menu
     * @param $id
     * @return mixed
     */
    public function update(Restaurant $restaurant, Menu $menu, $id)
    {
        return $restaurant->id == $menu->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Restaurant $restaurant
     * @param \App\Menu $menu
     * @param $id
     * @return mixed
     */
    public function delete(Restaurant $restaurant, Menu $menu, $id)
    {
        return $restaurant->id == $menu->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\Menu  $menu
     * @return mixed
     */
    public function restore(Restaurant $restaurant, Menu $menu)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\Menu  $menu
     * @return mixed
     */
    public function forceDelete(Restaurant $restaurant, Menu $menu)
    {
        return false;
    }
}
