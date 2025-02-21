<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f3f4f6;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            text-align: center;
        }
        .title {
            color: #374151;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .qr-code {
            margin: 1rem 0;
        }
        .description {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">QR Code 📱</h1>
        <div class="qr-code">
            {!! $qrCode !!}
        </div>
        <p class="description">
            Scannez ce QR code pour accéder au livre
        </p>
    </div>
</body>
</html>
