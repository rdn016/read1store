<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Customer Information')
                            ->schema([
                                Forms\Components\TextInput::make('customer_name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('customer_email')
                                    ->email()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('customer_phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(20),

                                Forms\Components\TextInput::make('customer_whatsapp')
                                    ->required()
                                    ->maxLength(20)
                                    ->placeholder('e.g., 08123456789'),

                                Forms\Components\Textarea::make('shipping_address')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Section::make('Order Notes')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->label('Customer Notes')
                                    ->rows(2),

                                Forms\Components\Textarea::make('admin_notes')
                                    ->label('Admin Notes')
                                    ->rows(2),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Order Details')
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Auto-generated'),

                                Forms\Components\Select::make('status')
                                    ->options(Order::STATUSES)
                                    ->required()
                                    ->default('pending')
                                    ->native(false),

                                Forms\Components\TextInput::make('total_amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->placeholder('Calculated from items'),
                            ]),

                        Forms\Components\Section::make('Timestamps')
                            ->schema([
                                Forms\Components\DateTimePicker::make('confirmed_at')
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\DateTimePicker::make('shipped_at')
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\DateTimePicker::make('completed_at')
                                    ->disabled()
                                    ->dehydrated(false),
                            ])
                            ->collapsible()
                            ->collapsed(),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->url(fn (Order $record): string => $record->whatsapp_link)
                    ->openUrlInNewTab()
                    ->icon('heroicon-m-chat-bubble-left-ellipsis')
                    ->color('success'),

                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => Order::STATUS_COLORS[$state] ?? 'secondary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('confirmed_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Order::STATUSES)
                    ->multiple(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Order $record): string => $record->whatsapp_link)
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm Order')
                    ->modalDescription('Are you sure you want to confirm this order? This will decrement the stock for all products in this order.')
                    ->modalSubmitActionLabel('Yes, confirm order')
                    ->visible(fn (Order $record): bool => $record->canBeConfirmed())
                    ->action(function (Order $record): void {
                        if ($record->confirm()) {
                            Notification::make()
                                ->title('Order Confirmed')
                                ->body("Order {$record->order_number} has been confirmed and stock has been updated.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Error')
                                ->body('Order could not be confirmed.')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Order')
                    ->modalDescription('Are you sure you want to cancel this order? If the order was confirmed, stock will be restored.')
                    ->modalSubmitActionLabel('Yes, cancel order')
                    ->visible(fn (Order $record): bool => $record->canBeCancelled())
                    ->action(function (Order $record): void {
                        if ($record->cancel()) {
                            Notification::make()
                                ->title('Order Cancelled')
                                ->body("Order {$record->order_number} has been cancelled.")
                                ->warning()
                                ->send();
                        }
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Group::make([
                    Infolists\Components\Section::make('Order Information')
                        ->schema([
                            Infolists\Components\TextEntry::make('order_number')
                                ->label('Order Number')
                                ->weight('bold')
                                ->copyable(),

                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->color(fn (string $state): string => Order::STATUS_COLORS[$state] ?? 'secondary'),

                            Infolists\Components\TextEntry::make('total_amount')
                                ->money('IDR')
                                ->weight('bold'),

                            Infolists\Components\TextEntry::make('created_at')
                                ->dateTime('d M Y H:i'),
                        ])->columns(2),

                    Infolists\Components\Section::make('Customer Information')
                        ->schema([
                            Infolists\Components\TextEntry::make('customer_name'),

                            Infolists\Components\TextEntry::make('customer_email')
                                ->placeholder('Not provided'),

                            Infolists\Components\TextEntry::make('customer_phone')
                                ->icon('heroicon-m-phone'),

                            Infolists\Components\TextEntry::make('customer_whatsapp')
                                ->icon('heroicon-m-chat-bubble-left-ellipsis')
                                ->url(fn (Order $record): string => $record->whatsapp_link)
                                ->openUrlInNewTab()
                                ->color('success'),

                            Infolists\Components\TextEntry::make('shipping_address')
                                ->columnSpanFull()
                                ->placeholder('Not provided'),
                        ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Infolists\Components\Group::make([
                    Infolists\Components\Section::make('Timeline')
                        ->schema([
                            Infolists\Components\TextEntry::make('confirmed_at')
                                ->label('Confirmed')
                                ->dateTime('d M Y H:i')
                                ->placeholder('Not confirmed'),

                            Infolists\Components\TextEntry::make('shipped_at')
                                ->label('Shipped')
                                ->dateTime('d M Y H:i')
                                ->placeholder('Not shipped'),

                            Infolists\Components\TextEntry::make('completed_at')
                                ->label('Completed')
                                ->dateTime('d M Y H:i')
                                ->placeholder('Not completed'),
                        ]),

                    Infolists\Components\Section::make('Notes')
                        ->schema([
                            Infolists\Components\TextEntry::make('notes')
                                ->label('Customer Notes')
                                ->placeholder('No notes'),

                            Infolists\Components\TextEntry::make('admin_notes')
                                ->label('Admin Notes')
                                ->placeholder('No admin notes'),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
