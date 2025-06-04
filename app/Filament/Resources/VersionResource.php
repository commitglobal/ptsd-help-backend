<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enum\VersionStatus;
use App\Filament\Resources\VersionResource\Pages;
use App\Models\Version;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VersionResource extends Resource
{
    protected static ?string $model = Version::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->columns(1)
                    ->schema(components: [
                        DateTimePicker::make('published_at')
                            ->label(__('version.field.published_at'))
                            ->visible(fn(?Version $record) => $record?->isPublished())
                            ->disabled()
                            ->native(false)
                            ->timezone('UTC')

                            ->displayFormat('Y-m-d\TH:i:s\Z')
                            ->extraAttributes(['class' => 'max-w-sm']),

                        Hidden::make('status')
                            ->default(VersionStatus::drafted),

                        TextInput::make('name')
                            ->label(__('version.field.name'))
                            ->maxLength(200)
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(VersionStatus $state): string => match ($state) {
                        VersionStatus::drafted => 'secondary',
                        VersionStatus::archived => 'warning',
                        VersionStatus::published => 'success',
                    }),

                TextColumn::make('published_at')
                    ->label(__('version.field.published_at'))
                    ->formatStateUsing(fn($state) => $state?->toDateTimeString() ?? '-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('general.created_at'))
                    ->formatStateUsing(fn($state) => $state->toDateTimeString())
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label(__('general.updated_at'))
                    ->formatStateUsing(fn($state) => $state->toDateTimeString())
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(), // TODO: display this filter only when superadmins are accessing this page
                SelectFilter::make('status')
                    ->label(__('version.field.status'))
                    ->options(VersionStatus::options()),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersions::route('/'),
            'create' => Pages\CreateVersion::route('/create'),
            'view' => Pages\ViewVersion::route('/{record}'),
            'edit' => Pages\EditVersion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
