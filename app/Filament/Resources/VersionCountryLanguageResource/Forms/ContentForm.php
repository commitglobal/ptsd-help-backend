<?php

declare(strict_types=1);

namespace App\Filament\Resources\VersionCountryLanguageResource\Forms;

use App\Enum\ToolIds;
use App\Enum\ToolSubcategoriesIds;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;

class ContentForm
{
    public static function getSchema(): array
    {
        return [
            TextInput::make('title')
                ->label('Title')
                ->required(),

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
                        ->schema(function (Forms\Get $get) {
                            if ($get('type') === 'category') {
                                return ContentForm::getCategoryTopicsSchema();
                            }

                            if ($get('type') === 'topic') {
                                return ContentForm::getTopicContentSchema();
                            }

                            return [];
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'New Page')
                ->addActionLabel('Add Page'),
        ];
    }

    private static function getCategoryTopicsSchema(): array
    {
        return [
            Repeater::make('topics')
                ->label('Topics')
                ->schema([
                    TextInput::make('label')
                        ->label('Topic Title')
                        ->required(),

                    TextInput::make('icon')
                        ->label('Icon URL')
                        ->url(),

                    Repeater::make('content.sections')
                        ->label('Sections')
                        ->schema([
                            self::getSectionSchema(),
                        ])
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['type'] ?? 'New Section')
                        ->addActionLabel('Add Section'),
                ])
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'New Topic')
                ->addActionLabel('Add Topic'),
        ];
    }

    private static function getTopicContentSchema(): array
    {
        return [
            Repeater::make('content.sections')
                ->label('Sections')
                ->schema([
                    self::getSectionSchema(),
                ])
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['type'] ?? 'New Section')
                ->addActionLabel('Add Section'),
        ];
    }

    private static function getSectionSchema(): Component
    {
        return Group::make()
            ->schema([
                Select::make('type')
                    ->options([
                        'image' => 'Image',
                        'text' => 'Text',
                        'rich-text' => 'Rich Text',
                        'button' => 'Button',
                        'multiContent' => 'Multi Content',
                        'multiPage' => 'Multi Page',
                        'contact' => 'Contact',
                    ])
                    ->required()
                    ->live(),

                // Conditional fields based on section type
                Group::make()
                    ->schema(function (Forms\Get $get) {
                        $schemas = [];

                        // Image Content
                        if ($get('type') === 'image') {
                            $schemas[] = TextInput::make('src')
                                ->label('Image URL')
                                ->required()
                                ->url();

                            $schemas[] = TextInput::make('alt')
                                ->label('Alt Text')
                                ->required();
                        }

                        // Text Content
                        if ($get('type') === 'text') {
                            $schemas[] = Textarea::make('content')
                                ->label('Text Content')
                                ->required();
                        }

                        // Rich Text Content
                        if ($get('type') === 'rich-text') {
                            $schemas[] = RichEditor::make('content')
                                ->label('Rich Text Content')
                                ->required()
                                ->disableToolbarButtons([
                                    'attachFiles',
                                    'codeBlock',
                                ]);
                        }

                        // Button Content
                        if ($get('type') === 'button') {
                            $schemas[] = TextInput::make('label')
                                ->label('Button Text')
                                ->required();

                            $schemas[] = ContentForm::getActionSchema();
                        }

                        // Multi Content
                        if ($get('type') === 'multiContent') {
                            $schemas[] = Repeater::make('contentArray')
                                ->label('Content Items')
                                ->schema([
                                    ContentForm::getSectionSchema(),
                                ])
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['type'] ?? 'New Item')
                                ->addActionLabel('Add Item');
                        }

                        // Multi Page
                        if ($get('type') === 'multiPage') {
                            $schemas[] = Repeater::make('pageArray')
                                ->label('Pages')
                                ->schema([
                                    Repeater::make('')
                                        ->label('Page Sections')
                                        ->schema([
                                            ContentForm::getSectionSchema(),
                                        ])
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['type'] ?? 'New Section')
                                        ->addActionLabel('Add Section'),
                                ])
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => 'Page ' . ($state['_id'] ?? ''));
                        }

                        // Contact Content (no additional fields needed)

                        return $schemas;
                    }),
            ]);
    }

    private static function getActionSchema(): Component
    {
        return Group::make()
            ->schema([
                Select::make('action.type')
                    ->label('Action Type')
                    ->options([
                        'start_tool' => 'Start Tool',
                        'external' => 'External URL',
                        'phone' => 'Phone',
                        'webview' => 'Webview',
                        'share' => 'Share',
                    ])
                    ->required()
                    ->live(),

                // Conditional fields based on action type
                Group::make()
                    ->schema(function (Forms\Get $get) {
                        $schemas = [];

                        if ($get('action.type') === 'start_tool') {
                            $schemas[] = Select::make('action.toolId')
                                ->label('Tool')
                                ->options([
                                    'Observe Thoughts Tools' => [
                                        ToolIds::OBSERVE_THOUGHTS => 'Observe Thoughts',
                                        ToolSubcategoriesIds::CLOUDS => 'Clouds',
                                        ToolSubcategoriesIds::RIVER => 'River',
                                    ],
                                    'Positive Imagery Tools' => [
                                        ToolIds::POSTIVE_IMAGERY => 'Positive Imagery',
                                        ToolSubcategoriesIds::BEACH => 'Beach',
                                        ToolSubcategoriesIds::COUNTRY_ROAD => 'Country river',
                                        ToolSubcategoriesIds::FOREST => 'Forrest',
                                    ],
                                    'Body scan tools' => [
                                        ToolIds::BODY_SCAN => 'Body scan',
                                        ToolSubcategoriesIds::JULIA => 'Julia',
                                        ToolSubcategoriesIds::ROBYN => 'Robyn',
                                    ],
                                    ToolIds::MUSCLE_RELAXATION => 'Muscle relaxation',
                                    ToolIds::DEEP_BREATHING => 'Deep breathing',
                                    'Relationships tools' => [

                                        ToolIds::RELATIONSHIPS => 'Relationships',
                                        ToolSubcategoriesIds::RECONNECT_WITH_PARTNER => 'Reconect with the partner',
                                        ToolSubcategoriesIds::POSITIVE_COMMUNICATION => 'Positive communication',
                                        ToolSubcategoriesIds::I_MESSAGES => 'I messages',
                                        ToolSubcategoriesIds::HEALTHY_ARGUMENTS => 'Healthy arguments',
                                    ],
                                    ToolIds::AMBIENT_SOUNDS => 'Ambient sounds',
                                    'Mindfulness tools' => [
                                        ToolIds::MINDFULNESS => 'Mindfulness',
                                        ToolSubcategoriesIds::CONSCIOUS_BREATHING => 'Conscious Breathing',
                                        ToolSubcategoriesIds::MINDFUL_WALKING => 'Mindful Walking',
                                        ToolSubcategoriesIds::EMOTIONAL_DISCOMFORT => 'Emotional Discomfort',
                                        ToolSubcategoriesIds::SENSE_AWARENESS => 'Sense Awarness',
                                        ToolSubcategoriesIds::LOVING_KINDNESS => 'Loving Kindess',
                                    ],
                                    ToolIds::PAUSE => 'Pause',
                                    ToolIds::MY_FEELINGS => 'My feelings',
                                    'Sleep tools' => [
                                        ToolIds::SLEEP => 'Sleep',
                                        ToolSubcategoriesIds::SLEEP_HELP => 'Sleep Help',
                                        ToolSubcategoriesIds::SLEEP_HABITS => 'Sleep Habits',
                                        ToolSubcategoriesIds::SLEEP_PERSPECTIVE => 'Sleep Perspective',
                                    ],
                                    ToolIds::WORRY_TIME => 'Worry Time',
                                    ToolIds::RID => 'RID',
                                    ToolIds::SOOTHE_SENSES => 'Soothe Senses',
                                    ToolIds::CONNECT_WITH_OTHERS => 'Connect with others',
                                    ToolIds::CHANGE_PERSPECTIVE => 'Change perspective',
                                    ToolIds::GROUNDING => 'Grounding',
                                    ToolIds::QUOTES => 'Quotes',
                                    ToolIds::MY_STRENGTHS => 'My strengths',
                                    ToolIds::SHIFT_THOUGHTS => 'Shift thoughts',
                                    'Recreational Activities Tools' => [
                                        ToolIds::RECREATIONAL_ACTIVITIES => 'Recreational Activities',
                                        ToolSubcategoriesIds::RECREATIONAL_ACTIVITIES_ALONE => 'Recreational Activities Alone',
                                        ToolSubcategoriesIds::RECREATIONAL_ACTIVITIES_CITY => 'Recreational Activities City',
                                        ToolSubcategoriesIds::RECREATIONAL_ACTIVITIES_NATURE => 'Recreational Activities Nature',
                                    ],
                                ])
                                ->required()
                                ->searchable();
                        }

                        if (\in_array($get('action.type'), ['external', 'webview'])) {
                            $schemas[] = TextInput::make('action.url')
                                ->label('URL')
                                ->required()
                                ->url();
                        }

                        if ($get('action.type') === 'phone') {
                            $schemas[] = TextInput::make('action.number')
                                ->label('Phone Number')
                                ->required();
                        }

                        if ($get('action.type') === 'share') {
                            $schemas[] = Textarea::make('action.message')
                                ->label('Message')
                                ->required();
                        }

                        return $schemas;
                    }),
            ]);
    }

    private static function mutateFormDataBeforeSave(array $data): array
    {
        // Generate UUIDs for pages and topics
        foreach ($data['pages'] as &$page) {
            $page['id'] = (string) Str::uuid();

            if ($page['type'] === 'category' && isset($page['topics'])) {
                foreach ($page['topics'] as &$topic) {
                    $topic['id'] = (string) Str::uuid();
                    $topic['type'] = 'topic';
                }
            }
        }

        return $data;
    }
}
