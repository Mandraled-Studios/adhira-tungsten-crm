<x-filament-panels::page>
    <style>
        .ms-generate-invoice-btn {
            color: #197bde;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            text-align: center;
        }
    </style>
    <table class="table w-full table-fixed border-collapse border border-slate-400">
        <thead>
            <tr>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Task Code </th>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Assessment Year </th>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Bill Value </th>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Duedate </th>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Task Type </th>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Client </th>
                <th class="border-collapse border border-slate-400 px-4 py-4"> Action </th>
            </tr>
        </thead>
        <tbody>
            @if (count($completed) == 0)
                <tr>
                    <td colspan="7" class="text-center border-collapse border border-slate-400 px-4 py-4"> No completed tasks available </td>
                </tr>
            @else
            @foreach ($completed as $compTask)
                @php
                   $task_type = \App\Models\TaskType::find($compTask->task_type_id);
                   $task_type = $task_type ? $task_type : "N/A";

                   $client = \App\Models\Client::find($compTask->client_id);
                   $client = $client ? $client : "N/A";

                @endphp
                <tr>
                    <td class="text-center border-collapse border border-slate-400 px-4 py-4"> {{ $compTask->code }} </td>
                    <td class="text-center border-collapse border border-slate-400 px-4 py-4"> {{ $compTask->assessment_year }} </td>
                    <td class="text-center border-collapse border border-slate-400 px-4 py-4"> {{ $compTask->billing_value }} </td>
                    <td class="text-center border-collapse border border-slate-400 px-4 py-4"> {{ $compTask->duedate }} </td>
                    <td class="text-center border-collapse border border-slate-400 px-4 py-4"> {{ $task_type->name ?? "N/A" }} </td>
                    <td class="text-center border-collapse border border-slate-400 px-4 py-4"> {{ $client->company_name ?? "N/A" }} </td>
                    <td class="text-center border-collapse border border-slate-400 px-1 py-4"> 
                        <a class="ms-generate-invoice-btn" href="{{ route('filament.app.resources.invoices.create', ['task' => $compTask->id])}}"> Generate Invoice </a> 
                    </td>
                </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</x-filament-panels::page>
