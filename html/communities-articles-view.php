<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>SuperStream Contributions Test Data Context Model | Twain</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/laravel/resources/assets/css/style.css">

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/laravel/resources/assets/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <script src="/laravel/resources/assets/js/vendor/bootstrap.min.js"></script>
        <script src="/laravel/resources/assets/js/vendor/jquery.slimmenu.min.js"></script>
        <script src="/laravel/resources/assets/js/vendor/jquery.validate.js"></script>
        <script src="/laravel/resources/assets/js/vendor/redactor.js"></script>
        <script src="/laravel/resources/assets/js/scripts.js"></script>
    </head>
    <body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="main-wrapper">

        <?php include_once('parts/header.php'); ?>

        <div class="container main-container">
            <div class="main-content">

                <div class="page-title">
                    <h1>SuperStream Rollover Overview</h1>
                    <div class="page-title-actions">
                        <a href="communities-articles-create.php" class="btn btn-success btn-with-icon btn-add">Edit</a>
                        <a href="communities-articles-history.php" class="btn btn-primary btn-with-icon btn-history">History</a>
                        <a href="communities-articles.php" class="btn btn-default btn-with-icon btn-back">Back</a>
                        <a href="#" class="btn btn-primary btn-icon btn-print">Print</a>
                    </div>
                </div>

                <div class="article-view">
                    <div class="static-content">
                        <h2>Sequence Diagram</h2>
                        <p>The SuperStream Rollover process is summarised in the diagram below.  The key roles are:</p>
                        <ul>
                            <li>The Member, identified by a TFN.  Note that, for members that do not disclose their TFN, then the fund member ID may be used.</li>
                            <li>The Transferring Fund, identified by  an ABN and USI (except for SMSFs which have no USI).</li>
                            <li>The Receiving Fund, identified by an ABN and USI (except for SMSFs which have no USI).</li>
                            <li>The terms “Transferring” and “Receiving” when used with respect to Superannuation Funds refer to the direction of money flow and not message flow (ie savings are moved from transferring fund to receiving fund)</li>
                            <li>The Transferring Fund Administrator and the Receiving Fund Administrator, both identified by an ABN.</li>
                            <li>Fund Administrators are largely transparent in the message exchange process but will usually be theorparty in the message exchange.</li>
                        </ul>
                        <p>The ComplianceTest SuperStream Rollover<a href="https://www.compliancetest.net/test-suite/superstream-rollover-test-suite-v-1-1/">Test Suite</a>is designed to test conformance with the<a href="http://www.ato.gov.au/uploadedFiles/Content/SPR/downloads/spr00335171_Rollover_Message_Implementation.pdf">SuperStream  Rollover MIG</a>released by the Australian Tax Office.</p>
                        <div class="wp-caption alignnone"><img width="738" height="1063" style="outline: 0px none; margin: 0px; padding: 0px; text-shadow: none; border: medium none rgba(0, 0, 0, 0.498); vertical-align: middle; z-index: 1; background: transparent none repeat scroll 0% 0%;" src="https://www.compliancetest.net/wp-content/uploads/bp-attachments/372/Rollover.ContextModel.png" alt="" title="Superstream Rollover Context Model"><p class="wp-caption-text">Superstream Rollover Context Model</p></div>
                        <p><a href="https://www.compliancetest.net/wp-content/uploads/bp-attachments/372/Rollover.ContextModel.png"> </a></p>
                        <h2><strong> Sequence Description </strong></h2>
                        <table>
                            <tbody>
                            <tr>
                                <td><strong>Step</strong></td>
                                <td><strong>Pattern</strong></td>
                                <td><strong>Description</strong></td>
                            </tr>
                            <tr>
                                <td>1a</td>
                                <td>Manual</td>
                                <td>This process is typically not electronic and so not mandated by the MIG.A Fund member requests the receiving fund to rollover the member’s savings from the transferring fund.  The receiving fund forwards the request to their fund administrator for action.</td>
                            </tr>
                            <tr>
                                <td>1b</td>
                                <td>2 way sync</td>
                                <td>This is a mandatory step for each rollover member.The ATO hosted Superstream Tax File Number (TFN) Integrity Check service (STIC) is used to validate the identity of the member requesting the rollover.</td>
                            </tr>
                            <tr>
                                <td>1c</td>
                                <td>2 way sync</td>
                                <td>This is a mandatory step prior to each initiate rollover and rollover transaction.The ATO hosted SuperStream Fund validation service (FVS) is used for two purposes.  The first is to confirm that the transferring fund USI (Superannuation Product Identifier) is valid.  The second it to obtain an end point location to send the initiate rollover request message.</td>
                            </tr>
                            <tr>
                                <td>1d</td>
                                <td>1 way ebMS push</td>
                                <td>Optional Message &ndash; needed when the request for rollover is made by the member to the receiving fund.The receiving fund sends an Initiate Rollover Request message to the transferring fund.  Although the logical message flow is fund-to-fund, the physical message flow is typically between fund administrators.</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>1 way ebMS push</td>
                                <td>Optional message &ndash; needed only if there is an error condition in the initiate rollover request.The transferring fund will validate the rollover request and confirm with the member that the rollover request is genuine.  The mechanism by with the transferring fund confirms intent with the member is not defined by the specification.If there are either technical errors in the message or if a business rule is violated (eg the member did not confirm the request), then the error response is sent to the receiving fund.The ebMS header conversationID element must contain the same value as that in the initiate rollover request message.  This is because these are asynchronous messages and much be correlated.</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>1 way ebMS push</td>
                                <td>Mandatory message &ndash; always needed when there is a rollover payment.The transferring fund sends the rollover transaction request message to the receiving fund.  The rollover transaction request may contain many member rollovers, provided they are all between the same two funds. Although the logical message flow is fund-to-fund, the physical message flow is typically between fund administrators.Note that the ebMS header conversationID is not REQUIRED to be the same as that in the initiate rollover messages.  This is because the individual member rollovers contained in the rollover transaction may be different to those in the rollover request.</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>BECS Payment</td>
                                <td>This process is not defined by the Superstream specification &ndash; it assumes that standard payment mechansism (typically BECS direct entry) are used.The transferring fund makes a payment to the receiving fund for an amount equal to the total amount defined in the corresponding rollover transaction request message.</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>1 way ebMS push</td>
                                <td>Mandatory message for every corresponding rollover transaction request.The receiving fund performs a bank reconciliation to confirm that the payment defined in the rollover transaction request has arrived.  If the funds have cleared and there are no errors in the rollover transaction request message then a positive rollover outcome response is sent.  Otherwise a business error is reported.Note that the ebMS header conversationID value in the outcome response message must match that of the corresponding rollover transaction request.</td>
                            </tr>
                            </tbody>
                        </table>
                        <h2>Further Information</h2>
                        <p>Please review the “Identifier Mapping” article to gain a clear understanding of how the various party identifiers (TFNs, USIs, ABNs, etc) are mapped to ebMS header parts and XBRL payload context definitions.</p>
                        <p>Please review the “Test Data” tabs to understand the reference data sets that are used by ComplianceTest to represent funds, employers, and members.</p>
                        <p>Please review the “Test Suite” tab to understand the construction of the Rollover test suite from the constituent test cases.</p>
                    </div>

                    <div class="article-attachments-box">
                        <h2>Attachments</h2>
                        <ul class="attached-file-list">
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-zip" target="_blank">File1.zip</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-mp3" target="_blank">File2.mp3</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-ics" target="_blank">File3.ics</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-odb" target="_blank">File4.odb</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-odg" target="_blank">File5.odg</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-jpg" target="_blank">File6.jpg</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-pdf" target="_blank">File7.pdf</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-xls" target="_blank">File8.xls</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-txt" target="_blank">File9.txt</a></li>
                            <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-mp4" target="_blank">File10.mp4</a></li>
                        </ul>
                    </div>

                    <div class="article-tags">
                        Tags: <a href="#">rollover</a>, <a href="#">superstream</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
