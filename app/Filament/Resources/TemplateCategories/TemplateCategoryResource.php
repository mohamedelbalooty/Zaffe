<?php

namespace App\Filament\Resources\TemplateCategories;

use App\Filament\Resources\TemplateCategories\Pages\CreateTemplateCategory;
use App\Filament\Resources\TemplateCategories\Pages\EditTemplateCategory;
use App\Filament\Resources\TemplateCategories\Pages\ListTemplateCategories;
use App\Filament\Resources\TemplateCategories\Schemas\TemplateCategoryForm;
use App\Filament\Resources\TemplateCategories\Tables\TemplateCategoriesTable;
use App\Models\TemplateCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TemplateCategoryResource extends Resource
{
    protected static ?string $model = TemplateCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TemplateCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TemplateCategoriesTable::configure($table);
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
            'index' => ListTemplateCategories::route('/'),
            'create' => CreateTemplateCategory::route('/create'),
            'edit' => EditTemplateCategory::route('/{record}/edit'),
        ];
    }
}
