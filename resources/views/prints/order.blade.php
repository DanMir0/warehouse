<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заказ №{{ $order->id }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }
        .container { padding: 20px; }
        .title { font-size: 26px; font-weight: bold; text-align: center }
        .info { margin-bottom: 15px; font-style: italic; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
<div class="container">
    <p class="title">Заказ на производство №{{ $order->id }} от {{ $order->created_at->format('Y-m-d') }}</p>
    <p class="info">
        Заказчик: {{ $order->counterparty->name }}
        Телефон: {{ $order->counterparty->contact_info }}
    </p>
    <p class="info">
        Производитель: {{ $organization->name }}
    </p>
    <table>
        <thead>
        <tr>
            <th>№</th>
            <th>Код</th>
            <th>Товар</th>
            <th>Ед. изм.</th>
            <th>Количество</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($order->ordersTechCards as $index => $techCard)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $techCard->product->id }}</td>
                <td>{{ $techCard->product->name }}</td>
                <td>{{ $techCard->product->unitOfMeasurements->name }}</td>
                <td>{{ $techCard->quantity }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
