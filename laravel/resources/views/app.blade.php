<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ isset($pageTitle) ? $pageTitle : 'Communities' }} | TWAIN</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ getSiteUrl() }}/laravel/resources/assets/images/favicons/favicon.ico" type="image/x-icon">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ getSiteUrl() }}/laravel/resources/assets/css/style.css">
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery-1.11.2.min.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/bootstrap.min.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery.slimmenu.min.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery.validate.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jsonary-super-bundle.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/clipboard.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/redactor.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/vendor/jquery.form.js"></script>
    <script src="{{ getSiteUrl() }}/laravel/resources/assets/js/scripts.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
</head>
<body>
<!--[if lt IE 8]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->
<div id="main-wrapper">

    @include('header')

    <?php flushMessages();?>

    @yield('content')


</div>

@include('footer')
<script>
    jQuery(document).ready(function($) {
        Page.communityCreate.init();
    });
</script>
</body>
</html>