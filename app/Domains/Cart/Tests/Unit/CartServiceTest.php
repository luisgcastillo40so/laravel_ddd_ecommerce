<?php

namespace App\Domains\Cart\Tests\Unit;

use App\Application\Tests\TestCase;
use App\Domains\Cart\Models\Cart;
use App\Domains\Cart\Services\CartService;
use App\Domains\Catalog\Database\Seeders\ProductCategorySeeder;
use App\Domains\Catalog\Database\Seeders\ProductPriceSeeder;
use App\Domains\Catalog\Database\Seeders\ProductSeeder;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\Settings\CatalogSettings;

class CartServiceTest extends TestCase
{
    private CatalogSettings $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = app(CatalogSettings::class);
    }

    protected function setUpOnce(): void
    {
        $this->seed([
            ProductCategorySeeder::class,
            ProductSeeder::class,
            ProductPriceSeeder::class,
        ]);
    }

    public function test_cart_workflow(): void
    {
        $quantity = 5;

        /** @var Product $product */
        $product = Product::query()->with(['prices'])->inRandomOrder()->first();
        $this->assertNotNull($product);

        $cart = CartService::make($this->settings->default_currency, null);
        $cart = CartService::add($cart, $product, $quantity);
        /** @var Cart $cart */
        $cart = CartService::find(null, $cart->key);
        $this->assertNotNull($cart);

        $productPrice = $product->getPurchasablePrice($this->settings->default_currency)->getAmount();
        $productDiscountedPrice = $product->getPurchasablePriceDiscounted($this->settings->default_currency)?->getAmount() ?? $productPrice;

        $this->assertEquals($productPrice * $quantity, $cart->price_items->getAmount());
        $this->assertEquals($productDiscountedPrice * $quantity, $cart->price_items_discounted->getAmount());

        $newQuantity = 10;
        $cart = CartService::update($cart, $product, $newQuantity);

        $this->assertEquals($productPrice * $newQuantity, $cart->price_items->getAmount());
        $this->assertEquals($productDiscountedPrice * $newQuantity, $cart->price_items_discounted->getAmount());

        CartService::save($cart);
        /** @var Cart $cart */
        $cart = Cart::query()->first();
        $this->assertNotNull($cart);

        $this->assertEquals($productPrice * $newQuantity, $cart->price_items->getAmount());
        $this->assertEquals($productDiscountedPrice * $newQuantity, $cart->price_items_discounted->getAmount());

        CartService::delete(null, $cart->key);
        $cart = CartService::find(null, $cart->key);
        $this->assertNull($cart);
    }
}