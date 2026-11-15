<?php

namespace App\Filament\Resources\RsvpResponses\Pages;

use App\Filament\Resources\RsvpResponses\RsvpResponseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRsvpResponse extends ViewRecord
{
    protected static string $resource = RsvpResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
