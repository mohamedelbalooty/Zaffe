<?php

namespace App\Filament\Resources\InvitationTemplates\Pages;

use App\Filament\Resources\InvitationTemplates\InvitationTemplateResource;
use App\Models\InvitationTemplate;
use App\Support\TemplateStyleCatalog;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditInvitationTemplate extends EditRecord
{
    protected static string $resource = InvitationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label(__('admin.template_editor.actions.preview_live'))
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->url(fn (): string => route('templates.preview', $this->getRecord()->slug))
                ->openUrlInNewTab()
                ->visible(function (): bool {
                    /** @var InvitationTemplate $record */
                    $record = $this->getRecord();

                    return $record->is_active && ($record->category === null || $record->category->is_active);
                }),
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var InvitationTemplate $record */
        $record = $this->getRecord();

        $data['default_styles'] = TemplateStyleCatalog::sanitizeStyles(
            $data['default_styles'] ?? [],
            $record->default_styles ?? [],
        );

        return $data;
    }
}
