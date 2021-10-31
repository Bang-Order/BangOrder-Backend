<?php

namespace App\Policies;

use App\Order;
use App\Restaurant;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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
     * @param \App\Order $order
     * @param $id
     * @return mixed
     */
    public function view(?Restaurant $restaurant, Order $order, $id)
    {
        return $order->restaurant_id == $id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param Restaurant|null $restaurant
     * @param $id
     * @return mixed
     */
    public function create(?Restaurant $restaurant, $id)
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Restaurant $restaurant
     * @param \App\Order $order
     * @param $id
     * @return mixed
     */
    public function update(Restaurant $restaurant, Order $order, $id)
    {
        return $restaurant->id == $order->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Restaurant $restaurant
     * @param \App\Order $order
     * @param $id
     * @return mixed
     */
    public function delete(Restaurant $restaurant, Order $order, $id)
    {
        return $restaurant->id == $order->restaurant_id && $restaurant->id == $id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\Order  $order
     * @return mixed
     */
    public function restore(Restaurant $restaurant, Order $order)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Restaurant  $restaurant
     * @param  \App\Order  $order
     * @return mixed
     */
    public function forceDelete(Restaurant $restaurant, Order $order)
    {
        return false;
    }
}
