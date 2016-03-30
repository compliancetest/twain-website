<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Community - Downloads | Twain</title>
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
                            <li class="forum-tab"><a href="communities-forum.php">Forum</a></li>
                            <li class="downloads-tab"><a href="communities-downloads.php">Downloads</a></li>
                            <li class="reports-tab"><a href="communities-reports.php" class="active">Reports</a></li>
                            <li class="admin-tab"><a href="communities-admin.php">Admin</a></li>
                        </ul>
                    </div>

                    <div class="community-tab-content">

                        <div class="community-downloads row">
                            <div class="col-md-12 table-responsive">
                                <table class="table downloads-list-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left">File name</th>
                                            <th>Size</th>
                                            <th>License Agreement</th>
                                            <th>Last Updated</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="display: none;">
                                            <td>
                                                <a href="#">Contributions_1.0_SoapUI_Reference_Client_V2.5.xml </a>
                                                <p>
                                                    Version: <strong>2.5</strong><br/>
                                                    SOAP UI Project file including Contributions 1.0 Conformance Level A test cases. Intended for use as a reference to support testing of customer products.
                                                </p>
                                                <p>Pull requests now require the soap:role="ebms" attribute in line with the ebMS3 specification. The security header also needs a soap:mustUnderstand attribute.</p>
                                            </td>
                                            <td class="text-nowrap text-center">475.79 RB</td>
                                            <td></td>
                                            <td class="text-nowrap text-center">2014-08-04</td>
                                            <td class="text-nowrap">
                                                <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit"></a>
                                                <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip" title="Delete"></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="empty-row ">No file uploaded yet</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="add-new-download-section">
                            <div class="add-new-download-default">
                                <a href="#add-new-download-section" id="add-new-download" class="add-new-download-link">Upload New File(s)</a>
                            </div>
                            <div id="add-new-download-section" style="display: none;">
                                <form name="newfileform" id="newfileform" action="" enctype="multipart/form-data" method="post">
                                    <h3>Upload New File(s)</h3>
                                    <div class="file-description-section">
                                        <label>File Version:</label>
                                        <input type="text" class="input" name="file_version[]" value="" />

                                        <input type="file" name="file[]" class="input-file" />
                                        <a href="#" class="btn btn-danger btn-with-icon btn-delete">Remove</a>

                                        <label>Description:</label>
                                        <textarea cols="20" rows="5" name="file_description[]" class="text"></textarea>
                                        <label>File License Agreement:</label>
                                        <textarea cols="20" rows="5" name="file_license[]" class="text"></textarea>
                                    </div>
                                    <div class="form-actions">
                                        <a href="#" id="add-more-file" class="add-new-download-link">Add New File</a>
                                        <div class="pull-right">
                                            <a href="#" class="btn btn-success btn-with-icon btn-upload">Upload &amp; Save</a>
                                            <a href="#" class="btn btn-default btn-with-icon btn-cancel">Cancel</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>
    <script>
        jQuery(document).ready(function($) {
            Page.communityDownloads.init();
        });
    </script>

    </body>
</html>
