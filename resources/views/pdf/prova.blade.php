<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Applicazione web per la logistica intelligente nelle Smart City">
    <meta name="author" content="Nicola Favata">
</head>

<body>
@foreach ($work as $t)
    {{$t->url_file_batch_inventory}}
@endforeach
</body>
</html>

