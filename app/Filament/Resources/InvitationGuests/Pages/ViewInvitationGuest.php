<?php

namespace App\Filament\Resources\InvitationGuests\Pages;

use App\Filament\Resources\InvitationGuests\InvitationGuestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvitationGuest extends ViewRecord
{
    protected static string $resource = InvitationGuestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
