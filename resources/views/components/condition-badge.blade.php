<span
    class="text-sm rounded
    @if ($condition === 'New')
        text-white font-bold bg-green-600
    @elseif ($condition === 'Like New')
        text-white font-bold bg-blue-600
    @elseif ($condition === 'Very Good')
        text-white font-bold bg-black
    @elseif ($condition === 'Good')
        text-white font-bold bg-orange-600
    @elseif ($condition === 'Acceptable')
        text-white font-bold bg-gray-600
    @endif
    px-2 py-1"
>
    {{ $condition }}
</span>
