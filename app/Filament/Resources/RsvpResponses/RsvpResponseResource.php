<?php

namespace App\Filament\Resources\RsvpResponses;

use App\Filament\Resources\RsvpResponses\Pages\CreateRsvpResponse;
use App\Filament\Resources\RsvpResponses\Pages\EditRsvpResponse;
use App\Filament\Resources\RsvpResponses\Pages\ListRsvpResponses;
use App\Filament\Resources\RsvpResponses\Schemas\RsvpResponseForm;
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

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return RsvpResponseForm::configure($schema);
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
            'create' => CreateRsvpResponse::route('/create'),
            'edit' => EditRsvpResponse::route('/{record}/edit'),
        ];
    }
}
