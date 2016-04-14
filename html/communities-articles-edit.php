<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Community - Create Article | Twain</title>
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
                    <h1>Edit Article</h1>
                    <div class="page-title-actions">
                        <a href="communities-articles-create.php" class="btn btn-success btn-with-icon btn-add">Add new article</a>
                        <a href="#" class="btn btn-primary btn-with-icon btn-history">History</a>
                        <a href="#" class="btn btn-primary btn-icon btn-print">Print</a>
                    </div>
                </div>

                <div class="community-tabs article-create">

                    <form action="#" name="article-create-form" method="post" data-validate="validate">

                        <div class="colored-box">
                            <div class="colored-box-header">Information</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content">
                                    <div class="form-group">
                                        <label for="articleTitle">Title</label>
                                        <input type="text" value="" class="form-control" name="article[title]" id="articleTitle" required data-msg-required="Title is required">
                                    </div>

                                    <div class="form-group">
                                        <label for="articleContent">Content</label>
                                        <textarea class="redactor_editor" name="article_content" id="articleContent"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="colored-box">
                            <div class="colored-box-header">Attachments</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content article-attachments-box">
                                    <a href="#" id="addNewArticleFile" class="btn btn-primary btn-with-icon btn-add">Add Files</a>
                                    <ul class="attached-file-list">
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-zip" target="_blank">File1.zip</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-mp3" target="_blank">File2.mp3</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a> </li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-ics" target="_blank">File3.ics</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-odb" target="_blank">File4.odb</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a> </li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-odg" target="_blank">File5.odg</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-jpg" target="_blank">File6.jpg</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-pdf" target="_blank">File7.pdf</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-xls" target="_blank">File8.xls</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-txt" target="_blank">File9.txt</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                        <li><a href="#" class="doc-attachment-mime-icon doc-attachment-mime-mp4" target="_blank">File10.mp4</a> <a href="#" class="btn btn-danger btn-with-icon btn-delete removeFileLink" data-tooltip="tooltip" title="Remove file">Remove</a></li>
                                    </ul>

                                    <script>
                                        jQuery(document).ready(function($) {
                                            Page.communityArticleManage.init();
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="colored-box">
                            <div class="colored-box-header">Associated Community</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content form-horizontal">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label for="articleAssociatedCommunity" class="control-label">Which community should this Article be associated with?</label>
                                            <p class="label-note">(Optional) Note that the Access settings available for this Article may be limited by the privacy settings of the community you choose.</p>
                                        </div>
                                        <div class="col-sm-6">
                                            <select name="article[community]" id="articleAssociatedCommunity" class="form-control">
                                                <option>Community 1</option>
                                                <option>Community 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="colored-box">
                            <div class="colored-box-header">Access</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content form-horizontal">
                                    <div class="form-group">
                                        <label for="articleReadAccess" class="col-sm-4 control-label">Who can read this article?</label>
                                        <div class="col-sm-8">
                                            <select name="article[read]" id="articleReadAccess" class="form-control">
                                                <option selected="selected" value="anyone">Anyone</option>
                                                <option value="creator">The Article author only</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="articleEditAccess" class="col-sm-4 control-label">Who can edit this article?</label>
                                        <div class="col-sm-8">
                                            <select name="article[edit]" id="articleEditAccess" class="form-control">
                                                <option selected="selected" value="anyone">Anyone</option>
                                                <option value="creator">The Article author only</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="articleReadCommentsAccess" class="col-sm-4 control-label">Who can read comments on this article?</label>
                                        <div class="col-sm-8">
                                            <select name="article[read_comments]" id="articleReadCommentsAccess" class="form-control">
                                                <option selected="selected" value="anyone">Anyone</option>
                                                <option value="creator">The Article author only</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="articlePostCommentsAccess" class="col-sm-4 control-label">Who can post comments on this article?</label>
                                        <div class="col-sm-8">
                                            <select name="article[post_comments]" id="articlePostCommentsAccess" class="form-control">
                                                <option selected="selected" value="anyone">Anyone</option>
                                                <option value="creator">The Article author only</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="articleViewHistoryAccess" class="col-sm-4 control-label">Who can view the history of this article?</label>
                                        <div class="col-sm-8">
                                            <select name="article[view_history]" id="articleViewHistoryAccess" class="form-control">
                                                <option selected="selected" value="anyone">Anyone</option>
                                                <option value="creator">The Article author only</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="colored-box">
                            <div class="colored-box-header">Tags</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content form-horizontal">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label for="articleWords" class="control-label">Tags are words or phrases that help to describe and organize your Articles.</label>
                                            <p class="label-note">Separate tags with commas (for example: orchestra, snare drum, piccolo, Brahms)</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea name="article[words]" id="articleWords" class="form-control" cols="30" rows="5"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="colored-box">
                            <div class="colored-box-header">Parent</div>
                            <div class="colored-box-body">
                                <div class="colored-box-content form-horizontal">
                                    <div class="form-group">
                                        <div class="col-sm-4">
                                            <label for="articleParent" class="control-label">Select a parent for this Article.</label>
                                            <p class="label-note">(Optional) Assigning a parent Article means that a link to the parent will appear at the bottom of this Article, and a link to this Articles will appear at the bottom of the parent.</p>
                                        </div>
                                        <div class="col-sm-8">
                                            <select name="article[parent]" id="articleParent" class="form-control">
                                                <option>Article 1</option>
                                                <option>Article 2</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions clearfix">
                            <a href="#" class="btn btn-danger btn-with-icon btn-delete pull-right">Delete</a>
                            <div class="pull-left">
                                <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                                <button type="button" class="btn btn-default btn-with-icon btn-cancel">Cancel</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>

    </body>
</html>
