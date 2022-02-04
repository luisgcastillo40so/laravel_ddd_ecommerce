<?php

namespace App\Domains\Catalog\Models\Virtual\Product;

use App\Components\Mediable\Models\Virtual\Media;
use App\Domains\Catalog\Models\Virtual\ProductCategory\LightProductCategory;

/**
 * @OA\Schema(
 *    @OA\Xml(name="LightProduct")
 * )
 */
class LightProduct extends Product
{
    /**
     * @OA\Property()
     * @var Media
     */
    public $image;

    /**
     * @OA\Property()
     * @var LightProductCategory[]
     */
    public $categories;
}