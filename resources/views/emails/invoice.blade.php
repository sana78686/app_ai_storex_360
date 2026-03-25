<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; font-size:14px">

<p>Hello,</p>

<p>
Thank you for your purchase.<br>
Your invoice <strong>{{ $invoice->invoice_number }}</strong> is ready.
</p>

<p>
👉 <a href="{{ $downloadUrl }}">Download your invoice (PDF)</a>
</p>

<p>
Order details:<br>
<a href="{{ $orderUrl }}">{{ $orderUrl }}</a>
</p>

<p>
Best regards,<br>
{{ config('app.name') }}
</p>

</body>
</html>
