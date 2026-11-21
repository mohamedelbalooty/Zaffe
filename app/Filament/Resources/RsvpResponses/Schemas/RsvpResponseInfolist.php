<?php

namespace App\Filament\Resources\RsvpResponses\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RsvpResponseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.rsvp_details'))
                    ->columns(2)
                    ->components([
                        TextEntry::make('invitation.title')
                            ->label(__('admin.fields.invitation')),
                        TextEntry::make('invitation.slug')
                            ->label(__('admin.fields.invitation_slug')),
                        TextEntry::make('guest_name')
                            ->label(__('admin.fields.guest_name')),
                        TextEntry::make('phone')
                            ->label(__('admin.fields.phone')),
                        TextEntry::make('email')
                            ->label(__('admin.fields.email'))
                            ->placeholder('—'),
                        TextEntry::make('attendance_status')
                            ->label(__('admin.fields.attendance_status'))
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => __("admin.options.attendance.{$state}"))
                            ->color(fn (string $state): string => match ($state) {
                                'attending' => 'success',
                                'declined' => 'danger',
                                default => 'warning',
                            }),
                        TextEntry::make('guest_count')
                            ->label(__('admin.fields.guest_count'))
                            ->numeric(),
                        TextEntry::make('children_count')
                            ->label(__('admin.fields.children_count'))
                            ->numeric(),
                        TextEntry::make('created_at')
                            ->label(__('admin.fields.submitted_at'))
                            ->dateTime(),
                        TextEntry::make('message')
                            ->label(__('admin.fields.message'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('dietary_notes')
                            ->label(__('admin.fields.dietary_notes'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
