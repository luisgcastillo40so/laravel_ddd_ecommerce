<?php

namespace App\Domains\Catalog\Models\Virtual;

use App\Domains\Generic\Enums\Response\ResponseValueType;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
final class ProductAttribute
{
    /**
     * @OA\Property()
     * @var string
     * @example Width
     */
    public $title;

    /**
     * @OA\Property()
     * @var string
     * @example width
     */
    public $slug;

    /**
     * @OA\Property(ref="#/components/schemas/ResponseValueType")
     * @var ResponseValueType
     */
    public $type;
}
