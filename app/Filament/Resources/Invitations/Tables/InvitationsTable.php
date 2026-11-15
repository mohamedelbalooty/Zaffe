<?php

namespace App\Filament\Resources\Invitations\Tables;

use App\Models\Invitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('admin.fields.user'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('template.name_ar')
                    ->label(__('admin.fields.template'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->searchable(),
                TextColumn::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('groom_name')
                    ->label(__('admin.fields.groom_name'))
                    ->searchable(),
                TextColumn::make('bride_name')
                    ->label(__('admin.fields.bride_name'))
                    ->searchable(),
                TextColumn::make('wedding_date')
                    ->label(__('admin.fields.wedding_date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('timezone')
                    ->label(__('admin.fields.timezone'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __("admin.options.statuses.{$state}"))
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                IconColumn::make('is_rsvp_enabled')
                    ->label(__('admin.fields.is_rsvp_enabled'))
                    ->boolean(),
                TextColumn::make('rsvps_count')
                    ->counts('rsvps')
                    ->label(__('admin.fields.rsvps_count'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('view_count')
                    ->label(__('admin.fields.view_count'))
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
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(fn (): array => __('admin.options.statuses')),
                SelectFilter::make('timezone')
                    ->label(__('admin.fields.timezone'))
                    ->options(fn (): array => __('admin.options.timezones')),
                SelectFilter::make('template_id')
                    ->relationship('template', 'name_ar')
                    ->label(__('admin.fields.template')),
            ])
            ->recordActions([
                Action::make('viewPublic')
                    ->label(__('admin.actions.view_public'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Invitation $record): string => route('invitation.show', $record->slug))
                    ->openUrlInNewTab()
                    ->visible(fn (Invitation $record): bool => $record->status === 'published'),
                Action::make('publish')
                    ->label(__('admin.actions.publish'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Invitation $record): bool => $record->status !== 'published')
                    ->action(fn (Invitation $record): bool => $record->update(['status' => 'published'])),
                Action::make('unpublish')
                    ->label(__('admin.actions.unpublish'))
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Invitation $record): bool => $record->status === 'published')
                    ->action(fn (Invitation $record): bool => $record->update(['status' => 'draft'])),
                Action::make('archive')
                    ->label(__('admin.actions.archive'))
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (Invitation $record): bool => $record->status !== 'archived')
                    ->action(fn (Invitation $record): bool => $record->update(['status' => 'archived'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
