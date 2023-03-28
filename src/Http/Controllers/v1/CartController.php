<?php

namespace Fleetbase\Http\Controllers\Storefront\v1;

use Fleetbase\Http\Controllers\Controller;
use Fleetbase\Models\Storefront\Cart;
use Fleetbase\Support\Resp;
use Illuminate\Http\Request;
use Fleetbase\Http\Resources\Storefront\Cart as StorefrontCart;
use Exception;

class CartController extends Controller
{
    /**
     * Retrieve or create a cart using a unique identifier. If no unique identifier is provided
     * one will be created.
     *
     * @param string|null $uniqueId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function retrieve(?string $uniqueId = null, Request $request)
    {
        $cart = Cart::retrieve($uniqueId, true);

        // reset currency
        $cart->resetCurrency();

        return new StorefrontCart($cart);
    }

    /**
     * Adds a product to cart and creates a line item for the product.
     *
     * @param string $cartId
     * @param string $productId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(string $cartId, string $productId, Request $request)
    {
        $quantity = $request->input('quantity', 1);
        $variants = $request->input('variants', []);
        $addons = $request->input('addons', []);
        $scheduledAt = $request->input('scheduled_at');
        $storeLocationId = $request->input('store_location');
        $cart = Cart::retrieve($cartId);

        if (!$cart) {
            return Resp::error('Cart was not found or has already been checkout out.');
        }

        try {
            $cart->add($productId, $quantity, $variants, $addons, $storeLocationId, $scheduledAt);
        } catch (Exception $e) {
            return Resp::error($e->getMessage());
        }

        // reset currency
        $cart->resetCurrency();

        return new StorefrontCart($cart);
    }

    /**
     * Update a line item in the cart
     *
     * @param string $cartId
     * @param string $cartItemId - can be either product id or line item id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(string $cartId, string $cartItemId, Request $request)
    {
        $quantity = $request->input('quantity', null);
        $variants = $request->input('variants', null);
        $addons = $request->input('addons', null);
        $scheduledAt = $request->input('scheduled_at');
        $cart = Cart::retrieve($cartId);

        if (!$cart) {
            return Resp::error('Cart was not found or has already been checkout out.');
        }

        try {
            $cart->updateItem($cartItemId, $quantity, $variants, $addons, $scheduledAt);
        } catch (Exception $e) {
            return Resp::error($e->getMessage());
        }

        // reset currency
        $cart->resetCurrency();

        return new StorefrontCart($cart);
    }

    /**
     * Removes a line item in the cart
     *
     * @param string $cartId
     * @param string $cartItemId - can be either product id or line item id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function remove(?string $cartId, ?string $cartItemId, Request $request)
    {
        $cart = Cart::retrieve($cartId);

        if (!$cart) {
            return Resp::error('Cart was not found or has already been checkout out.');
        }

        try {
            $cart->remove($cartItemId);
        } catch (Exception $e) {
            return Resp::error($e->getMessage());
        }

        // reset currency
        $cart->resetCurrency();

        return new StorefrontCart($cart);
    }

    /**
     * Empties a cart
     *
     * @param string $cartId
     * @return \Illuminate\Http\Response
     */
    public function empty(string $cartId)
    {
        $cart = Cart::retrieve($cartId);

        if (!$cart) {
            return Resp::error('Unable to empty cart.');
        }

        $cart->empty();

        return new StorefrontCart($cart);
    }

    /**
     * Deletes a cart.
     *
     * @param string $cartId
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(string $cartId)
    {
        $cart = Cart::retrieve($cartId);

        if (!$cart) {
            return Resp::error('Cart was not found or has already been checkout out.');
        }

        $cart->delete();

        return response()->json([]);
    }
}
