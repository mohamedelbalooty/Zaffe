<?php

namespace App\Filament\Resources\InvitationTemplates;

use App\Filament\Resources\InvitationTemplates\Pages\CreateInvitationTemplate;
use App\Filament\Resources\InvitationTemplates\Pages\EditInvitationTemplate;
use App\Filament\Resources\InvitationTemplates\Pages\ListInvitationTemplates;
use App\Filament\Resources\InvitationTemplates\Schemas\InvitationTemplateForm;
use App\Filament\Resources\InvitationTemplates\Tables\InvitationTemplatesTable;
use App\Models\InvitationTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class InvitationTemplateResource extends Resource
{
    protected static ?string $model = InvitationTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('admin.resources.invitation_templates.navigation');
    }

    public static function getModelLabel(): string
    {
        return __('admin.resources.invitation_templates.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.resources.invitation_templates.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return InvitationTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvitationTemplatesTable::configure($table);
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
            'index' => ListInvitationTemplates::route('/'),
            'create' => CreateInvitationTemplate::route('/create'),
            'edit' => EditInvitationTemplate::route('/{record}/edit'),
        ];
    }
}
