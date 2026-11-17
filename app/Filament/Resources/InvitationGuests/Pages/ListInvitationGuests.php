<?php

namespace App\Filament\Resources\InvitationGuests\Pages;

use App\Filament\Resources\InvitationGuests\InvitationGuestResource;
use Filament\Resources\Pages\ListRecords;

class ListInvitationGuests extends ListRecords
{
    protected static string $resource = InvitationGuestResource::class;
}
