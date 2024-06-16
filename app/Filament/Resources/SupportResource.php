<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Support;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SupportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SupportResource\RelationManagers;

class SupportResource extends Resource
{
    protected static ?string $model = Support::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Tickets';

    protected static ?string $navigationGroup = "Support";

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])->schema([
                    Section::make('Issue Details')
                        ->schema([
                            Forms\Components\TextInput::make('issue_title')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('issue_description')
                                ->maxLength(65535)
                                ->columnSpanFull(),
                            Forms\Components\RichEditor::make('dev_notes')
                                ->maxLength(65535)
                                ->columnSpanFull()
                                ->disabled(!auth()->user()->isDev()),
                            Forms\Components\Select::make('user_id')
                                ->relationship('issue_owner', 'name')
                                ->default(auth()->user()->id)
                                ->disabled()
                                ->dehydrated(),
                        ])->columnSpan(2),
                    Section::make()
                        ->schema([
                            Section::make('Issue Status')
                                ->schema([
                                    Forms\Components\Select::make('status')
                                        ->options([
                                            'Created' => 'Created',
                                            'InProcess' => 'In Process',
                                            'OnHold' => 'Need More Info',
                                            'Resolved' => 'Resolved',
                                        ])
                                        ->default('Created')
                                        ->selectablePlaceholder(false)
                                        ->disabled(!auth()->user()->isDev()),
                                    Forms\Components\Toggle::make('is_resolved')
                                        ->label('Has issue been resolved?')
                                        ->inline(false),
                            ]),
                            Section::make('Attachments')
                                ->schema([
                                    Forms\Components\FileUpload::make('file_attachment_1')
                                        ->image()
                                        ->imageEditor(),
                                    Forms\Components\FileUpload::make('file_attachment_2'),
                            ]),
                        ])->columnSpan(1),
                ])
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->prefix('#')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue_owner.name')
                    ->label('Ticket Created By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_resolved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('unresolved_issues')
                    ->label('Unresolved Issues')
                    ->query(fn (Builder $query): Builder => $query->where('status', '!=', 'Resolved')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSupports::route('/'),
            'create' => Pages\CreateSupport::route('/create'),
            'edit' => Pages\EditSupport::route('/{record}/edit'),
        ];
    }
}
