<?php

namespace App\Filament\Resources\Invitations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('template.id')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('groom_name')
                    ->searchable(),
                TextColumn::make('bride_name')
                    ->searchable(),
                TextColumn::make('wedding_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('wedding_time')
                    ->searchable(),
                TextColumn::make('venue_name')
                    ->searchable(),
                TextColumn::make('cover_photo')
                    ->searchable(),
                TextColumn::make('music_url')
                    ->searchable(),
                TextColumn::make('status')
                    ->searchable(),
                IconColumn::make('is_rsvp_enabled')
                    ->boolean(),
                TextColumn::make('rsvp_deadline')
                    ->date()
                    ->sortable(),
                TextColumn::make('max_guests_per_rsvp')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('view_count')
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
