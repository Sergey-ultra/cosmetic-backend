<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
    <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="{{ asset('/css/normalize.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/article.css') }}">
    <title>Smart-Beautiful - агрегатор цен косметических товаров</title>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(87589913, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/87589913" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</head>
<body>
<section class="top-menu">
    <div class="container">
        <div class="top-menu__wrapper">
            <div class="city-choice city">
                <fa icon="map-marker-alt"></fa>
                Москва
            </div>
        </div>
    </div>
</section>
<nav class="menu">
    <div class="container">
        <div class="menu__layer"></div>
            <div class="menu__wrapper">
                <ul class="menu__list">
            @foreach($links as $link)
            <li class="menu__item">
                <a href="{{ $link['url'] }}">{{ $link['title'] }}</a>
            </li>
            @endforeach
            <li  class="menu__item">
                <a href="/article">Статьи</a>
            </li>
        </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <h2>Статьи</h2>
    @yield('content')
</div>


</body>
</html>
