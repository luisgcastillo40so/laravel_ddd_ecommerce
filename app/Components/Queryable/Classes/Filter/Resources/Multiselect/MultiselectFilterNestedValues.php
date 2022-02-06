<?php

namespace App\Components\Queryable\Classes\Filter\Resources\Multiselect;

use App\Components\Generic\Enums\BooleanString;
use App\Components\Generic\Enums\Response\ResponseValueType;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

final class MultiselectFilterNestedValues
{
    public function __construct(public readonly MultiselectFilterNestedValuesAttribute $attribute, public readonly Collection|EloquentCollection $values)
    {
    }

    #[ArrayShape(['attribute' => "array", 'values' => "array"])]
    public function toArray(): array
    {
        return [
            'attribute' => $this->attribute->toArray(),
            'values' => $this->values->toArray(),
        ];
    }

    public function adjustValueType(string $value): mixed
    {
        return match ($this->attribute->valuesType) {
            ResponseValueType::STRING => $value,
            ResponseValueType::INTEGER => (int) $value,
            ResponseValueType::FLOAT => (float) $value,
            ResponseValueType::BOOLEAN => ($value === BooleanString::TRUE->value),
        };
    }
}
