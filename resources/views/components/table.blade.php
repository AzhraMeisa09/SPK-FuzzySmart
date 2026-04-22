@props(['headers' => []])

<div {{ $attributes->merge(['class' => 'table-container']) }}>
    <table class="data-table">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th scope="col">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="font-bold">
            {{ $slot }}
        </tbody>
    </table>
</div>
