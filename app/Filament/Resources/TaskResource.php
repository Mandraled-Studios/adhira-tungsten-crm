<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use App\Models\User;
use Filament\Tables;
use App\Models\Client;
use Filament\Forms\Set;
use App\Enums\BillingAt;
use App\Models\TaskType;
use Filament\Forms\Form;
use App\Enums\TaskStatus;
use Filament\Tables\Table;
use App\Enums\AssessmentYear;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TaskResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use RelationManagers\TaskCheckpointsRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Tasks";

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'client_code')
                    ->getSearchResultsUsing(fn (string $search): array => Client::where('company_name', 'like', "%{$search}%")->orWhere('client_code', 'like', "%{$search}%")->limit(50)->pluck('company_name', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Client::find($value)?->client_code.' - '.Client::find($value)?->company_name)
                    ->searchable()
                    ->required()
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev()) 
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $client = Client::find($state);
                        $whoseClient = $client ? $client->auditor_group_id : 0;
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
                        $counter = Task::count() > 0 ? Task::latest()->first()->id+1 : 1;
                        if($counter<10) {
                            $padding='0000';
                        } else if($counter<100) {
                            $padding='000';
                        } else if($counter<1000) {
                            $padding='00';
                        } else if($counter<10000) {
                            $padding='0';
                        } else {
                            $padding='';
                        }
                        $suggestedTaskName = 'TA'.$auditName.'-'.date('dmY').'-'.$padding.$counter;

                        $set('code', $suggestedTaskName);

                        if(isset($client->billing_at)) {
                            switch($client->billing_at->name) {
                                case 'ADHIRA': $billing = 'Adhira Associates';
                                                          break;
                                case 'PERFECT': $billing = 'Perfect Tax Consultancy';
                                                                break;
                                case 'PERFECT_GLOBAL': $billing = 'Perfect Global Solutions';
                                                                 break;
                                default:
                                        $billing = 'Adhira Associates';
                                        break;
                            }
                        } else {
                            $billing = 'Adhira Associates';
                        }

                        $set('billing_company', $billing);
                    }),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),
        
                Forms\Components\Select::make('assessment_year')
                    ->required()
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->options([
                        '2000-01' => AssessmentYear::Y200001->value,
                        '2001-02' => AssessmentYear::Y200102->value,
                        '2002-03' => AssessmentYear::Y200203->value,
                        '2003-04' => AssessmentYear::Y200304->value,
                        '2004-05' => AssessmentYear::Y200405->value,
                        '2005-06' => AssessmentYear::Y200506->value,
                        '2006-07' => AssessmentYear::Y200607->value,
                        '2007-08' => AssessmentYear::Y200708->value,
                        '2008-09' => AssessmentYear::Y200809->value,
                        '2009-10' => AssessmentYear::Y200910->value,
                        '2010-11' => AssessmentYear::Y201011->value,
                        '2011-12' => AssessmentYear::Y201112->value,
                        '2012-13' => AssessmentYear::Y201213->value,
                        '2013-14' => AssessmentYear::Y201314->value,
                        '2014-15' => AssessmentYear::Y201415->value,
                        '2015-16' => AssessmentYear::Y201516->value,
                        '2016-17' => AssessmentYear::Y201617->value,
                        '2017-18' => AssessmentYear::Y201718->value,
                        '2018-19' => AssessmentYear::Y201819->value,
                        '2019-20' => AssessmentYear::Y201920->value,
                        '2020-21' => AssessmentYear::Y202021->value,
                        '2021-22' => AssessmentYear::Y202122->value, 
                        '2022-23' => AssessmentYear::Y202223->value, 
                        '2023-24' => AssessmentYear::Y202324->value, 
                        '2024-25' => AssessmentYear::Y202425->value,
                    ]),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'Assigned' => TaskStatus::Assigned->value,
                        'In Progress' => TaskStatus::InProgress->value, 
                        'On Hold' => TaskStatus::OnHold->value, 
                        'Waiting To Invoice' => TaskStatus::WaitingToInvoice->value, 
                        'Completed' => TaskStatus::Completed->value 
                    ])
                    ->default('Assigned')
                    ->afterStateUpdated(function (Set $set, $state) {
                        if($state == 'Completed') {
                            $set('completed_by', auth()->user()->id);
                        }
                    }),
                Forms\Components\DateTimePicker::make('duedate')
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->required(),
                Forms\Components\Select::make('assigned_user_id')
                    ->options(User::all()->pluck('name', 'id'))
                    ->label('Assign To User')
                    ->required()
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->dehydrated(),
                Forms\Components\Select::make('task_type_id')
                    ->searchable()
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->preload()
                    ->relationship('taskType', 'name')
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $taskType = TaskType::find($state);
                        if($taskType) {
                            $set('default_frequency', $taskType->frequency);
                        } else {
                            $set('default_frequency', '');
                        }
                    })
                    ->required(),
                Forms\Components\TextInput::make('default_frequency')
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->readOnly(),
                Forms\Components\TextInput::make('frequency_override')
                    ->label('Override Default Frequency')
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->datalist([
                        'One Time', 'Quarterly', 'Monthly', 'Yearly'
                    ])
                    ->maxLength(128),
                Forms\Components\Select::make('billing_company')
                    ->options([
                        "Adhira Associates" => BillingAt::ADHIRA->value,
                        "Perfect Tax Consultancy" => BillingAt::PERFECT->value,
                        "Perfect Global Solutions" => BillingAt::PERFECT_GLOBAL->value,
                    ])
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->required(),
                Forms\Components\Toggle::make('billing_status')
                    ->inline(false)
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->live(),
                Forms\Components\TextInput::make('billing_value')
                    ->numeric()
                    ->disabled(! auth()->user()->isAuditor() && ! auth()->user()->isDev())
                    ->hidden(function (Forms\Get $get): bool {
                        if(auth()->user()->isAuditor() || auth()->user()->isDev()) {
                            return ! $get('billing_status');
                        } else {
                            return true;
                        }
                     }),
                Forms\Components\Hidden::make('completed_by')
                     ->default(null),

                Forms\Components\Hidden::make('invoice_id')
                     ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.company_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessment_year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duedate')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_user.name')
                    ->exists('assigned_user')
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency_override')
                    ->searchable(),
                Tables\Columns\IconColumn::make('billing_status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('billing_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('taskType.name')
                    ->numeric()
                    ->sortable(),
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
            // ->groups([
            //     'auditor_group'
            // ])
            ->filters([
                Filter::make('completed_tasks')
                    ->label('Completed Tasks')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'completed')),
                Filter::make('incompleted_tasks')
                    ->label('Incomplete Tasks')
                    ->query(fn (Builder $query): Builder => $query->where('status', '!=', 'completed')),
                Filter::make('overdue_tasks')
                    ->label('Overdue Tasks')
                    ->query(fn (Builder $query): Builder => $query->where([['duedate', '<', now()], ['status', '!=', 'Completed']])),
                SelectFilter::make('under_auditor')
                    ->relationship('auditor_group', 'name')
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

    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->isDev() || auth()->user()->isAuditor()) {
            return parent::getEloquentQuery();
        } elseif(auth()->user()->isAdmin()) {
            return parent::getEloquentQuery()->where('status', 'Waiting To Invoice');
        } else {
            return parent::getEloquentQuery()->where('assigned_user_id', auth()->id());
        }
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TaskCheckpointsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
            'completed' => Pages\CompletedTasks::route('/completed'),
        ];
    }
}
