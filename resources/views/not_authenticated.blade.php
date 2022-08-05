<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Redirecting you</title>
</head>
<body>

<script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
<script>
    message = JSON.stringify({
        message: "Shopify.API.remoteRedirect",
        data: { location: "/admin/apps/{{ config('shopify.key') }}" }
    });
    window.parent.postMessage(message, "*");
</script>
</body>
</html>