<?php

namespace App\Filament\Resources\ResourceMaterials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ResourceMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
               FileUpload::make('file')
                    ->disk('public')
                    ->directory('resources')
                    ->maxSize(102400) // 100MB in KB
                    ->preserveFilenames()
                    ->downloadable()
                    ->openable()
                    ->required(),
                Select::make('type')
                    ->options(['pdf' => 'Pdf', 'audio' => 'Audio', 'video' => 'Video'])
                    ->required(),
            ]);
    }
}
