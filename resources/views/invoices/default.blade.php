<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        @page {
            margin: 25mm 20mm 30mm 20mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
        }

        /* ---------- HEADER ---------- */
        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .logo {
            width: 160px;
            margin-bottom: 10px;
        }

        .company-address {
            font-size: 10px;
            margin-bottom: 25px;
        }

        .invoice-meta {
            width: 100%;
            margin-bottom: 20px;
        }

        .invoice-meta td {
            vertical-align: top;
            padding: 2px 0;
        }

        .right {
            text-align: right;
        }

        /* ---------- TABLE ---------- */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table.items th {
            border-bottom: 2px solid #000;
            padding: 6px 4px;
            font-weight: bold;
            text-align: left;
        }

        table.items td {
            border-bottom: 1px solid #ddd;
            padding: 6px 4px;
        }

        .amount {
            text-align: right;
        }

        /* ---------- TOTAL ---------- */
        .totals {
            margin-top: 15px;
            width: 100%;
        }

        .totals td {
            padding: 4px 0;
        }

        .totals .label {
            text-align: right;
        }

        .totals .value {
            text-align: right;
            width: 100px;
        }

        /* ---------- FOOTER ---------- */
        .footer {
            position: fixed;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            font-size: 9px;
            color: #444;
            border-top: 1px solid #ccc;
            padding-top: 6px;
        }
        .footer {
    position: fixed;
    bottom: 15mm;
    left: 20mm;
    right: 20mm;
    font-size: 9px;
    color: #444;
    border-top: 1px solid #ccc;
    padding-top: 8px;
}

    </style>
</head>

<body>

    {{-- ---------- HEADER ---------- --}}
    <div class="header">

        @if($logoPath && file_exists($logoPath))
        <img src="{{ $logoPath }}" class="logo">
        @endif


        <div class="company-address">
            <strong>{{ $settings->company_name }}</strong><br>
            {{ $settings->street ?? '' }}<br>
            {{ $settings->zip }} {{ $settings->city }}<br>
            {{ $settings->country }}<br>
            Email: {{ $settings->default_email }}
        </div>

    </div>

    {{-- ---------- INVOICE META ---------- --}}
    <table class="invoice-meta">
        <tr>
            <td>
                <strong>Bill To:</strong><br>
                {{ $invoice->customer_email }}
            </td>
            <td class="right">
                <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
                <strong>Date:</strong> {{ $invoice->created_at->format('d.m.Y') }}
            </td>
        </tr>
    </table>

    @if($invoice->intro_text)
    <p>{{ $invoice->intro_text }}</p>
    @endif

    {{-- ---------- ITEMS TABLE ---------- --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width: 55%">Description</th>
                <th style="width: 15%">Quantity</th>
                <th style="width: 15%">Unit</th>
                <th style="width: 15%" class="amount">Total</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $product->name }}</td>
                <td>1</td>
                <td>Piece</td>
                <td class="amount">
                    {{ number_format($invoice->subtotal, 2, ',', '.') }}
                    {{ $invoice->currency }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ---------- TOTALS ---------- --}}
    <table class="totals">
        <tr>
            <td class="label">Subtotal (net):</td>
            <td class="value">
                {{ number_format($invoice->subtotal, 2, ',', '.') }}
                {{ $invoice->currency }}
            </td>
        </tr>

        <tr>
            <td class="label">VAT (0%):</td>
            <td class="value">0.00 {{ $invoice->currency }}</td>
        </tr>

        <tr>
            <td class="label"><strong>Total Amount:</strong></td>
            <td class="value">
                <strong>
                    {{ number_format($invoice->total, 2, ',', '.') }}
                    {{ $invoice->currency }}
                </strong>
            </td>
        </tr>
    </table>

    {{-- ---------- FOOTER (LEGAL) ---------- --}}
   {{-- @if($invoice->footer_text) --}}
<div class="footer">

    <table width="100%">
        <tr>
            {{-- Company --}}
            <td width="33%" valign="top">
                {{-- <strong>Your Company Name</strong><br> --}}
               <strong> {{ $settings->company_name }}</strong><br>
                {{ $settings->street ?? '' }}<br>
                {{ $settings->zip }} {{ $settings->city }}<br>
                {{ $settings->country }}
            </td>

            {{-- Contact Information --}}
            <td width="33%" valign="top">
                <strong>Contact Information</strong><br>
                Email: {{ $settings->default_email }}<br>
                Phone: {{ $settings->calling_code ?? '' }}<br>
                Website: {{ $settings->website ?? '' }}
            </td>

            {{-- Payment Details --}}
            <td width="33%" valign="top">
                <strong>Payment Details</strong><br>
                Bank Name: {{ $settings->bank_name ?? '-' }}<br>
                IBAN: {{ $settings->iban ?? '-' }}<br>
                BIC / SWIFT: {{ $settings->swift ?? '-' }}
            </td>
        </tr>
    </table>

</div>
{{-- @endif --}}


</body>

</html>
