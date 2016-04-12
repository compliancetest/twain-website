<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Create a Community | Twain</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800|Oswald:400,300,700" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="/laravel/resources/assets/css/style.css">

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="/laravel/resources/assets/js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
        <script src="/laravel/resources/assets/js/vendor/bootstrap.min.js"></script>
        <script src="/laravel/resources/assets/js/vendor/jquery.slimmenu.min.js"></script>
        <script src="/laravel/resources/assets/js/vendor/jquery.validate.js"></script>
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

                <div class="create-community">
                    <div class="page-title">
                        <h1>Create a Community </h1>
                    </div>
                    <div class="community-admin">
                        <form class="form" action="#" method="post" data-validate="validate">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="colored-box">
                                        <div class="colored-box-header">Details</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <div class="form-group">
                                                    <label for="community-name">Community Name (required):</label>
                                                    <input type="text" value="" class="form-control" id="community-name" size="80" name="community-name" required data-msg-required="Please fill this field">
                                                </div>
                                                <div class="form-group">
                                                    <label for="community-desc">Community Description (required): </label>
                                                    <textarea rows="5" class="form-control" id="community-desc" name="community-desc" required data-msg-required="Please fill this field"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Display Image</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content community-image-management">
                                                <div class="community-image">
                                                    <img src="images/gravatar.jpg" alt="">
                                                </div>
                                                <div class="community-avatar-description">
                                                    <p>Upload an image to use as an avatar for this community. The image will be shown on the main community page, and in search results.</p>
                                                    <p>Click below to select a JPG, GIF or PNG format photo from your computer and then click 'Upload Image' to proceed.</p>
                                                    <div class="upload-file-field">
                                                        <input type="file" name="file" class="input-file" data-file-type="image" data-file-extensions="(.jpg, .png, .gif or .jpeg file)" />
                                                    </div>
                                                    <a href="#" class="btn btn-success btn-with-icon btn-add">Upload Image</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Community Forum</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <p>Create a discussion forum to allow members of this community to communicate in a structured, bulletin-board style fashion.</p>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" value="1" name="create-community-forum"> Yes. I want this community to have a forum.
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-6">
                                    <div class="colored-box">
                                        <div class="colored-box-header">Community Articles</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <div class="form-group">
                                                    <label for="bp-docs[group-enable]"> <input type="checkbox" checked="checked" value="1" id="bp-docs-group-enable" name="bp-docs[group-enable]"> Enable Articles for this community</label>
                                                </div>
                                                <div class="form-group">
                                                    <label for="bp-docs[can-create-admins]">Minimum role to associate Article with this community:</label>
                                                    <select class="form-control" name="bp-docs[can-create]">
                                                        <option selected="selected" value="admin">Community Admin</option>
                                                        <option value="mod">Community Support</option>
                                                        <option value="member">Community Member</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Privacy Options</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <div class="form-group">
                                                    <label>
                                                        <input type="radio" value="public" name="group-status">
                                                        <strong>This is a public community</strong>
                                                    </label>
                                                    <ul class="privacy-options-list">
                                                        <li>Any site member can join this community.</li>
                                                        <li>This community will be listed in the communities directory and in search results.</li>
                                                        <li>Community content and activity will be visible to any site member.</li>
                                                    </ul>
                                                </div>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="radio" checked="checked" value="private" name="group-status">
                                                        <b>This is a private community</b>
                                                    </label>
                                                    <ul class="privacy-options-list">
                                                        <li>Only users who request membership and are accepted can join the community.</li>
                                                        <li>This community will be listed in the communities directory and in search results.</li>
                                                        <li>Community content and activity will only be visible to members of the community.</li>
                                                    </ul>
                                                </div>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="radio" value="hidden" name="group-status">
                                                        <b>This is a hidden community</b>
                                                    </label>
                                                    <ul class="privacy-options-list">
                                                        <li>Only users who are invited can join the community.</li>
                                                        <li>This community will not be listed in the communities directory or search results.</li>
                                                        <li>Community content and activity will only be visible to members of the community.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Community Invitations</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <p>Which members of this community are allowed to invite others?</p>
                                                <ul class="community-invitation-options">
                                                    <li>
                                                        <label>
                                                            <input type="radio" name="group-invite-status" value="members">
                                                            <strong>All community members</strong>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label>
                                                            <input type="radio" name="group-invite-status" value="mods">
                                                            <strong>Community admins and supports only</strong>
                                                        </label>
                                                    </li>
                                                    <li>
                                                        <label>
                                                            <input type="radio" name="group-invite-status" value="admins" checked="checked">
                                                            <strong>Community admins only</strong>
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg">Create Community</button>
                        </form>
                    </div>
                </div>


            </div>
        </div>

    </div>

    <?php include_once('parts/footer.php'); ?>
    <script>
        jQuery(document).ready(function($) {
            Page.communityCreate.init();
        });
    </script>

    </body>
</html>
