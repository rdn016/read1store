<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Spatie\Permission\Models\Role;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (Role $record): bool => $record->name !== 'super_admin'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Role Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Role Name')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'super_admin' => 'danger',
                                'admin' => 'warning',
                                'panel_user' => 'success',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('guard_name')
                            ->label('Guard')
                            ->badge()
                            ->color('info'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y, H:i'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('d M Y, H:i'),
                    ])->columns(4),

                Infolists\Components\Section::make('Permissions')
                    ->schema([
                        Infolists\Components\TextEntry::make('permissions.name')
                            ->label('Assigned Permissions')
                            ->badge()
                            ->color('success')
                            ->separator(', ')
                            ->placeholder('No permissions assigned'),
                    ]),

                Infolists\Components\Section::make('Users with this Role')
                    ->schema([
                        Infolists\Components\TextEntry::make('users.name')
                            ->label('Users')
                            ->badge()
                            ->color('primary')
                            ->separator(', ')
                            ->placeholder('No users assigned'),
                    ]),
            ]);
    }
}
