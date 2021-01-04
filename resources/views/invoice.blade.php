<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Invoice</title>

    <style>
        body {
            background: #fff none;
            font-size: 12px;
        }
        h2 {
            font-size: 28px;
            color: #ccc;
        }
        .container {
            padding-top: 30px;
        }
        .invoice-head td {
            padding: 0 8px;
        }
        .table th {
            vertical-align: bottom;
            font-weight: bold;
            padding: 8px;
            line-height: 20px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table tr.row td {
            border-bottom: 1px solid #ddd;
        }
        .table td {
            padding: 8px;
            line-height: 20px;
            text-align: left;
            vertical-align: top;
        }
    </style>
</head>
<body>
<div class="container">
    <table style="margin-left: auto; margin-right: auto;" width="550">
        <tr>
            <td width="160">
                &nbsp;
            </td>

            <!-- Organization Name / Image -->
            <td align="right">
                <strong>{{ $vendor }}</strong>
            </td>
        </tr>
        <tr valign="top">
            <td style="font-size: 28px; color: #ccc;">
                Invoice
            </td>

            <!-- Organization Name / Date -->
            <td>
                <br><br>
                <strong>To:</strong> {{ $subscription->subscriber()->name }}
                <br>
                <strong>Date:</strong> {{ $invoice->date()->toFormattedDateString() }}
            </td>
        </tr>
        <tr valign="top">
            <!-- Organization Details -->
            <td style="font-size:9px;">
                {{ $vendor }}<br>

                @if (isset($street))
                    {{ $street }}<br>
                @endif

                @if (isset($location))
                    {{ $location }}<br>
                @endif

                @if (isset($phone))
                    <strong>T</strong> {{ $phone }}<br>
                @endif

                @if (isset($url))
                    <a href="{{ $url }}">{{ $url }}</a>
                @endif
            </td>
            <td>
                <!-- Invoice Info -->
                <p>
                    <strong>Plan:</strong> {{ $subscription->plan->name }}<br>
                    <strong>Invoice Number:</strong> {{ '[INV-'.$invoice->number.']' }}<br>
                </p>

                <!-- Extra / VAT Information -->
                @if (isset($vatInfo))
                    <p>
                        {{ $vatInfo }}
                    </p>
                @endif

                <br><br>

                <!-- Invoice Table -->
                <table width="100%" class="table" border="0">
                    <tr>
                        <th align="left">Description</th>
                        <th align="right">Date</th>

                        @if ($invoice->hasTax())
                            <th align="right">Tax</th>
                        @endif

                        <th align="right">Amount</th>
                    </tr>

                    <!-- Display The Invoice Items -->
                    <tr class="row">
                        <td colspan="2">{{ $subscription->plan->name }}</td>

                        @if ($invoice->hasTax())
                            <td>
                                @foreach ($invoice->tax as $tx)
                                    {{ $tx['name'].' '.$tx['percentage'] }}%
                                @endforeach
                            </td>
                        @endif

                        <td>{{ $subscription->plan->amount.' x '.$subscription.quantity }}</td>
                    </tr>

                    <!-- Display The Subtotal -->
                    @if ($invoice->hasTax())
                        <tr>
                            <td colspan="{{ $invoice->hasTax() ? 3 : 2 }}" style="text-align: right;">Subtotal</td>
                            <td>{{ $invoice->amount_without_tax }}</td>
                        </tr>
                    @endif

                    <!-- Display The Taxes -->
                    @foreach ($invoice->tax as $tax)
                        <tr>
                            <td colspan="3" style="text-align: right;">
                                {{ $tax['name'] }} 
                                ({{ $tax['percentage'] }}%{{ ' incl.' }})
                            </td>
                            <td>{{ $tax['amount'] }}</td>
                        </tr>
                    @endforeach

                    <!-- Display The Final Total -->
                    <tr>
                        <td colspan="{{ $invoice->hasTax() ? 3 : 2 }}" style="text-align: right;">
                            <strong>Total</strong>
                        </td>
                        <td>
                            <strong>{{ $invoice->amount }}</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
