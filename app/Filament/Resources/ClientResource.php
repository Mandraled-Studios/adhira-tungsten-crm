<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Client;
use App\Enums\FirmType;
use App\Enums\BillingAt;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClientResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Filament\Resources\ClientResource\Widgets\StatsOverview;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('auditor_group_id')
                    ->label("Under Auditor Group")
                    ->relationship('auditorGroup', 'name', function (Builder $query) {
                        return $query->where('role', 'auditor');
                    }),
                Forms\Components\TextInput::make('company_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('firm_type')
                    ->enum(FirmType::class)
                    ->options(FirmType::class)
                    ->required(),
                Forms\Components\TextInput::make('pan_number')
                    ->maxLength(15),
                Forms\Components\TextInput::make('client_code')
                    ->maxLength(32),
                Forms\Components\TextInput::make('client_name')
                    ->maxLength(128),
                Forms\Components\TextInput::make('aadhar_number')
                    ->maxLength(12),
                Forms\Components\TextInput::make('mobile')
                    ->maxLength(15)
                    ->required(),
                Forms\Components\TextInput::make('whatsapp')
                    ->maxLength(15),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(234),
                Forms\Components\TextInput::make('alternate_email')
                    ->email()
                    ->maxLength(254),
                Forms\Components\TextInput::make('website')
                    ->maxLength(128),
                Forms\Components\TextInput::make('address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('city')
                    ->maxLength(64),
                Forms\Components\TextInput::make('state')
                    ->maxLength(64),
                Forms\Components\TextInput::make('country')
                    ->maxLength(64),
                Forms\Components\TextInput::make('pincode')
                    ->maxLength(10),
                Forms\Components\TextInput::make('tan_no')
                    ->label('TAN No')
                    ->maxLength(20),
                Forms\Components\TextInput::make('cin_no')
                    ->label('CIN No')
                    ->maxLength(20),
                Forms\Components\TextInput::make('gstin')
                    ->label('GSTIN')
                    ->maxLength(16),
                Forms\Components\Select::make('billing_at')
                    ->options([
                        "Adhira Associates" => BillingAt::ADHIRA->value,
                        "Perfect Tax Consultancy" => BillingAt::PERFECT->value,
                        "Perfect Global Solutions" => BillingAt::PERFECT_GLOBAL->value,
                    ]),
                Forms\Components\Toggle::make('client_status'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('firm_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('auditorGroup.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pan_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('aadhar_number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alternate_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pincode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tan_no')
                    ->label('TAN No')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('cin_no')
                    ->label('CIN No')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gstin')
                    ->label('GSTIN')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('billing_at')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('client_status')
                    ->boolean()
                    ->sortable(),
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
                SelectFilter::make('client_status')
                    ->options([
                        1 => 'Active Clients',
                        0 => 'Inactive Clients'
                    ])
                    ->attribute('client_status'),
                SelectFilter::make('auditor')
                    ->options([
                        3 => "SK",
                        4 => "HK",
                        5 => "MK",
                    ])
                    ->attribute('auditor_group_id'),
                SelectFilter::make('firm_type')
                    ->options(FirmType::class)
                    ->attribute('firm_type'),
                SelectFilter::make('billing_at')
                    ->options(BillingAt::class)
                    ->attribute('billing_at')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
