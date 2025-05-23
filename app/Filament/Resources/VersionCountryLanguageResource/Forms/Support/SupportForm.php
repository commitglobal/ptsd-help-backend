<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms\Support;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class SupportForm
{
    public static function getSchema(): array
    {
        return [
            Repeater::make('support')
                ->label('Support Pages')
                ->schema([
                    // Page type selector (topic/category)
                    Select::make('type')
                        ->options([
                            'topic' => 'Topic Page',
                            'category' => 'Category Page',
                        ])
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    // Common fields for both types
                    TextInput::make('label')
                        ->label('Page Title')
                        ->required(),

                    TextInput::make('icon')
                        ->label('Icon URL')
                        ->url()
                        ->columnSpanFull(),

                    // Conditional fields based on page type
                    Group::make()
                        ->schema(function (callable $get) {
                            $schemas = [];

                            // Schema for topic pages
                            if ($get('type') === 'topic') {
                                $schemas[] = SupportForm::getTopicContentSchema();
                            }

                            // Schema for category pages
                            if ($get('type') === 'category') {
                                $schemas[] = SupportForm::getCategoryTopicsSchema();
                            }

                            return $schemas;
                        })
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->collapsible()
                ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'New Page')
                ->addActionLabel('Add New Page')
                ->defaultItems(1),
        ];
    }

    protected static function getTopicContentSchema(): Section
    {
        return Section::make('Page Content')
            ->schema([
                Repeater::make('content.sections')
                    ->label('Content Sections')
                    ->schema([
                        Select::make('type')
                            ->options([
                                'rich-text' => 'Rich Text',
                                'button' => 'Button',
                            ])
                            ->required()
                            ->live(),

                        // Rich Text Content
                        RichEditor::make('content')
                            ->label('Text Content')
                            ->columnSpanFull()
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->visible(fn(callable $get) => $get('type') === 'rich-text'),

                        // Button Content
                        TextInput::make('label')
                            ->label('Button Text')
                            ->required()
                            ->visible(fn(callable $get) => $get('type') === 'button'),

                        Select::make('action.type')
                            ->label('Button Action')
                            ->options([
                                'phone' => 'Phone Number',
                                'external' => 'External Link',
                            ])
                            ->required()
                            ->live()
                            ->visible(fn(callable $get) => $get('type') === 'button'),

                        TextInput::make('action.number')
                            ->label('Phone Number')
                            ->required()
                            ->visible(fn(callable $get) => $get('type') === 'button' &&
                                $get('action.type') === 'phone'),

                        TextInput::make('action.url')
                            ->label('URL')
                            ->required()
                            ->url()
                            ->visible(fn(callable $get) => $get('type') === 'button' &&
                                $get('action.type') === 'external'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['type'] === 'rich-text' ? 'Text Section' : ($state['label'] ?? 'Button'))
                    ->addActionLabel('Add Section'),
            ]);
    }

    protected static function getCategoryTopicsSchema(): Section
    {
        return Section::make('Topics')
            ->schema([
                Repeater::make('topics')
                    ->label('Category Topics')
                    ->schema([
                        TextInput::make('label')
                            ->label('Topic Title')
                            ->required(),

                        TextInput::make('icon')
                            ->label('Icon URL')
                            ->url(),

                        // Nested content sections for each topic
                        Repeater::make('content.sections')
                            ->label('Topic Content')
                            ->schema([
                                Select::make('type')
                                    ->options([
                                        'rich-text' => 'Rich Text',
                                        'button' => 'Button',
                                    ])
                                    ->required()
                                    ->live(),

                                RichEditor::make('content')
                                    ->label('Text Content')
                                    ->columnSpanFull()
                                    ->visible(fn(callable $get) => $get('type') === 'rich-text'),

                                TextInput::make('label')
                                    ->label('Button Text')
                                    ->visible(fn(callable $get) => $get('type') === 'button'),

                                Select::make('action.type')
                                    ->label('Button Action')
                                    ->options([
                                        'phone' => 'Phone Number',
                                        'external' => 'External Link',
                                    ])
                                    ->live()
                                    ->visible(fn(callable $get) => $get('type') === 'button'),

                                TextInput::make('action.number')
                                    ->label('Phone Number')
                                    ->visible(fn(callable $get) => $get('type') === 'button' &&
                                        $get('action.type') === 'phone'),

                                TextInput::make('action.url')
                                    ->label('URL')
                                    ->url()
                                    ->visible(fn(callable $get) => $get('type') === 'button' &&
                                        $get('action.type') === 'external'),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->itemLabel(fn(array $state): ?string => $state['type'] ?? 'Section'),
                    ])
                    ->columns(1)
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'New Topic')
                    ->addActionLabel('Add Topic'),
            ]);
    }
}
