<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Models\Task;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use App\Filament\Resources\TaskResource;
use Filament\Tables\Concerns\InteractsWithTable;

class CompletedTasks extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $resource = TaskResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = "Tasks";

    protected static ?string $navigationLabel = "Completed Tasks To Invoice";

    protected static string $view = 'filament.resources.task-resource.pages.completed-tasks';

    public function mount(): void
    {
        static::authorizeResourceAccess();
    }

    // protected function getViewData(): array
    // {
    //     $completed = Task::where('status', 'completed')
    //                         ->whereNull('invoice_id')
    //                         ->get();
    //     return ['completed' => $completed];
    // }

    public function table(Table $table): Table
    {
        return $table
            ->query(Task::query()->where('status', '=', 'completed')->whereNull('invoice_id'))
            ->columns([
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('client.company_name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('assessment_year')
                    ->sortable(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('duedate')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('assigned_user.name')
                    ->exists('assigned_user')
                    ->searchable(),
                TextColumn::make('frequency_override')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('billing_status')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('billing_value')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('billing_company')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('taskType.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                Action::make('edit')
                ->label('Edit Task')
                ->icon('heroicon-o-pencil')
                ->url(fn (Task $record): string => route('filament.app.resources.tasks.edit', $record)) ,
                Action::make('invoice')
                ->label('Generate Invoice')
                ->icon('heroicon-o-currency-rupee')
                ->url(fn (Task $record): string => route('filament.app.resources.invoices.create', ['task' => $record->id])) ,
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
