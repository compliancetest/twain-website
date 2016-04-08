<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Community Name | Admin | Twain</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="main-wrapper">

        <?php include_once('parts/header.php'); ?>

        <div class="container main-container">
            <div class="main-content">

                <div class="community-tabs">
                    <div class="tabs-menu">
                        <ul>
                            <li class="test-suites-tab"><a href="communities-test-suites.php">Test Suites</a></li>
                            <li class="test-data-tab"><a href="communities-test-data.php">Test Data</a></li>
                            <li class="articles-tab"><a href="communities-articles.php">Articles</a></li>
                            <li class="forum-tab"><a href="#">Forum</a></li>
                            <li class="downloads-tab"><a href="communities-downloads.php">Downloads</a></li>
                            <li class="reports-tab"><a href="communities-reports.php">Reports</a></li>
                            <li class="admin-tab"><a href="communities-admin.php" class="active">Admin</a></li>
                        </ul>
                    </div>


                </div>

            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>
    <script src="js/vendor/redactor.js"></script>
    <script>
        jQuery(document).ready(function($) {
            Page.communityAdmin.init();
        });
    </script>

    </body>
</html>
