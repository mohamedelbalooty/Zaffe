<?php

namespace App\Filament\Resources\TemplateCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TemplateCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')
                    ->label(__('admin.fields.name_ar'))
                    ->required(),
                TextInput::make('name_en')
                    ->label(__('admin.fields.name_en'))
                    ->required(),
                TextInput::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->required()
                    ->maxLength(100)
                    ->alphaDash()
                    ->unique(ignoreRecord: true),
                TextInput::make('icon')
                    ->label(__('admin.fields.icon'))
                    ->required()
                    ->default('sparkles'),
                Textarea::make('description_ar')
                    ->label(__('admin.fields.description_ar'))
                    ->columnSpanFull(),
                Textarea::make('description_en')
                    ->label(__('admin.fields.description_en'))
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->default(true)
                    ->required(),
                TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
