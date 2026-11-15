<?php

namespace App\Filament\Resources\RsvpResponses;

use App\Filament\Resources\RsvpResponses\Pages\ListRsvpResponses;
use App\Filament\Resources\RsvpResponses\Pages\ViewRsvpResponse;
use App\Filament\Resources\RsvpResponses\Schemas\RsvpResponseInfolist;
use App\Filament\Resources\RsvpResponses\Tables\RsvpResponsesTable;
use App\Models\RsvpResponse;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class RsvpResponseResource extends Resource
{
    protected static ?string $model = RsvpResponse::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.rsvp_responses.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.rsvp_responses.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.rsvp_responses.plural');
    }

    public static function infolist(Schema $schema): Schema
    {
        return RsvpResponseInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RsvpResponsesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRsvpResponses::route('/'),
            'view' => ViewRsvpResponse::route('/{record}'),
        ];
    }
}
