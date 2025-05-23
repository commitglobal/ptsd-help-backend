<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Learn;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Group;

class LearnForm
{
    public static function getSchema(): array
    {
        return [
            Repeater::make('pages')
                ->label('Pages')
                ->schema([
                    // Common fields for all pages
                    Select::make('type')
                        ->options([
                            'category' => 'Category',
                            'topic' => 'Topic',
                        ])
                        ->required()
                        ->live(),

                    TextInput::make('label')
                        ->label('Page Title')
                        ->required(),

                    TextInput::make('icon')
                        ->label('Icon URL')
                        ->url(),

                    // Conditional fields based on page type
                    Group::make()
                        ->schema(function (callable $get) {
                            $schemas = [];

                            // Schema for category pages
                            if ($get('type') === 'category') {
                                $schemas[] = $this->getCategoryTopicsSchema();
                            }

                            // Schema for topic pages
                            if ($get('type') === 'topic') {
                                $schemas[] = $this->getTopicContentSchema();
                            }

                            return $schemas;
                        })
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'New Page')
                ->addActionLabel('Add Page')
                ->defaultItems(1),
        ];
    }
}