<?php

namespace App\Filament\Resources\PlatformSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PlatformSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label(__('admin.fields.key'))
                    ->required()
                    ->maxLength(120)
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->helperText(__('admin.fields.key_helper')),
                Textarea::make('value')
                    ->label(__('admin.fields.value'))
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('group')
                    ->label(__('admin.fields.group'))
                    ->options(fn (): array => __('admin.options.setting_groups'))
                    ->required()
                    ->default('general')
                    ->native(false),
            ]);
    }
}
