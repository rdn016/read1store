<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('whatsapp')
                ->label('Open WhatsApp')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('success')
                ->url(fn (): string => $this->record->whatsapp_link)
                ->openUrlInNewTab(),

            Actions\Action::make('confirm')
                ->label('Confirm Order')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm Order')
                ->modalDescription('Are you sure you want to confirm this order? This will decrement the stock for all products in this order.')
                ->visible(fn (): bool => $this->record->canBeConfirmed())
                ->action(function (): void {
                    if ($this->record->confirm()) {
                        Notification::make()
                            ->title('Order Confirmed')
                            ->body("Order {$this->record->order_number} has been confirmed.")
                            ->success()
                            ->send();

                        $this->refreshFormData(['status', 'confirmed_at']);
                    }
                }),

            Actions\Action::make('updateStatus')
                ->label('Update Status')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->options(Order::STATUSES)
                        ->default(fn () => $this->record->status)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $oldStatus = $this->record->status;
                    $newStatus = $data['status'];

                    // Handle stock adjustment for status changes
                    if ($oldStatus === 'pending' && in_array($newStatus, ['confirmed', 'processing', 'shipped', 'completed'])) {
                        // Decrement stock when moving from pending to confirmed+
                        foreach ($this->record->items as $item) {
                            if ($item->product) {
                                $item->product->decrementStock($item->quantity);
                            }
                        }
                    } elseif (in_array($oldStatus, ['confirmed', 'processing', 'shipped']) && $newStatus === 'cancelled') {
                        // Restore stock when cancelling a confirmed order
                        foreach ($this->record->items as $item) {
                            if ($item->product) {
                                $item->product->incrementStock($item->quantity);
                            }
                        }
                    }

                    $this->record->status = $newStatus;

                    // Set timestamps
                    if ($newStatus === 'confirmed' && !$this->record->confirmed_at) {
                        $this->record->confirmed_at = now();
                    } elseif ($newStatus === 'shipped' && !$this->record->shipped_at) {
                        $this->record->shipped_at = now();
                    } elseif ($newStatus === 'completed' && !$this->record->completed_at) {
                        $this->record->completed_at = now();
                    }

                    $this->record->save();

                    Notification::make()
                        ->title('Status Updated')
                        ->body("Order status changed to " . Order::STATUSES[$newStatus])
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'confirmed_at', 'shipped_at', 'completed_at']);
                }),

            Actions\EditAction::make(),
        ];
    }
}
