<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('template_id')
                    ->relationship('template', 'id'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('groom_name')
                    ->required(),
                TextInput::make('bride_name')
                    ->required(),
                DatePicker::make('wedding_date')
                    ->required(),
                TextInput::make('wedding_time'),
                TextInput::make('venue_name')
                    ->required(),
                Textarea::make('venue_address')
                    ->columnSpanFull(),
                Textarea::make('venue_map_url')
                    ->columnSpanFull(),
                Textarea::make('welcome_message')
                    ->columnSpanFull(),
                Textarea::make('invitation_message')
                    ->columnSpanFull(),
                TextInput::make('cover_photo'),
                TextInput::make('music_url')
                    ->url(),
                Textarea::make('custom_styles')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                Toggle::make('is_rsvp_enabled')
                    ->required(),
                DatePicker::make('rsvp_deadline'),
                TextInput::make('max_guests_per_rsvp')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('view_count')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
