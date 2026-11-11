<?php

namespace App\Filament\Resources\InvitationTemplates\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class InvitationTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->relationship('category', 'id'),
                TextInput::make('name_ar')
                    ->required(),
                TextInput::make('name_en')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description_ar')
                    ->columnSpanFull(),
                Textarea::make('description_en')
                    ->columnSpanFull(),
                TextInput::make('thumbnail'),
                TextInput::make('preview_url')
                    ->url(),
                TextInput::make('view_name')
                    ->required(),
                Textarea::make('default_styles')
                    ->columnSpanFull(),
                Toggle::make('is_premium')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
