<?php

namespace App\Domains\Components\Priceable\Models\Virtual;

/**
 * @OA\Schema(
 *    @OA\Xml(name="Money")
 * )
 */
class Money
{
    /**
     * @OA\Property()
     * @var float
     * @example 48.08
     */
    public $value;

    /**
     * @OA\Property()
     * @var float|int
     * @example 4808
     */
    public $amount;

    /**
     * @OA\Property()
     * @var string
     * @example $48.08
     */
    public $render;
}