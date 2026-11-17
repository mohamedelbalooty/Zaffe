<?php

namespace App\Filament\Resources\InvitationGuests\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvitationGuestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invitation.title')
                    ->label(__('admin.fields.invitation'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('admin.fields.guest_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('admin.fields.phone'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(__('admin.fields.guest_category'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("admin.options.guest_categories.{$state}")),
                TextColumn::make('pass_code')
                    ->label(__('admin.fields.pass_code'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('rsvp_status')
                    ->label(__('admin.fields.attendance_status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'attending' => 'success',
                        'declined' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('max_adults')
                    ->label(__('admin.fields.max_adults'))
                    ->numeric(),
                TextColumn::make('max_children')
                    ->label(__('admin.fields.max_children'))
                    ->numeric(),
                TextColumn::make('invited_at')
                    ->label(__('admin.fields.invited_at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('rsvp_status')
                    ->label(__('admin.fields.attendance_status'))
                    ->options([
                        'pending' => 'Pending',
                        'attending' => 'Attending',
                        'declined' => 'Declined',
                        'tentative' => 'Tentative',
                    ]),
                SelectFilter::make('category')
                    ->label(__('admin.fields.guest_category'))
                    ->options(fn (): array => __('admin.options.guest_categories')),
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
