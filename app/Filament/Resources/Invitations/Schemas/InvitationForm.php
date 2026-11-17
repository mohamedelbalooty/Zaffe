<?php

namespace App\Filament\Resources\Invitations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('admin.fields.user'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('template_id')
                    ->label(__('admin.fields.template'))
                    ->relationship('template', 'name_ar')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label(__('admin.fields.title'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->required()
                    ->maxLength(120)
                    ->alphaDash()
                    ->unique(ignoreRecord: true),
                TextInput::make('groom_name')
                    ->label(__('admin.fields.groom_name'))
                    ->required()
                    ->maxLength(100),
                TextInput::make('bride_name')
                    ->label(__('admin.fields.bride_name'))
                    ->required()
                    ->maxLength(100),
                DatePicker::make('wedding_date')
                    ->label(__('admin.fields.wedding_date'))
                    ->required(),
                TextInput::make('wedding_time')
                    ->label(__('admin.fields.wedding_time'))
                    ->maxLength(20),
                Select::make('timezone')
                    ->label(__('admin.fields.timezone'))
                    ->options(fn (): array => __('admin.options.timezones'))
                    ->required()
                    ->default('Africa/Cairo')
                    ->native(false),
                TextInput::make('venue_name')
                    ->label(__('admin.fields.venue_name'))
                    ->required()
                    ->maxLength(255),
                Textarea::make('venue_address')
                    ->label(__('admin.fields.venue_address'))
                    ->columnSpanFull(),
                Textarea::make('venue_map_url')
                    ->label(__('admin.fields.venue_map_url'))
                    ->columnSpanFull(),
                Textarea::make('welcome_message')
                    ->label(__('admin.fields.welcome_message'))
                    ->columnSpanFull(),
                Textarea::make('invitation_message')
                    ->label(__('admin.fields.invitation_message'))
                    ->columnSpanFull(),
                TextInput::make('cover_photo')
                    ->label(__('admin.fields.cover_photo'))
                    ->maxLength(500),
                TextInput::make('music_url')
                    ->label(__('admin.fields.music_url'))
                    ->url()
                    ->maxLength(500),
                KeyValue::make('custom_styles')
                    ->label(__('admin.fields.custom_styles'))
                    ->keyLabel(__('admin.fields.style_key'))
                    ->valueLabel(__('admin.fields.style_value'))
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(fn (): array => __('admin.options.statuses'))
                    ->required()
                    ->default('draft')
                    ->native(false),
                Select::make('invitation_mode')
                    ->label(__('admin.fields.invitation_mode'))
                    ->options(fn (): array => __('admin.options.invitation_modes'))
                    ->required()
                    ->default('rsvp_qr')
                    ->native(false)
                    ->live(),
                Toggle::make('is_rsvp_enabled')
                    ->label(__('admin.fields.is_rsvp_enabled'))
                    ->default(true)
                    ->required(),
                DatePicker::make('rsvp_deadline')
                    ->label(__('admin.fields.rsvp_deadline')),
                TextInput::make('max_guests_per_rsvp')
                    ->label(__('admin.fields.max_guests_per_rsvp'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(20)
                    ->default(5),
                TextInput::make('view_count')
                    ->label(__('admin.fields.view_count'))
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(fn (string $operation): bool => $operation === 'create'),
            ]);
    }
}
