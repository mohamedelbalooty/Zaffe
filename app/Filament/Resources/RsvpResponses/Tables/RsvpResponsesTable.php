<?php

namespace App\Filament\Resources\RsvpResponses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RsvpResponsesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invitation.title')
                    ->label(__('admin.fields.invitation'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('guest_name')
                    ->label(__('admin.fields.guest_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('admin.fields.phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('attendance_status')
                    ->label(__('admin.fields.attendance_status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("admin.options.attendance.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'attending' => 'success',
                        'declined' => 'danger',
                        default => 'warning',
                    })
                    ->searchable(),
                TextColumn::make('guest_count')
                    ->label(__('admin.fields.guest_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('children_count')
                    ->label(__('admin.fields.children_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('dietary_notes')
                    ->label(__('admin.fields.dietary_notes'))
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ip_address')
                    ->label(__('admin.fields.ip_address'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('admin.fields.submitted_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('admin.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('attendance_status')
                    ->label(__('admin.fields.attendance_status'))
                    ->options(fn (): array => __('admin.options.attendance')),
                SelectFilter::make('invitation_id')
                    ->relationship('invitation', 'title')
                    ->searchable()
                    ->preload()
                    ->label(__('admin.fields.invitation')),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
