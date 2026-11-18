<?php

namespace App\Filament\Resources\InvitationTemplates\Schemas;

use App\Models\InvitationTemplate;
use App\Support\TemplateStyleCatalog;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class InvitationTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(['lg' => 3])
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->components([
                        self::identitySection(),
                        self::defaultStylesSection(),
                        self::technicalMediaSection(),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->components([
                        self::publishingSection(),
                        self::usageSummarySection(),
                    ]),
            ]);
    }

    protected static function identitySection(): Section
    {
        return Section::make(__('admin.template_editor.sections.identity'))
            ->description(__('admin.template_editor.sections.identity_desc'))
            ->icon(Heroicon::OutlinedSparkles)
            ->columns(2)
            ->components([
                Select::make('category_id')
                    ->label(__('admin.fields.category'))
                    ->relationship('category', 'name_ar')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->required()
                    ->maxLength(120)
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->helperText(__('admin.template_editor.helpers.slug')),

                TextInput::make('name_ar')
                    ->label(__('admin.fields.name_ar'))
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),

                TextInput::make('name_en')
                    ->label(__('admin.fields.name_en'))
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, Set $set, Get $get, ?string $state): void {
                        if ($operation === 'create' && blank($get('slug')) && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),

                Textarea::make('description_ar')
                    ->label(__('admin.fields.description_ar'))
                    ->rows(2)
                    ->columnSpan(1),

                Textarea::make('description_en')
                    ->label(__('admin.fields.description_en'))
                    ->rows(2)
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->columnSpan(1),
            ]);
    }

    protected static function defaultStylesSection(): Section
    {
        return Section::make(__('admin.template_editor.sections.styles'))
            ->key('default_styles_section')
            ->description(__('admin.template_editor.sections.styles_desc'))
            ->icon(Heroicon::OutlinedSwatch)
            ->columns(['md' => 12])
            ->headerActions([
                self::presetsActionGroup(),
            ])
            ->components([
                Group::make()
                    ->statePath('default_styles')
                    ->columnSpan(['md' => 7])
                    ->columns(2)
                    ->components([
                        self::hexColorPicker(
                            'primary_color',
                            __('admin.template_editor.styles.primary_color'),
                            __('admin.template_editor.styles.primary_color_helper'),
                            TemplateStyleCatalog::DEFAULTS['primary_color'],
                        ),
                        self::hexColorPicker(
                            'secondary_color',
                            __('admin.template_editor.styles.secondary_color'),
                            __('admin.template_editor.styles.secondary_color_helper'),
                            TemplateStyleCatalog::DEFAULTS['secondary_color'],
                        ),
                        self::hexColorPicker(
                            'background_color',
                            __('admin.template_editor.styles.background_color'),
                            __('admin.template_editor.styles.background_color_helper'),
                            TemplateStyleCatalog::DEFAULTS['background_color'],
                        ),
                        self::hexColorPicker(
                            'card_color',
                            __('admin.template_editor.styles.card_color'),
                            __('admin.template_editor.styles.card_color_helper'),
                            TemplateStyleCatalog::DEFAULTS['card_color'],
                        ),
                        self::hexColorPicker(
                            'accent_color',
                            __('admin.template_editor.styles.accent_color'),
                            __('admin.template_editor.styles.accent_color_helper'),
                            TemplateStyleCatalog::DEFAULTS['accent_color'],
                        )->columnSpanFull(),

                        Select::make('font_family')
                            ->label(__('admin.template_editor.styles.font_family'))
                            ->options(fn (?string $state): array => TemplateStyleCatalog::fonts($state))
                            ->default(fn (string $operation): ?string => $operation === 'create'
                                ? TemplateStyleCatalog::DEFAULTS['font_family']
                                : null)
                            ->native(false)
                            ->live()
                            ->nullable()
                            ->columnSpanFull(),

                        Select::make('border_style')
                            ->label(__('admin.template_editor.styles.border_style'))
                            ->options(fn (?string $state): array => TemplateStyleCatalog::borderStyles($state))
                            ->default(fn (string $operation): ?string => $operation === 'create'
                                ? TemplateStyleCatalog::DEFAULTS['border_style']
                                : null)
                            ->searchable()
                            ->live()
                            ->nullable(),

                        Select::make('ornament_style')
                            ->label(__('admin.template_editor.styles.ornament_style'))
                            ->options(fn (?string $state): array => TemplateStyleCatalog::ornamentStyles($state))
                            ->default(fn (string $operation): ?string => $operation === 'create'
                                ? TemplateStyleCatalog::DEFAULTS['ornament_style']
                                : null)
                            ->searchable()
                            ->live()
                            ->nullable(),
                    ]),

                Html::make(fn (Get $get): string => view('filament.components.template-style-preview', [
                    'styles' => $get('default_styles') ?? [],
                ])->render())
                    ->columnSpan(['md' => 5]),
            ]);
    }

    protected static function technicalMediaSection(): Section
    {
        return Section::make(__('admin.template_editor.sections.media_technical'))
            ->description(__('admin.template_editor.sections.media_technical_desc'))
            ->icon(Heroicon::OutlinedCodeBracket)
            ->columns(2)
            ->components([
                TextInput::make('view_name')
                    ->label(__('admin.fields.view_name'))
                    ->required()
                    ->maxLength(255)
                    ->in(TemplateStyleCatalog::bladeViews())
                    ->datalist(TemplateStyleCatalog::bladeViews())
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->helperText(__('admin.template_editor.helpers.view_name')),

                TextInput::make('thumbnail')
                    ->label(__('admin.fields.thumbnail'))
                    ->maxLength(500)
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->helperText(__('admin.template_editor.helpers.thumbnail')),

                TextInput::make('preview_url')
                    ->label(__('admin.fields.preview_url'))
                    ->url()
                    ->maxLength(500)
                    ->extraInputAttributes(['dir' => 'ltr'])
                    ->columnSpanFull(),
            ]);
    }

    protected static function publishingSection(): Section
    {
        return Section::make(__('admin.template_editor.sections.publishing'))
            ->description(__('admin.template_editor.sections.publishing_desc'))
            ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
            ->components([
                Toggle::make('is_active')
                    ->label(__('admin.fields.is_active'))
                    ->default(true)
                    ->required()
                    ->helperText(__('admin.template_editor.helpers.is_active')),

                TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->helperText(__('admin.template_editor.helpers.sort_order')),

                Toggle::make('is_premium')
                    ->label(__('admin.fields.is_premium_label'))
                    ->default(false)
                    ->disabled()
                    ->dehydrated(true)
                    ->afterStateHydrated(fn ($component) => $component->state(false))
                    ->helperText(__('admin.fields.is_premium_helper')),
            ]);
    }

    protected static function usageSummarySection(): Section
    {
        return Section::make(__('admin.template_editor.sections.usage'))
            ->icon(Heroicon::OutlinedChartBar)
            ->visibleOn('edit')
            ->components([
                TextEntry::make('invitations_usage_count')
                    ->label(__('admin.template_editor.helpers.invitations_using'))
                    ->state(fn (?InvitationTemplate $record): int => $record?->invitations()->count() ?? 0)
                    ->numeric()
                    ->badge()
                    ->color('primary'),

                TextEntry::make('styles_impact_notice')
                    ->hiddenLabel()
                    ->state(__('admin.template_editor.helpers.styles_impact_note')),
            ]);
    }

    protected static function hexColorPicker(
        string $name,
        string $label,
        string $helperText,
        string $fallbackDefault,
    ): ColorPicker {
        return ColorPicker::make($name)
            ->label($label)
            ->hex()
            ->placeholder($fallbackDefault)
            ->default(fn (string $operation): ?string => $operation === 'create' ? $fallbackDefault : null)
            ->nullable()
            ->regex('/^#[0-9A-Fa-f]{6}$/')
            ->live(onBlur: true)
            ->extraInputAttributes(['dir' => 'ltr'])
            ->helperText($helperText);
    }

    protected static function presetsActionGroup(): ActionGroup
    {
        $actions = [];

        foreach (TemplateStyleCatalog::presets() as $key => $styles) {
            $actions[] = Action::make("apply_preset_{$key}")
                ->label(__("admin.template_editor.presets.{$key}"))
                ->icon(Heroicon::OutlinedSwatch)
                ->action(function (Set $set) use ($styles): void {
                    $set('default_styles', $styles);

                    Notification::make()
                        ->title(__('admin.template_editor.actions.preset_applied'))
                        ->success()
                        ->send();
                });
        }

        return ActionGroup::make($actions)
            ->label(__('admin.template_editor.actions.apply_preset'))
            ->icon(Heroicon::OutlinedSparkles)
            ->button()
            ->color('gray')
            ->size('sm');
    }
}
