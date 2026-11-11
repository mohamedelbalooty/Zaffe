<?php

namespace App\Filament\Resources\InvitationTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvitationTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.id')
                    ->searchable(),
                TextColumn::make('name_ar')
                    ->searchable(),
                TextColumn::make('name_en')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('thumbnail')
                    ->searchable(),
                TextColumn::make('preview_url')
                    ->searchable(),
                TextColumn::make('view_name')
                    ->searchable(),
                IconColumn::make('is_premium')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
