<?php

namespace App\Filament\Resources\RsvpResponses\Pages;

use App\Filament\Resources\RsvpResponses\RsvpResponseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRsvpResponses extends ListRecords
{
    protected static string $resource = RsvpResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
