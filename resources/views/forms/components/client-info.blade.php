<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>   
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <textarea row="4" class="w-full border-none text-gray-950 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 bg-white/0" type="text" x-model="state" disabled readonly></textarea>
    </div>
</x-dynamic-component>

