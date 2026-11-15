<?php

namespace App\Filament\Resources\InvitationTemplates\Pages;

use App\Filament\Resources\InvitationTemplates\InvitationTemplateResource;
use App\Support\TemplateStyleCatalog;
use Filament\Resources\Pages\CreateRecord;

class CreateInvitationTemplate extends CreateRecord
{
    protected static string $resource = InvitationTemplateResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['default_styles'] = TemplateStyleCatalog::sanitizeStyles(
            $data['default_styles'] ?? [],
        );

        return $data;
    }
}
