<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/style.css" rel="stylesheet">
    <title>وضعیت سفارش</title>
</head>
<body>
<div align="center">
    <div class="payment-info">
        <b>{{ $message }}</b>
        <br/>
        <b>
            <span> کد پیگیری :</span>
            <span>{{ $refid }}</span>
        </b>
        <br/>
    </div>
    <a href="unitydl://{{setting('appsetting.package_name')}}?{{$Payment->status}}" class="green_btn">بازگشت به
        اپلیکیشن</a>

</div>
</body>
</html>
