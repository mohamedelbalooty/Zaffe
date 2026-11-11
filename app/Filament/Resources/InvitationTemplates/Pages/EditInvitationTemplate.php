<?php

namespace App\Filament\Resources\InvitationTemplates\Pages;

use App\Filament\Resources\InvitationTemplates\InvitationTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvitationTemplate extends EditRecord
{
    protected static string $resource = InvitationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
