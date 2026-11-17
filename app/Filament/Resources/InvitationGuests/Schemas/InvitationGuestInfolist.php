<?php

namespace App\Filament\Resources\InvitationGuests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvitationGuestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.guest_pass_details'))
                    ->columns(2)
                    ->components([
                        TextEntry::make('invitation.title')
                            ->label(__('admin.fields.invitation')),
                        TextEntry::make('name')
                            ->label(__('admin.fields.guest_name')),
                        TextEntry::make('phone')
                            ->label(__('admin.fields.phone'))
                            ->placeholder('—'),
                        TextEntry::make('category')
                            ->label(__('admin.fields.guest_category'))
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => __("admin.options.guest_categories.{$state}")),
                        TextEntry::make('pass_code')
                            ->label(__('admin.fields.pass_code')),
                        TextEntry::make('rsvp_status')
                            ->label(__('admin.fields.attendance_status'))
                            ->badge(),
                        TextEntry::make('max_adults')
                            ->label(__('admin.fields.max_adults'))
                            ->numeric(),
                        TextEntry::make('max_children')
                            ->label(__('admin.fields.max_children'))
                            ->numeric(),
                        TextEntry::make('invited_at')
                            ->label(__('admin.fields.invited_at'))
                            ->dateTime()
                            ->placeholder('—'),
                    ]),
            ]);
    }
}
