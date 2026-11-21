<?php

namespace App\Filament\Resources\InvitationGuests;

use App\Filament\Resources\InvitationGuests\Pages\ListInvitationGuests;
use App\Filament\Resources\InvitationGuests\Pages\ViewInvitationGuest;
use App\Filament\Resources\InvitationGuests\Schemas\InvitationGuestInfolist;
use App\Filament\Resources\InvitationGuests\Tables\InvitationGuestsTable;
use App\Models\InvitationGuest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvitationGuestResource extends Resource
{
    protected static ?string $model = InvitationGuest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?int $navigationSort = 7;

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.invitation_guests.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.invitation_guests.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.invitation_guests.plural');
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvitationGuestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvitationGuestsTable::configure($table);
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
            'index' => ListInvitationGuests::route('/'),
            'view' => ViewInvitationGuest::route('/{record}'),
        ];
    }
}
