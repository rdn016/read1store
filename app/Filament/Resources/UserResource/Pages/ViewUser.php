<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (User $record): bool => !$record->hasRole('super_admin')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Full Name')
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable()
                            ->icon('heroicon-o-envelope'),
                    ])->columns(3),

                Infolists\Components\Section::make('Roles & Permissions')
                    ->schema([
                        Infolists\Components\TextEntry::make('roles.name')
                            ->label('Assigned Roles')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'super_admin' => 'danger',
                                'admin' => 'warning',
                                'panel_user' => 'success',
                                default => 'gray',
                            })
                            ->separator(', ')
                            ->placeholder('No roles assigned'),
                    ]),

                Infolists\Components\Section::make('Account Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label('User ID'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Account Created')
                            ->dateTime('d M Y, H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('d M Y, H:i'),
                    ])->columns(3),
            ]);
    }
}
