<?php

namespace App\Components\Queryable\Classes\Filter;

use App\Components\Generic\Utils\LangUtils;
use App\Components\Queryable\Abstracts\Query;
use App\Components\Queryable\Enums\QueryFilterType;
use BackedEnum;
use JetBrains\PhpStorm\ArrayShape;

abstract class Filter extends Query
{
    public static QueryFilterType $type;

    public function __construct(BackedEnum $filter)
    {
        /** @var string $translation */
        $translation = LangUtils::translateEnum($filter);

        $this->query = $filter->name;
        $this->title = $translation;
    }

    #[ArrayShape(['query' => "string", 'title' => "string", 'type' => "string"])]
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => static::$type->value,
        ]);
    }

    abstract public function toAllowedArray(): array;

    abstract public function toAppliedArray(): array;

    abstract public function setSelectedValues(mixed ...$values): ?self;
}
