<?php

namespace App\Filament\Resources\PlatformSettings\Schemas;

use App\Models\PlatformSetting;
use App\Support\PlatformSettingCatalog;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PlatformSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        $record = $schema->getRecord();
        $isKnownSetting = $record instanceof PlatformSetting && PlatformSettingCatalog::has($record->key);

        if ($isKnownSetting) {
            return $schema->components([
                Section::make(PlatformSettingCatalog::labelFor($record->key))
                    ->description(PlatformSettingCatalog::descriptionFor($record->key))
                    ->icon(self::iconForType(PlatformSettingCatalog::typeFor($record->key)))
                    ->components([
                        self::makeSettingInput('value', $record->key)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
        }

        return $schema->components([
            Section::make(__('admin.resources.platform_settings.singular'))
                ->description(__('admin.settings.sections.custom_table_notice'))
                ->icon(Heroicon::OutlinedCog6Tooth)
                ->columns(2)
                ->components([
                    TextInput::make('key')
                        ->label(__('admin.settings.fields.custom_key'))
                        ->required()
                        ->maxLength(120)
                        ->regex('/^[a-z0-9_.-]+$/i')
                        ->unique(ignoreRecord: true)
                        ->visibleOn('create')
                        ->extraInputAttributes(['dir' => 'ltr'])
                        ->helperText(__('admin.settings.fields.custom_key_helper')),

                    Select::make('group')
                        ->label(__('admin.fields.group'))
                        ->options(fn (): array => __('admin.options.setting_groups'))
                        ->required()
                        ->default('general')
                        ->native(false),

                    Textarea::make('value')
                        ->label(__('admin.fields.value'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    /**
     * Build a typed, direction-aware input component for a canonical platform setting key.
     */
    public static function makeSettingInput(string $fieldName, string $key, bool $live = false): TextInput|Textarea
    {
        $type = PlatformSettingCatalog::typeFor($key);
        $label = PlatformSettingCatalog::labelFor($key);
        $description = PlatformSettingCatalog::descriptionFor($key);
        $placeholder = PlatformSettingCatalog::placeholderFor($key);
        $direction = PlatformSettingCatalog::directionFor($key);

        if ($type === 'textarea') {
            $component = Textarea::make($fieldName)
                ->label($label)
                ->rows(3);
        } else {
            $component = TextInput::make($fieldName)
                ->label($label)
                ->maxLength(255)
                ->required()
                ->prefixIcon(self::iconForType($type));

            if ($type === 'email') {
                $component->email();
            } elseif (in_array($type, ['phone', 'whatsapp'], true)) {
                $component->tel();
            }
        }

        if ($description !== null) {
            $component->helperText($description);
        }

        if ($placeholder !== null) {
            $component->placeholder($placeholder);
        }

        if ($direction === 'ltr') {
            $component->extraInputAttributes(['dir' => 'ltr']);
        }

        if ($live) {
            $component->live(onBlur: true);
        }

        return $component;
    }

    public static function iconForType(string $type): Heroicon
    {
        return match ($type) {
            'email' => Heroicon::OutlinedEnvelope,
            'phone' => Heroicon::OutlinedPhone,
            'whatsapp' => Heroicon::OutlinedChatBubbleOvalLeftEllipsis,
            default => Heroicon::OutlinedSparkles,
        };
    }
}
