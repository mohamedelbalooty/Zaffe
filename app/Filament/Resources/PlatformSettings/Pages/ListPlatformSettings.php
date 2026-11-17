<?php

namespace App\Filament\Resources\PlatformSettings\Pages;

use App\Filament\Resources\PlatformSettings\PlatformSettingResource;
use App\Filament\Resources\PlatformSettings\Schemas\PlatformSettingForm;
use App\Models\PlatformSetting;
use App\Support\PlatformSettingCatalog;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ListPlatformSettings extends ListRecords
{
    protected static string $resource = PlatformSettingResource::class;

    /**
     * @var array<string, mixed> | null
     */
    public ?array $settingsData = [];

    public function mount(): void
    {
        parent::mount();

        $this->loadSettingsState();
    }

    public function getSubheading(): ?string
    {
        return __('admin.settings.subheading');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('admin.settings.actions.add_custom'))
                ->color('gray'),
        ];
    }

    public function settingsForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('settingsData')
            ->columns(['lg' => 3])
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->components([
                        Section::make(__('admin.settings.sections.branding'))
                            ->description(__('admin.settings.sections.branding_desc'))
                            ->icon(Heroicon::OutlinedSparkles)
                            ->columns(2)
                            ->components([
                                PlatformSettingForm::makeSettingInput('site_name_ar', 'site_name_ar', live: true)
                                    ->columnSpanFull(),
                                PlatformSettingForm::makeSettingInput('site_name_en', 'site_name_en', live: true)
                                    ->columnSpanFull(),
                            ]),

                        Section::make(__('admin.settings.sections.contact'))
                            ->description(__('admin.settings.sections.contact_desc'))
                            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                            ->columns(2)
                            ->components([
                                PlatformSettingForm::makeSettingInput('contact_email', 'contact_email', live: true)
                                    ->columnSpanFull(),
                                PlatformSettingForm::makeSettingInput('contact_phone', 'contact_phone', live: true),
                                PlatformSettingForm::makeSettingInput('whatsapp_support', 'whatsapp_support', live: true),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->components([
                        Section::make(__('admin.settings.sections.preview'))
                            ->description(__('admin.settings.sections.preview_desc'))
                            ->icon(Heroicon::OutlinedEye)
                            ->components([
                                Html::make(fn (Get $get): string => view('filament.components.platform-settings-preview', [
                                    'settings' => [
                                        'site_name_ar' => $get('site_name_ar'),
                                        'site_name_en' => $get('site_name_en'),
                                        'contact_email' => $get('contact_email'),
                                        'contact_phone' => $get('contact_phone'),
                                        'whatsapp_support' => $get('whatsapp_support'),
                                    ],
                                ])->render()),
                            ]),
                    ]),
            ]);
    }

    public function saveSettings(): void
    {
        $state = $this->settingsForm->getState();

        foreach (PlatformSettingCatalog::keys() as $key) {
            if (! array_key_exists($key, $state)) {
                continue;
            }

            PlatformSetting::set(
                $key,
                trim((string) $state[$key]),
                PlatformSettingCatalog::groupFor($key)
            );
        }

        $this->loadSettingsState();

        Notification::make()
            ->title(__('admin.settings.actions.saved_title'))
            ->body(__('admin.settings.actions.saved_body'))
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('settings_workspace')
                    ->contained(false)
                    ->tabs([
                        Tab::make(__('admin.settings.tabs.general_workspace'))
                            ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                            ->schema([
                                Form::make([EmbeddedSchema::make('settingsForm')])
                                    ->id('settingsForm')
                                    ->livewireSubmitHandler('saveSettings')
                                    ->footer([
                                        Actions::make([
                                            Action::make('saveSettings')
                                                ->label(__('admin.settings.actions.save'))
                                                ->icon(Heroicon::OutlinedCheckCircle)
                                                ->submit('saveSettings'),
                                        ]),
                                    ]),
                            ]),

                        Tab::make(__('admin.settings.tabs.all_settings'))
                            ->icon(Heroicon::OutlinedQueueList)
                            ->badge(fn (): int => PlatformSetting::query()->count())
                            ->schema([
                                EmbeddedTable::make(),
                            ]),
                    ]),
            ]);
    }

    protected function loadSettingsState(): void
    {
        $values = [];

        foreach (PlatformSettingCatalog::definitions() as $key => $definition) {
            $values[$key] = (string) PlatformSetting::get($key, $definition['default']);
        }

        $this->settingsForm->fill($values);
    }
}
