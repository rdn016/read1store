<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Role Details')
                    ->description('Define the role name and assign permissions')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->unique(Role::class, 'name', ignoreRecord: true)
                            ->placeholder('Enter role name'),

                        Forms\Components\TextInput::make('guard_name')
                            ->default('web')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Permissions')
                    ->description('Select permissions for this role')
                    ->schema([
                        Forms\Components\CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->columns(3)
                            ->searchable()
                            ->bulkToggleable()
                            ->gridDirection('row'),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'panel_user' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Users')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Role $record): bool => $record->name !== 'super_admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $records = $records->reject(fn (Role $record) => $record->name === 'super_admin');
                        }),
                ]),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
