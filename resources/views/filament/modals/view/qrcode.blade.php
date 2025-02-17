<div class="flex flex-col items-center justify-center p-4">
    <h2 class="text-xl font-bold mb-4">
        QR Code pour "{{ $record->title }}"
    </h2>

    <div class="bg-white p-4 rounded-lg shadow-md">
    {{ $qrCode }}
    </div>

    <div class="mt-4 text-sm text-gray-600 text-center">
        <p>Scannez ce QR code pour accéder rapidement aux détails du livre</p>
        <p class="mt-2">ID: {{ $record->id }}</p>
        <p class="mt-2">ISBN: {{ $record->isbn }}</p>
    </div>
</div> 