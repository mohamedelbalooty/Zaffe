<?php

namespace App\Filament\Resources\InvitationTemplates\Tables;

use App\Models\InvitationTemplate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class InvitationTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name_ar')
                    ->label(__('admin.fields.category'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_ar')
                    ->label(__('admin.fields.name_ar'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_en')
                    ->label(__('admin.fields.name_en'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('view_name')
                    ->label(__('admin.fields.view_name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invitations_count')
                    ->counts('invitations')
                    ->label(__('admin.fields.invitations_count'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_premium')
                    ->label(__('admin.fields.is_premium'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
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
                SelectFilter::make('category_id')
                    ->relationship('category', 'name_ar')
                    ->label(__('admin.fields.category')),
                TernaryFilter::make('is_active')
                    ->label(__('admin.fields.is_active')),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label(__('admin.actions.preview'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (InvitationTemplate $record): string => route('templates.preview', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (InvitationTemplate $record): bool => $record->is_active && ($record->category === null || $record->category->is_active)),
                Action::make('toggleActive')
                    ->label(fn (InvitationTemplate $record): string => $record->is_active ? __('admin.actions.deactivate') : __('admin.actions.activate'))
                    ->icon(fn (InvitationTemplate $record): string => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-check-circle')
                    ->color(fn (InvitationTemplate $record): string => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (InvitationTemplate $record): bool => $record->update(['is_active' => ! $record->is_active])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
