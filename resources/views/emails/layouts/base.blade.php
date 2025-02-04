<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            font-size: 0.9em;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 20px;
            margin-top: 20px;
        }
        ul {
            padding-left: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        .text-break {
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>@yield('title')</h1>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        @section('footer')
            <p>Cet email a été envoyé automatiquement par {{ config('app.name') }}.</p>
            @yield('footer-extra')
        @show
    </div>
</body>
</html> 