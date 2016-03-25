<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Twain</title>
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

                <div class="community-header row">
                    <div class="community-logo">
                        <img src="images/gravatar.jpg" alt="SuperStream" />
                    </div>
                    <div class="community-short-description">
                        <h3>SuperStream</h3>
                        <p>SuperStream aims to improve efficiency in the Australian Superannuation industry through data standards. "Rollovers", where savings are transferred from one fund to another (mandatory for APRA regulated funds by July 1 2013 and all funds by July 1 2014). "Member Contributions", where regular payments are made by employers to funds of the employees choice (mandated for large employers by 1 July 2014 and all employers by 1 July 2015.</p>
                    </div>
                    <div class="community-membership-action">
                        <a href="#" class="btn btn-danger btn-lg">Cancel Membership</a>
                    </div>
                </div>

                <div class="community-tabs">
                    <div class="tabs-menu">
                        <ul>
                            <li class="test-suites-tab"><a href="#" class="active">Test Suites</a></li>
                            <li class="test-data-tab"><a href="#">Test Data</a></li>
                            <li class="articles-tab"><a href="#">Articles</a></li>
                            <li class="forum-tab"><a href="#">Forum</a></li>
                            <li class="downloads-tab"><a href="#">Downloads</a></li>
                            <li class="reports-tab"><a href="#">Reports</a></li>
                        </ul>
                    </div>

                    <div class="community-tab-content">

                        <div class="community-test-suites">
                            <div>

                                <div class="filter-panel">
                                    <div class="collapsible-panel-group" id="accordion">
                                        <div class="collapsible-panel">
                                            <div class="panel-heading">
                                                <h4 class="panel-title" data-toggle="collapse" data-target="#collapseType">Type</h4>
                                            </div>
                                            <div id="collapseType" class="panel-collapse collapse in">
                                                <div class="panel-body">
                                                    <ul>
                                                        <li><label><input type="checkbox" value="type1" name="type[]"> Type 1</label></li>
                                                        <li><label><input type="checkbox" value="type2" name="type[]"> Type 2</label></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapsible-panel">
                                            <div class="panel-heading">
                                                <h4 class="panel-title" data-toggle="collapse" data-target="#collapseIssuer">Issuer</h4>
                                            </div>
                                            <div id="collapseIssuer" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <ul>
                                                        <li><label><input type="checkbox" value="option1" name="issuer[]"> Option 1</label></li>
                                                        <li><label><input type="checkbox" value="option2" name="issuer[]"> Option 2</label></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapsible-panel">
                                            <div class="panel-heading">
                                                <h4 class="panel-title" data-toggle="collapse" data-target="#collapseYearOfIssue">Year of Issue</h4>
                                            </div>
                                            <div id="collapseYearOfIssue" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <div class="panel-body">
                                                        <ul>
                                                            <li><label><input type="checkbox" value="option1" name="issue_year[]"> Option 1</label></li>
                                                            <li><label><input type="checkbox" value="option2" name="issue_year[]"> Option 2</label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="collapsible-panel">
                                            <div class="panel-heading">
                                                <h4 class="panel-title" data-toggle="collapse" data-target="#collapseStatus">Status</h4>
                                            </div>
                                            <div id="collapseStatus" class="panel-collapse collapse">
                                                <div class="panel-body">
                                                    <div class="panel-body">
                                                        <ul>
                                                            <li><label><input type="checkbox" value="option1" name="status[]"> Option 1</label></li>
                                                            <li><label><input type="checkbox" value="option2" name="status[]"> Option 2</label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-content-panel">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Published</th>
                                                    <th>Status</th>
                                                    <th>Products</th>
                                                    <th>Notify Changes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
