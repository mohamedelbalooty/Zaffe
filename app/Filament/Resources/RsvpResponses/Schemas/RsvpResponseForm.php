<?php

namespace App\Filament\Resources\RsvpResponses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RsvpResponseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('invitation_id')
                    ->relationship('invitation', 'title')
                    ->required(),
                TextInput::make('guest_name')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('attendance_status')
                    ->required()
                    ->default('attending'),
                TextInput::make('guest_count')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('dietary_notes'),
                TextInput::make('ip_address'),
            ]);
    }
}
