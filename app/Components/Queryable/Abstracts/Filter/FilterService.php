<?php

namespace App\Components\Queryable\Abstracts\Filter;

use App\Components\Queryable\Abstracts\FilterBuilder;
use App\Components\Queryable\Classes\Filter\Filter;
use App\Components\Queryable\Enums\QueryKey;
use Closure;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\AllowedFilter;
use UnitEnum;

abstract class FilterService
{
    protected array $validated;

    /**
     * @var Collection<Filter>
     */
    private Collection $allowed;

    /**
     * @var Collection<AllowedFilter>
     */
    private Collection $callbacks;

    private UnitEnum & IAllowedFilterEnum $searchFilter;

    public function __construct(protected readonly FilterBuilder $builder)
    {
        $this->allowed = collect([]);
        $this->callbacks = collect([]);
    }

    /**
     * @return Collection<Filter>
     */
    public function allowed(): Collection
    {
        return $this->allowed->values();
    }

    /**
     * @return Collection<AllowedFilter>
     */
    public function callbacks(): Collection
    {
        return $this->callbacks->values();
    }

    abstract public function build(): static;

    protected function addFilter(UnitEnum & IAllowedFilterEnum $filter, Closure $callback): static
    {
        $this->allowed->offsetSet($filter->name, $this->builder->build($filter));
        $this->callbacks->offsetSet($filter->name, AllowedFilter::callback($filter->name, $callback));

        return $this;
    }

    protected function addSearchFilter(UnitEnum & IAllowedFilterEnum $filter, Closure $callback): static
    {
        $this->addFilter($filter, $callback);

        $this->searchFilter = $filter;

        return $this;
    }

    public function getSearchFilter(): ?Filter
    {
        return isset($this->searchFilter) ? $this->allowed->offsetGet($this->searchFilter->name) : null;
    }

    protected function getFilter(UnitEnum & IAllowedFilterEnum $filter): mixed
    {
        return $this->validated[QueryKey::FILTER->value][$filter->name];
    }
}
