<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use Filament\Tables;
use App\Enums\TaxLabel;
use App\Models\Invoice;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Enums\TaskLabel;
use Filament\Forms\Form;
use App\Mail\SendInvoice;
use Filament\Tables\Table;
use App\Enums\InvoiceStatus;
use App\Resources\GenericEmail;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use App\Forms\Components\ClientInfo;
use Illuminate\Support\Facades\Mail;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource_deleted extends Resource
{

    protected static ?string $model = Invoice::class;

    protected static ?int $column = 3;

    //protected static ?string $navigationLabel = 'Create Invoice For '.$client;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = "Payments";

    protected static ?int $navigationSort = 6;

    public static function canCreate(): bool
    {
       return true;
    }

    public static function form(Form $form): Form
    {
        $whoseClient = null; 
        $task = null; 
        $clientInfo = '';
        $clientState = null;
        $tax1_label = '';
        $tax2_label = '';
        $tax1 = 0;
        $tax2 = 0;
        $total = 0;
        $billing_at = null;

        if(isset($_GET['task'])) {
            $taskid = $_GET['task'];
            $task = Task::find($taskid);
            $client = $task->client;
            $billing_at = $client->billing_at ? $client->billing_at : "Adhira Associates";
            $whoseClient = $client ? $client->auditor_group_id : 0;
            $clientInfo = $client->company_name.' ('.$client->client_code.'), '.$client->address.', '.$client->state;
            $clientState = $client->state;
            if($task->billing_status && $task->billing_value > 0 && $client->billing_at == 'Adhira Associates') {
                if($clientState == 'Tamilnadu' || $clientState == 'Tamil nadu' || $clientState == 'Tamil Nadu') {
                    $tax1_label = 'CGST';
                    $tax2_label = 'SGST';
                    $tax1 = $task->billing_value ? (float)$task->billing_value * 0.09 : 0;
                    $tax2 = $task->billing_value ? (float)$task->billing_value * 0.09 : 0;
                } else {
                    $tax1_label = 'IGST';
                    $tax2_label = null;
                    $tax1 = $task->billing_value ? (float)$task->billing_value * 0.18 : 0;
                    $tax2 = 0;
                }
            }
            $total = (float)$task->billing_value + $tax1 + $tax2;
        }

        if($whoseClient) {
            switch($whoseClient) {
                case 3: $auditName = 'SK';
                        break;
                case 4: $auditName = 'HK';
                        break;
                case 5: $auditName = 'MK';
                        break;
                default:$auditName = 'AA';
                        break;
            }
        } else {
            $auditName = 'AA';
        }

        $month_start = date('Y-m-d', strtotime('first day of this month', time()));
        $month_end = date('Y-m-d', strtotime('last day of this month', time()));
        //$lastInvoice = Invoice::whereBetween('created_at', [$month_start, $month_end])->latest()->first();
        $lastInvoice = Invoice::latest()->first();
        $lastInvoiceID = $lastInvoice ? $lastInvoice->id : 0;

        if($task) {
            $ass_year = substr($task->assessment_year->name,3,2).'-'.substr($task->assessment_year->name,5);
        } else {
            $ass_year = substr(date('Y'), 2, 2).'-'.substr(date('Y')+1,2);
        }
        $prefix = $auditName.'/'.$ass_year.'/';
        $nextInvoiceId = (int)$lastInvoiceID+1;
        $invoiceNumber = $prefix.str_pad($nextInvoiceId, 5, "0", STR_PAD_LEFT);
        
        return $form
            ->schema([
                Section::make('Invoice For / Client Details')
                ->schema([
                    ClientInfo::make('client_details')
                        ->default(
                        // function(string $operation) {
                        //     if($operation === 'create') {
                        //         return $myForm->clientInfo;
                        //     }
                        // }
                        $clientInfo
                        )
                        ->columnSpan(3),
                    ClientInfo::make('billing_at')
                        ->default($billing_at ?? 'Adhira Associates')
                        ->columnSpan(1),
                ])->columns(4),
                Section::make('Invoice Details')
                ->schema([
                    Forms\Components\TextInput::make('invoice_number')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default($invoiceNumber)
                        ->maxLength(20),
                    Forms\Components\DatePicker::make('invoice_date')
                        ->default(today())
                        ->required(),
                    Forms\Components\DatePicker::make('duedate')
                        ->default(now()->addDays(15))
                        ->required(),
                    Forms\Components\Select::make('invoice_status')
                        ->enum(InvoiceStatus::class)
                        ->options(InvoiceStatus::class)
                        ->default('Generated')
                        //->readOnly()
                        //->disabledOn('create'),
                ])->columns(4),
                Section::make('Invoice For')
                ->schema([
                    Forms\Components\Select::make('task_id')
                        ->relationship('task', 'code')
                        ->live()
                        ->required()
                        ->searchable()
                        ->default($task->id ?? ''),
                        //->disabledOn('create'),
                    Forms\Components\Textarea::make('task_description')
                        ->label('description'),
                    Forms\Components\TextInput::make('subtotal')
                        ->required()
                        ->default($task->billing_value ?? 0)
                        ->numeric()
                        ->prefix('₹'),
                ])->columns(3),
                Section::make('Taxes')
                ->schema([
                    /*
                    Forms\Components\Select::make('enable_tax')
                        ->options([
                            'taxable' => 'Taxable',
                            'nontaxable' => 'Non Taxable',
                        ])
                        ->live()
                        ->default($billing_at == "Adhira Associates" ? 'taxable' : 'nontaxable')
                        ->afterStateUpdated(fn (Select $component) => $component
                            ->getContainer()
                            ->getComponent('taxable')
                            ->getChildComponentContainer()
                            ->fill()
                        ),
                    */
                    
                    Grid::make(2)
                        ->schema(fn (Get $get): array => match ($get('enable_tax')){
                            'taxable' => [
                                Forms\Components\TextInput::make('hsncode')
                                    ->label('HSN / SAC Code')
                                    ->numeric()
                                    ->default(912346)
                                    ->minValue(000000)
                                    ->maxValue(999999)
                                    ->columnSpan(2),
                                Forms\Components\Select::make('tax1_label')
                                    ->enum(TaxLabel::class)
                                    ->options(TaxLabel::class)
                                    ->default($tax1_label),
                                Forms\Components\TextInput::make('tax1')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default($tax1 ?? 0),
                                Forms\Components\Select::make('tax2_label')
                                    ->enum(TaxLabel::class)
                                    ->options(TaxLabel::class)
                                    ->default($tax2_label),
                                Forms\Components\TextInput::make('tax2')
                                    ->numeric()
                                    ->prefix('₹')
                                    ->default($tax2 ?? 0),
                            ],
                            'nontaxable' => [],
                            default => [],
                    })->key('taxable')->columns(2),
                ])->aside(),
                Section::make('Total Pricing')
                ->schema([ 
                    Forms\Components\TextInput::make('roundoff')
                        ->numeric()
                        ->prefix('₹')
                        ->minValue(-0.99)
                        ->maxValue(0.99)
                        ->step(0.01)
                        ->default(0.00)
                        ->live()
                        ->afterStateUpdated(function(Get $get, Set $set, ?string $state) {
                            $tot = (float) $get('subtotal') + (float) $get('tax1') + (float) $get('tax2') + $state;
                            $set('total', $tot);
                        }),
                    Forms\Components\TextInput::make('total')
                        ->required()
                        ->numeric()
                        ->prefix('₹')
                        ->default($total),
                ])->aside(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duedate')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('task.code')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tax1')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tax2')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax1_label')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tax2_label')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('invoice_status')
                    ->searchable(),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('sendEmail')
                    ->action(function (Invoice $invoice) {
                        Mail::to("mandraledstudios@gmail.com")->send(new SendInvoice($invoice));
                    })
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
