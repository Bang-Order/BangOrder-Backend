<?php

namespace App\Policies;

use App\MenuCategory;
use App\Restaurant;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuCategoryPolicy
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
     * @param \App\MenuCategory $menuCategory
     * @param $id
     * @return mixed
     */
    public function view(?Restaurant $restaurant, MenuCategory $menuCategory, $id)
    {
        return $menuCategory->restaurant_id == $id;
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
     * @param \App\MenuCategory $menuCategory
     * @param $id
     * @return mixed
     */
    public function update(Restaurant $restaurant, MenuCategory $menuCategory, $id)
    {
        return $restaurant->id == $menuCategory->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Restaurant $restaurant
     * @param \App\MenuCategory $menuCategory
     * @param $id
     * @return mixed
     */
    public function delete(Restaurant $restaurant, MenuCategory $menuCategory, $id)
    {
        return $restaurant->id == $menuCategory->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\MenuCategory  $menuCategory
     * @return mixed
     */
    public function restore(Restaurant $restaurant, MenuCategory $menuCategory)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\MenuCategory  $menuCategory
     * @return mixed
     */
    public function forceDelete(Restaurant $restaurant, MenuCategory $menuCategory)
    {
        return false;
    }
}
