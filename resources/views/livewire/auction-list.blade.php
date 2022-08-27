<div>
    <div class="m-4 flex items-center">
        <input type="text" class="border border-blue" wire:model.debounce.500ms="searchQuery" placeholder="Catan...">
        <svg wire:loading.delay class="ml-4 animate-spin h-8 w-8 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
    <table class="table-auto border-collapse w-full">
        <thead class="bg-gray-200 uppercase font-bold border-b border-slate-400">
            <td class="p-4">Index</td>
            <td class="p-4">Author</td>
            <td class="p-4">Title</td>
            <td class="p-4">Condition</td>
            <td class="p-4"></td>
            <td class="p-4">Language</td>
            <td class="p-4">Version</td>
            <td class="p-4">Starting bid</td>
            <td class="p-4">Soft reserve</td>
            <td class="p-4">BIN</td>
            <td class="p-4">Already sold</td>
        </thead>
        <tbody>
            @foreach($this->filteredList as $listing)
                <tr class="border-b border-slate-200 {{ $listing['deleted'] ? 'opacity-50' : '' }}">
                    <td class="p-4"><a href="https://boardgamegeek.com/geeklist/item/{{ $listing['id'] }}#item{{ $listing['id'] }}" class="underline hover:no-underline text-blue-500">{{ $listing['index'] }}</a></td>
                    <td class="p-4">{{ $listing['author'] }}</td>
                    <td class="p-4"><a href="{{ $listing['bggLink'] }}" class="underline hover:no-underline text-blue-500">{{ $listing['title'] }}</a></td>
                    <td class="p-4">
                        <x-condition-badge :condition="$listing['condition']" />
                    </td>
                    <td class="p-4">
                        {{ $listing['conditionComment'] }}
                    </td>
                    <td class="p-4">{{ $listing['language'] }}</td>
                    <td class="p-4">{{ $listing['version'] }}</td>
                    <td class="p-4">{{ $listing['startingBid'] }}</td>
                    <td class="p-4">{{ $listing['softReserve'] }}</td>
                    <td class="p-4">{{ $listing['bin'] }}</td>
                    <td class="p-4">@if($listing['deleted']) ✅ @endif</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
