<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Receipt;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\PaymentMethods;
use App\Helpers\InvoiceHelper;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ReceiptResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\ReceiptResource\RelationManagers;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = "Payments";

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        //session_start();
        $ass_year = substr(date('Y'), 2, 2).'-'.substr(date('Y')+1,2);
        $prefix = "Receipt/".$ass_year."/";
        $lastReceipt = Receipt::latest()->first();
        $lastReceiptID = $lastReceipt ? $lastReceipt->id : 0;
        $nextReceiptId = (int)$lastReceiptID+1;
        $def_receipt_number = $prefix.str_pad($nextReceiptId, 5, "0", STR_PAD_LEFT);
        return $form
            ->schema([
                Forms\Components\TextInput::make('receipt_number')
                    ->default($def_receipt_number)
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->dehydrated()
                    ->required(),
                Forms\Components\Select::make('invoice_id')
                    ->searchable()
                    ->relationship('invoice', 'invoice_number')
                    ->label("Against Invoice")
                    ->live()
                    ->afterStateUpdated(function($state, callable $set){ 
                        $invHelp = new InvoiceHelper;
                        $total = $invHelp->getInvoiceValue($state);
                        $client_name = $invHelp->getClientDetails($state, "name");
                        $client_code = $invHelp->getClientDetails($state, "code");
                        //$_SESSION['invoice_amount'] = $total;
                        $set('amount_paid_global', $total);
                        $set('client_name', $client_name);
                        $set('client_code', $client_code);
                        return $set('amount_paid', $total);
                    })
                    ->required(),
                Forms\Components\TextInput::make('client_name')
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->dehydrated()
                    ->default($client_name??"")
                    ->readOnly()
                    ->hiddenOn('edit'),
                Forms\Components\TextInput::make('client_code')
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->dehydrated()
                    ->default($client_code??"")
                    ->readOnly()
                    ->hiddenOn('edit'),
                Forms\Components\Hidden::make('amount_paid_global')
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->dehydrated()
                    ->default(0),
                Forms\Components\DatePicker::make('payment_date')
                    ->label('Receipt Date')
                    ->required(),
                Forms\Components\TextInput::make('amount_paid')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function($state, callable $set, $get){ 
                        $bal = (float) $get('amount_paid_global') - (float) $state;
                        if($bal > 0) {
                            $set('paid_in_full', false);
                        } else if($bal == 0) {
                            $set('paid_in_full', true);
                        }
                        return $set('balance', $bal);
                    })
                    ->numeric(),
                Forms\Components\TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('paid_in_full')
                    ->default(true)
                    ->disabled(),
                Forms\Components\Select::make('payment_method')
                    ->enum(PaymentMethods::class)
                    ->options(PaymentMethods::class)
                    ->required(),
                // Forms\Components\Toggle::make('refunded')
                //     ->required(),
            ]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('paid_in_full')
                    ->boolean()
                    ->default(false),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                // Tables\Columns\IconColumn::make('refunded')
                //     ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('with_balance')
                ->label('Partial Payments')
                ->query(fn (Builder $query): Builder => $query->where('balance', '>', 0)),
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
            'index' => Pages\ListReceipts::route('/'),
            'create' => Pages\CreateReceipt::route('/create'),
            'edit' => Pages\EditReceipt::route('/{record}/edit'),
        ];
    }
}
