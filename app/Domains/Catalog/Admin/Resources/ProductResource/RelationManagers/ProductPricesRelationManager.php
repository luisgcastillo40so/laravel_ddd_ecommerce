<?php

namespace App\Domains\Catalog\Admin\Resources\ProductResource\RelationManagers;

use App\Domains\Admin\Admin\Abstracts\RelationManagers\HasManyRelationManager;
use App\Domains\Catalog\Admin\Resources\ProductResource;
use App\Domains\Catalog\Enums\Translation\ProductPriceResourceTranslationKey;
use App\Domains\Catalog\Models\Product;
use App\Domains\Catalog\Models\Settings\CatalogSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

final class ProductPricesRelationManager extends HasManyRelationManager
{
    protected static string $relationship = 'prices';

    public static function form(Form $form): Form
    {
        $availableCurrencies = collect(app(CatalogSettings::class)->available_currencies);

        return $form
            ->schema(self::setTranslatableLabels([
                Select::make(ProductPriceResourceTranslationKey::CURRENCY->value)
                    ->required()
                    ->options(function (RelationManager $livewire) use ($availableCurrencies): array {
                        $currencies = $availableCurrencies
                            ->filter(function (string $currency) use ($livewire): bool {
                                /** @var Product $product */
                                $product = $livewire->ownerRecord;

                                return $product->prices->pluck('currency')->doesntContain($currency);
                            });

                        return $currencies->combine($currencies)->toArray();
                    })
                    ->searchable()
                    ->columnSpan(2),
                TextInput::make(ProductPriceResourceTranslationKey::PRICE->value)
                    ->required()
                    ->integer()
                    ->disabled(fn (callable $get): bool => $get(ProductPriceResourceTranslationKey::CURRENCY->value) === null)
                    ->afterStateHydrated(function (TextInput $component, array $state): void {
                        $component->state($state['amount']);
                    })
                    ->dehydrateStateUsing(fn (string $state): int => (int) $state),
                TextInput::make(ProductPriceResourceTranslationKey::PRICE_DISCOUNTED->value)
                    ->nullable()
                    ->disabled(fn (callable $get): bool => $get(ProductPriceResourceTranslationKey::CURRENCY->value) === null)
                    ->integer()
                    ->afterStateHydrated(function (TextInput $component, ?array $state): void {
                        $component->state(($state === null) ? null : $state['amount']);
                    })
                    ->dehydrateStateUsing(fn (?string $state): ?int => ($state === null) ? null : (int) $state),
            ]));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::setTranslatableLabels([
                TextColumn::make(ProductPriceResourceTranslationKey::CURRENCY->value)
                    ->sortable()
                    ->searchable(),
                TextColumn::make(ProductPriceResourceTranslationKey::PRICE->value)
                    ->sortable()
                    ->searchable(),
                TextColumn::make(ProductPriceResourceTranslationKey::PRICE_DISCOUNTED->value)
                    ->sortable()
                    ->searchable(),
            ]));
    }

    /*
     * Policies
     * */

    protected function canCreate(): bool
    {
        /** @var Product $product */
        $product = $this->ownerRecord;

        return $product->prices->count() < count(app(CatalogSettings::class)->available_currencies) && $this->shouldBeDisplayed();
    }

    protected function canDeleteAny(): bool
    {
        return $this->shouldBeDisplayed();
    }

    protected function canDelete(Model $record): bool
    {
        return $this->shouldBeDisplayed();
    }

    protected function canEdit(Model $record): bool
    {
        return $this->shouldBeDisplayed();
    }

    private function shouldBeDisplayed(): bool
    {
        return collect([
            ProductResource::getUrl('view', $this->ownerRecord->getKey()),
            route('livewire.message', ['catalog.admin.resources.product-resource.pages.view-product']),
        ])->doesntContain(Request::url());
    }

    /*
     * Translation
     * */

    protected static function getTranslationKeyClass(): string
    {
        return ProductPriceResourceTranslationKey::class;
    }
}
