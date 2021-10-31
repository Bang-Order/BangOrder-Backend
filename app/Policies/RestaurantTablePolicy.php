<?php

namespace App\Policies;

use App\Restaurant;
use App\RestaurantTable;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestaurantTablePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Restaurant  $restaurant
     * @return mixed
     */
    public function viewAny(Restaurant $restaurant, $id)
    {
        return $restaurant->id == $id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param Restaurant|null $restaurant
     * @param \App\RestaurantTable $restaurantTable
     * @param $id
     * @return mixed
     */
    public function view(?Restaurant $restaurant, RestaurantTable $restaurantTable, $id)
    {
        return $restaurantTable->restaurant_id == $id;
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
     * @param \App\RestaurantTable $restaurantTable
     * @param $id
     * @return mixed
     */
    public function update(Restaurant $restaurant, RestaurantTable $restaurantTable, $id)
    {
        return $restaurant->id == $restaurantTable->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\RestaurantTable  $restaurantTable
     * @return mixed
     */
    public function delete(Restaurant $restaurant, RestaurantTable $restaurantTable, $id)
    {
        return $restaurant->id == $restaurantTable->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\RestaurantTable  $restaurantTable
     * @return mixed
     */
    public function restore(Restaurant $restaurant, RestaurantTable $restaurantTable)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\RestaurantTable  $restaurantTable
     * @return mixed
     */
    public function forceDelete(Restaurant $restaurant, RestaurantTable $restaurantTable)
    {
        return false;
    }
}
