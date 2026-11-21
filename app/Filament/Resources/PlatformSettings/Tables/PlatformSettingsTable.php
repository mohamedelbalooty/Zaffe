<?php

namespace App\Filament\Resources\PlatformSettings\Tables;

use App\Models\PlatformSetting;
use App\Support\PlatformSettingCatalog;
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
                    ->label(__('admin.settings.fields.setting'))
                    ->formatStateUsing(fn (string $state): string => PlatformSettingCatalog::labelFor($state))
                    ->description(fn (PlatformSetting $record): ?string => PlatformSettingCatalog::descriptionFor($record->key))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('value')
                    ->label(__('admin.fields.value'))
                    ->limit(60)
                    ->searchable()
                    ->wrap()
                    ->extraAttributes(fn (PlatformSetting $record): array => PlatformSettingCatalog::directionFor($record->key) === 'ltr'
                        ? ['dir' => 'ltr', 'class' => 'text-right font-mono']
                        : [])
                    ->description(fn (PlatformSetting $record): ?string => $record->key === 'whatsapp_support'
                        ? PlatformSettingCatalog::whatsappPreviewUrl($record->value)
                        : null),
                TextColumn::make('group')
                    ->label(__('admin.fields.group'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'contact' => 'success',
                        'seo' => 'info',
                        'localization' => 'warning',
                        default => 'primary',
                    })
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
            ->checkIfRecordIsSelectableUsing(fn (PlatformSetting $record): bool => ! PlatformSettingCatalog::has($record->key))
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
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                ]),
            ]);
    }
}
