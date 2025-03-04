<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Codes des Livres</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }

        .container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .qr-card {
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            break-inside: avoid;
        }

        .qr-code {
            margin-bottom: 10px;
        }

        .book-title,
        .book-owner {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .book-author {
            font-size: 10px;
            color: #666;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">
        Imprimer les QR Codes 🖨️
    </button>
    <div class="container">
        @foreach($qrCodes as $qrCode)
            <div class="qr-card">
                <div class="qr-code">
                    <img src="{{ asset('storage/qr-codes/' . $qrCode['qrCode']) }}" alt="QR Code" width="{{ $printSize }}" height="{{ $printSize }}">
                </div>
                <div class="book-title">{{ $qrCode['title'] }}</div>
                <div class="book-isbn">{{ $qrCode['isbn'] }}</div>
                <div class="book-owner">{{ $qrCode['owner'] }}</div>
            </div>
        @endforeach
    </div>
</body>
</html> 