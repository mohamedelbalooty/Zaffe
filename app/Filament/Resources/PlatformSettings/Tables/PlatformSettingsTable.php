<?php

namespace App\Filament\Resources\PlatformSettings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PlatformSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label(__('admin.fields.key'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('value')
                    ->label(__('admin.fields.value'))
                    ->limit(60)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('group')
                    ->label(__('admin.fields.group'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("admin.options.setting_groups.{$state}"))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('admin.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label(__('admin.fields.group'))
                    ->options(fn (): array => __('admin.options.setting_groups')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
