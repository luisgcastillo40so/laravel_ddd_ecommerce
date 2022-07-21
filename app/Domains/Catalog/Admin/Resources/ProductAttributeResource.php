<?php

namespace App\Domains\Catalog\Admin\Resources;

use App\Domains\Admin\Admin\Abstracts\Resource;
use App\Domains\Admin\Admin\Components\Cards\TimestampsCard;
use App\Domains\Catalog\Enums\ProductAttributeValuesType;
use App\Domains\Catalog\Enums\Translation\ProductAttributeTranslationKey;
use App\Domains\Catalog\Models\ProductAttribute;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class ProductAttributeResource extends Resource
{
    protected static ?string $model = ProductAttribute::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $slug = 'catalog/attributes';

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    /*
     * Global Search
     * */

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug'];
    }

    /*
     * Data
     * */

    public static function form(Form $form): Form
    {
        return $form
            ->schema(self::setTranslatableLabels([
                Card::make()
                    ->schema(self::setTranslatableLabels([
                        TextInput::make(ProductAttributeTranslationKey::TITLE->value)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, $state): mixed => $set(ProductAttributeTranslationKey::SLUG->value, Str::slug($state)))
                            ->minValue(2)
                            ->maxLength(255)
                            ->placeholder('Width'),
                        TextInput::make(ProductAttributeTranslationKey::SLUG->value)
                            ->required()
                            ->minValue(2)
                            ->maxLength(255)
                            ->placeholder('width'),
                        Select::make(ProductAttributeTranslationKey::VALUES_TYPE->value)
                            ->required()
                            ->options(collect(ProductAttributeValuesType::cases())->reduce(fn (Collection $acc, ProductAttributeValuesType $valuesType): Collection => tap($acc, static fn () => $acc->offsetSet($valuesType->value, self::translateEnum($valuesType))), collect([])))
                            ->searchable(),
                    ]))
                    ->columnSpan(2),
                TimestampsCard::make()
                    ->columnSpan(1),
            ]))
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::setTranslatableLabels([
                TextColumn::make(ProductAttributeTranslationKey::TITLE->value)->sortable()->searchable(),
                TextColumn::make(ProductAttributeTranslationKey::SLUG->value)->searchable(),
            ]))
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Domains\Catalog\Admin\Resources\ProductAttributeResource\Pages\ListProductAttributes::route('/'),
            'create' => \App\Domains\Catalog\Admin\Resources\ProductAttributeResource\Pages\CreateProductAttribute::route('/create'),
            'edit' => \App\Domains\Catalog\Admin\Resources\ProductAttributeResource\Pages\EditProductAttribute::route('/{record}/edit'),
            'view' => \App\Domains\Catalog\Admin\Resources\ProductAttributeResource\Pages\ViewProductAttribute::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['values']);
    }

    /*
     * Policies
     * */

    /**
     * @param ProductAttribute $record
     *
     * @return bool
     */
    public static function canDelete(Model $record): bool
    {
        return $record->values->isEmpty();
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    /*
     * Translation
     * */

    protected static function getTranslationKeyClass(): string
    {
        return ProductAttributeTranslationKey::class;
    }
}
