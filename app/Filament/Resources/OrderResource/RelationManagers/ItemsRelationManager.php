<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'product_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(Product::active()->inStock()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state) {
                        if ($state) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('product_name', $product->name);
                                $set('product_sku', $product->sku);
                                $set('unit_price', $product->price);
                                $quantity = $get('quantity') ?: 1;
                                $set('subtotal', $product->price * $quantity);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('product_name')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\TextInput::make('product_sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255)
                    ->disabled()
                    ->dehydrated(),

                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?int $state) {
                        $unitPrice = $get('unit_price') ?: 0;
                        $set('subtotal', $unitPrice * ($state ?: 1));
                    }),

                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->reactive()
                    ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $quantity = $get('quantity') ?: 1;
                        $set('subtotal', ($state ?: 0) * $quantity);
                    }),

                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.featured_image')
                    ->label('Image')
                    ->square()
                    ->size(50),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->searchable(),

                Tables\Columns\TextColumn::make('product_sku')
                    ->label('SKU')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('unit_price')
                    ->money('IDR')
                    ->label('Unit Price'),

                Tables\Columns\TextColumn::make('subtotal')
                    ->money('IDR')
                    ->weight('bold'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (isset($data['product_id'])) {
                            $product = Product::find($data['product_id']);
                            if ($product) {
                                $data['product_snapshot'] = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'sku' => $product->sku,
                                    'price' => $product->price,
                                    'specifications' => $product->specifications,
                                    'featured_image' => $product->featured_image,
                                ];
                            }
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
