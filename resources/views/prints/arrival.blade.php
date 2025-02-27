<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Приходная накладная</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid black;
            padding: 5px;
        }

        .font-italic {
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="title">Приходная накладная №{{ $document->id }} от {{ $document->created_at->format('Y-m-d') }}</div>
<p class="font-italic">Поставщик: {{ $document->counterparty->name }}, {{ $document->counterparty->address }}, ИНН {{ $document->counterparty->inn }}</p>
<p class="font-italic font-weight-black">Покупатель: {{ $organization ? $organization->name : 'Не указан' }}</p>
<p class="font-italic">Склад: Основной склад</p>
<table>
   <thead>
   <tr>
       <th>Код</th>
       <th>Наименование</th>
       <th>Ед. изм.</th>
       <th>Количество</th>
   </tr>
   </thead>
    @if($document->order)
        @foreach($document->order->ordersTechCards  as $orderTechCards )
            <tr>
                <td>{{ $orderTechCards->product->id }}</td> <!-- Код товара -->
                <td>{{ $orderTechCards->product->name }}</td> <!-- Код товара -->
                <td>{{ $orderTechCards->product->unitOfMeasurements->name  }}</td> <!-- Код товара -->
                <td>{{ $orderTechCards->quantity }}</td> <!-- Код товара -->
            </tr>
        @endforeach
    @else
        @foreach($document->documentProducts as $documentProduct )
            <td>{{ $documentProduct->product->id }}</td> <!-- Код товара -->
            <td>{{ $documentProduct->product->name }}</td> <!-- Код товара -->
            <td>{{ $documentProduct->product->unitOfMeasurements->name }}</td> <!-- Код товара -->
            <td>{{ $documentProduct->quantity }}</td> <!-- Код товара -->
        @endforeach
    @endif
</table>

<div class="сaption">
    <p>Отпустил</p>
    <hr class="indent">
    <p>Получил</p>
    <hr>
</div>
</body>
</html>
