<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('admin.fields.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('admin.fields.email'))
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                DateTimePicker::make('email_verified_at')
                    ->label(__('admin.fields.email_verified_at')),
                TextInput::make('password')
                    ->label(__('admin.fields.password'))
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->helperText(__('admin.fields.password_helper')),
                TextInput::make('phone')
                    ->label(__('admin.fields.phone'))
                    ->tel()
                    ->maxLength(30),
                Select::make('role')
                    ->label(__('admin.fields.role'))
                    ->options(fn (): array => __('admin.options.roles'))
                    ->required()
                    ->default('user')
                    ->native(false),
                TextInput::make('avatar_url')
                    ->label(__('admin.fields.avatar_url'))
                    ->rule('regex:/^(https?:\/\/|\/).+/i')
                    ->maxLength(500),
            ]);
    }
}
