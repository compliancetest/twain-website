<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>User Communities Directory | Twain</title>
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
                <div class="community-search row">
                    <div class="col-sm-4">
                        <div class="search-form">
                            <form method="get" action="">
                                <input type="text" placeholder="Search Communities..." value="<?php echo $_GET['s'] ?>" class="search-form-input" id="groupsSearch" name="s">
                                <button class="submit-search-btn" type="submit"><span class="submit-search-btn-icon">Search</span></button>
                            </form>
                        </div>
                    </div>
                    <p class="col-sm-6 search-result-count">Viewing group 1 to 2 (of 2 groups)</p>
                </div>
                <div class="table-responsive community-lists">
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-left">Community Name</th>
                                <th class="col-sm-1">Test Suites</th>
                                <th class="col-sm-1 text-left">Members</th>
                                <th>Compliant Products</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="community-image"><a href="communities-test-suites.php"><img width="50" height="50" title="ebMS3" alt="Community logo of ebMS3" src="images/gravatar.jpg"></a></td>
                                <td class="community-name">
                                    <a href="communities-test-suites.php">ebMS3</a>
                                    <p>ebMS3 is a standard that describes a protocol neutral method for exchanging electronic business messages. It is produced by the Organization for the Advancement of Structured Information Standards (OASIS) and has [...]</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">11</td>
                                <td class="text-center">0</td>
                                <td class="text-center"><a class="btn btn-danger btn-lg joinCommunity" href="#confirmJoinCommunity" data-community-id="1">Join Community</a></td>
                            </tr>
                            <tr>
                                <td class="community-image"><a href="communities-test-suites.php"><img width="50" height="50" title="ebMS3" alt="Community logo of ebMS3" src="images/gravatar.jpg"></a></td>
                                <td class="community-name">
                                    <a href="communities-test-suites.php">Community</a>
                                    <p>Community description</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">11</td>
                                <td class="text-center">0</td>
                                <td class="text-center"><span class="status status-sent status-lg">Request Sent</span></td>
                            </tr>
                            <tr>
                                <td class="community-image"><a href="communities-test-suites.php"><img width="50" height="50" title="ebMS3" alt="Community logo of ebMS3" src="images/gravatar.jpg"></a></td>
                                <td class="community-name">
                                    <a href="communities-test-suites.php">SuperStream</a>
                                    <p>SuperStream aims to improve efficiency in the Australian Superannuation industry through data standards. "Rollovers", where savings are transferred from one fund to another (mandatory for APRA regulated funds by [...]</p>
                                </td>
                                <td class="text-center">4</td>
                                <td class="text-center">11</td>
                                <td class="text-center">0</td>
                                <td class="text-center"><a class="btn btn-danger btn-lg" href="#confirmCancelMembership" data-toggle="modal" data-community-id="3">Cancel Membership</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="confirmCancelMembership" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                        Confirm Community Membership Cancellation
                    </div>
                    <div class="modal-body">
                        This will cancel your membership of the SuperStream community. Are you sure?
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-success btn-with-icon btn-confirm">Confirm</a>
                        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal modal-small fade" id="confirmJoinCommunity" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-dismiss="modal" aria-label="Close">Close</button>
                        Community Registration
                    </div>
                    <div class="modal-body">
                        You need to join the community of interest in order to view Test Cases and Participate in the Forum
                        <div class="popup-terms-box">
                            <input type="checkbox" id="agree_community_terms" value="agree" name="agree_terms"> I agree with <a href="#readTermsAndConditions" data-toggle="modal" data-dismiss="modal">Terms &amp; Conditions</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-success btn-with-icon btn-confirm" id="registerInCommunity">Register</a>
                        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="readTermsAndConditions" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close-modal" data-tooltip="tooltip" title="Close popup" data-placement="left" data-target="#confirmJoinCommunity" data-toggle="modal" data-dismiss="modal" aria-label="Close">Close</button>
                        Terms and Conditions
                    </div>
                    <div class="modal-body">
                        <div class="modal-terms-content">
                            <p>These terms and conditions are specific to the SuperStream Community and supplement the general terms and conditions for registration on <a href="#">www.compliancetest.net</a> </p>
                            <ol>
                                <li>In order to access the SuperStream Community features, you must become a member of the community.</li>
                                <li>Membership of the SuperStream Community is limited to persons or organisations that have an active role in implementation (or implementation support) of the SuperStream standards (eg Funds, Administrators, Employers, Gateways, Software providers).</li>
                                <li>We reserve the right to terminate your membership at any time if you breach these terms and conditions.</li>
                                <li>You are responsible for all and any Content you contribute via the community forum or wiki. When you provide Content you retain ownership of the intellectual property in that information however you grant all current and future community members unrestricted and royalty free rights to use your content for any purpose that supports the implementation of SuperStream stndards. This licence ends when you cease your membership except for Content which has already been released as part of the Service.</li>
                                <li>We reserve the right but will not have an obligation to remove or refuse to distribute any Content. We also reserve the right to adapt or modify your Content for any reason including for distribution purposes.&nbsp;</li><li>By posting Content on this website, you provide us with an undertaking that such Content does not infringe the rights of someone else and that it does not violate the law in any other way such as by being defamatory, being of racist content or is threatening.&nbsp;</li><li>To the extent permitted by law, you release and discharge us from any liability or claim arising out of any loss or damage that may be suffered or incurred as a result of your participation in the SuperStream community.&nbsp;</li><li>We respect your privacy. Our compliance with privacy legislation is set out in our separate Privacy Policy which may be accessed from our home page.</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-with-icon btn-cancel" data-target="#confirmJoinCommunity" data-toggle="modal" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-success btn-with-icon btn-confirm" onclick="document.getElementById('agree_community_terms').checked = true"  data-target="#confirmJoinCommunity" data-toggle="modal" data-dismiss="modal">Agree</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>
    <script>
        // Search autofocus
        jQuery(document).ready(function() {
            var inputSearch = jQuery('#groups_search');
            var searchTerm = inputSearch.val();
            if (searchTerm != ''){
                inputSearch.focus().val('').val(searchTerm);
            } else {
                inputSearch.focus();
            }
        });
        Page.communities.init();
    </script>

    </body>
</html>
