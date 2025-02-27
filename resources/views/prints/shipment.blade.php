<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Расходная накладная</title>
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
<div class="title">Расходная накладная №{{ $document->id }} от {{ $document->created_at->format('Y-m-d') }}</div>
<p class="font-italic">Поставщик: {{ $organization->name }}</p>
<p class="font-italic font-weight-black">Покупатель: {{ $document->counterparty->name }}
    , {{ $document->counterparty->address }}, ИНН {{ $document->counterparty->inn }}</p>
<p class="font-italic">Склад: Основной склад</p>

<table>
    <tr>
        <th>Код</th>
        <th>Наименование</th>
        <th>Ед. изм.</th>
        <th>Количество</th>
    </tr>
    @php
        use App\Helpers\Common\OrderStatuses;
    @endphp
    @if($document->order)
        @if($document->order->status->id == OrderStatuses::STATUS_ISSUED)
            @foreach($document->order->ordersTechCards as $orderTechCard)
               <tr>
                   <td>{{ $orderTechCard->product->id }}</td>
                   <td>{{ $orderTechCard->product->name }}</td>
                   <td>{{ $orderTechCard->product->unitOfMeasurements->name }}</td>
                   <td>{{ $orderTechCard->quantity }}</td>
               </tr>
            @endforeach

        @else
            @foreach($document->order->ordersTechCards  as $orderTechCards )
                @foreach($orderTechCards->techCard->products as $product)
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->unitOfMeasurements->name }}</td>
                    <td>{{ $product->pivot->quantity * $orderTechCards->quantity}}</td>
                @endforeach
            @endforeach
        @endif
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
