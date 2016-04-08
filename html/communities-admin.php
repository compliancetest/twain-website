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

                    <div class="community-tab-content">

                        <div class="community-admin">
                            <p>You must subscribe to at least one test suite in the community to access this content. To subscribe once you are a community member, just select the desired suite from the community home page, and click on the "Access" bar.</p>
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="colored-box">
                                        <div class="colored-box-header">Details</div>
                                        <div class="colored-box-body">
                                            <form role="main" enctype="multipart/form-data" method="post" action="ajax.php" id="group-details-form" name="group-details-form" data-validate="validate">
                                                <div class="colored-box-content">
                                                    <div class="form-group">
                                                        <label for="communityName">Community Name</label>
                                                        <input type="text" name="group-name" class="form-control" id="communityName" value="SuperStream" required data-msg-required="Community Name is required">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="communityDescription">Community Description</label>
                                                        <textarea name="group-desc" rows="5" class="form-control" data-air="true" id="communityDescription" required data-msg-required="Community Description is required">SuperStream aims to improve efficiency in the Australian Superannuation industry through data standards. "Rollovers", where savings are transferred from one fund to another (mandatory for APRA regulated funds by July 1 2013 and all funds by July 1 2014). "Member Contributions", where regular payments are made by employers to funds of the employees choice (mandated for large employers by 1 July 2014 and all employers by 1 July 2015.</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="communityTermsAndConditions">Terms and Conditions</label>
                                                        <textarea name="terms_and_conditions" rows="5" class="form-control redactor_editor" data-air="true" id="communityTermsAndConditions">
                                                            <p>These terms and conditions are specific to the SuperStream Community and supplement the general terms and conditions for registration on <a href="http://www.compliancetest.net">www.compliancetest.net</a> </p>
                                                            <ol>
                                                                <li>In order to access the SuperStream Community features, you must become a member of the community.</li><li>Membership of the SuperStream Community is limited to persons or organisations that have an active role in implementation (or implementation support) of the SuperStream standards (eg Funds, Administrators, Employers, Gateways, Software providers).</li><li>We reserve the right to terminate your membership at any time if you breach these terms and conditions.</li><li>You are responsible for all and any Content you contribute via the community forum or wiki.  When you provide Content you retain ownership of the intellectual property in that information however you grant all current and future community members unrestricted and royalty free rights to use your content for any purpose that supports the implementation of SuperStream stndards.   This licence ends when you cease your membership except for Content which has already been released as part of the Service.</li><li>We reserve the right but will not have an obligation to remove or refuse to distribute any Content.  We also reserve the right to adapt or modify your Content for any reason including for distribution purposes.</li><li>By posting Content on this website, you provide us with an undertaking that such Content does not infringe the rights of someone else and that it does not violate the law in any other way such as by being defamatory, being of racist content or is threatening.</li><li>To the extent permitted by law, you release and discharge us from any liability or claim arising out of any loss or damage that may be suffered or incurred as a result of your participation in the SuperStream community.</li><li>We respect your privacy. Our compliance with privacy legislation is set out in our separate Privacy Policy which may be accessed from our home page.</li></ol>
                                                        </textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="licenseAgreements">License Agreements</label>
                                                        <textarea name="license_agreements" rows="5" class="form-control redactor_editor" data-air="true" id="licenseAgreements">
                                                            <p>The software tools provided via the "Downloads" Tab on this community dashboard are provided for use by SuperStream community members and ComplianceTest service subscribers. &nbsp;</p><ol>
                                                            <li>The tools may be used by registered community members only.</li><li>There is no charge for use of the tools beyond the normal subscription fee for test suites.</li><li>The tools remain the copyright and intellectual property of Compliance Test.</li></ol>
                                                        </textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="communityObligationToClaim">Obligation to Claim</label>
                                                        <textarea name="obligation_for_claim" rows="5" class="form-control redactor_editor" data-air="true" id="communityObligationToClaim">
                                                            <p>By making this claim, I confirm that I have executed the SuperStream test cases using the product / version for which this compliance claim is made. I understand that this claim constitutes a "self-certification" to the effect that the product / version has completed testing and does not imply any warranty against any interoperability problems with the product. I understand that either the issuing authority or ComplianceTest may revoke the claim in the event that future problems indicate that tests may not have been properly completed. Once published, this claim entitles me to use the "ComplianceTested" Logo with the product for which this claim is made. The claim ID can be used as the certification reference on the ATO product register.</p>
                                                        </textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="communityNotificationEmailContent">Notification Email Content</label>
                                                        <textarea name="notification_email_of_changes" rows="5" class="form-control redactor_editor" data-air="true" id="communityNotificationEmailContent">There have been some changes to the SuperStream community content!</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Notify community members of changes via email</label><br>
                                                        <label>
                                                            <input type="radio" name="group-notify-members" value="1"> Yes
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="group-notify-members" value="0"> No
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="colored-box-footer">
                                                    <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                                                </div>
                                            </form>
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
                                                    <a href="#" class="btn btn-danger btn-with-icon btn-delete">Delete Image</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Details</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <p><span style="color: #ce1515;">WARNING</span>: Deleting this community will completely remove ALL content associated with it. There is no way back, please be careful with this option.</p>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="checkbox" value="1" id="delete-group-understand" name="delete-group-understand"> I understand the consequences of deleting this community.
                                                    </label>
                                                </div>
                                                <a href="#" class="btn btn-danger btn-with-icon btn-delete">Delete Community</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-sm-6">
                                    <div class="colored-box">
                                        <div class="colored-box-header">Profile Types</div>
                                        <div class="colored-box-body">
                                            <div id="profileTypeList">
                                                <div class="table-responsive">
                                                    <table class="colored-table profile-type-list">
                                                        <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Instances</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>TICK v1.1 </td>
                                                                <td>16</td>
                                                                <td class="text-nowrap">
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Product v4.1</td>
                                                                <td>2</td>
                                                                <td class="text-nowrap">
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Employer v2.5.1</td>
                                                                <td>6</td>
                                                                <td class="text-nowrap">
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Clearing House v2.3.1</td>
                                                                <td>164</td>
                                                                <td class="text-nowrap">
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Employer v2.11.1</td>
                                                                <td>1099</td>
                                                                <td class="text-nowrap">
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Product v5.6.1</td>
                                                                <td>291</td>
                                                                <td class="text-nowrap">
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Filter v1.1</td>
                                                                <td>18</td>
                                                                <td>
                                                                    <a href="#" class="btn btn-success btn-icon btn-download" data-tooltip="tooltip" title="Download Profile Type"></a>
                                                                    <a href="ajax.php" class="btn btn-primary btn-icon btn-edit" data-tooltip="tooltip" title="Edit Profile Type"></a>
                                                                    <a href="#" class="btn btn-danger btn-icon btn-delete" data-tooltip="tooltip" title="Remove Profile Type"></a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="colored-box-content">
                                                    <a href="#" class="btn btn-success btn-with-icon btn-add" id="addAddNewProfileType">Add New Profile Type</a>
                                                </div>
                                            </div>

                                            <div id="addNewProfile" style="display: none;">
                                                <form method="post" enctype="multipart/form-data" action="" id="profileTypeForm" name="profileTypeForm">
                                                    <div class="colored-box-content">
                                                        <div class="add-profile-title">Add New Profile Type</div>
                                                        <div class="form-group">
                                                            <label>Enter Schema:</label>
                                                            <textarea class="form-control" rows="20" id="profile_type_text" name="profile_type_text"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Or Select File:</label><br/>
                                                            <div class="upload-file-field">
                                                                <input type="file" name="profile_type_file" id="profile_type_file" class="input-file" data-file-type="doc" data-file-extensions="(.txt or .json file)" />
                                                            </div>
                                                        </div>

                                                        <input type="hidden" value="" name="community_id">
                                                        <input type="hidden" value="" id="type_id" name="type_id">
                                                        <input type="hidden" value="" name="td-action">
                                                    </div>
                                                    <div class="colored-box-footer">
                                                        <button type="submit" class="btn btn-success btn-with-icon btn-confirm">Save</button>
                                                        <a class="btn btn-default btn-with-icon btn-cancel" id="cancelAddingProfile" href="#">Cancel</a>
                                                    </div>
                                                </form>
                                            </div>

                                            <div id="profileTypesLoading" class="color-box-loading"><div class="loading-content"><span class="loader"></span><div class="loading-text">READING PROFILE TYPE</div><div class="loading-wait">Please wait...</div></div></div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Members</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content members-management">
                                                <form method="post" action="communities-admin.php" id="groupMembersForm" name="group-members-form">
                                                    <div class="pending-requests">
                                                        <p>There are no pending membership requests.</p>
                                                        <ul class="member-list" id="request-list">
                                                            <li>
                                                                <div class="pull-left">
                                                                    <img width="50" height="50" alt="" class="avatar" src="images/gravatar.jpg">
                                                                    <span class="member-info">
                                                                        <span class="member-name">Ivan Solowjew</span>
                                                                        <span class="member-email">ivansolowjew@gmail.com</span>
                                                                        <span class="member-activity">requested 17 seconds ago</span>
                                                                    </span>
                                                                </div>
                                                                <div class="pull-right action">
                                                                    <a class="btn btn-success btn-with-icon btn-confirm" href="#" data-tooltip="tooltip" title="Accept">Accept</a>
                                                                    <a class="btn btn-default btn-with-icon btn-cancel" href="#" data-tooltip="tooltip" title="Reject">Reject</a>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="members-group-action" id="membersGroupAction">
                                                        <ul>
                                                            <li><a data-action="ban" href="#">Kick &amp; Ban</a></li>
                                                            <li><a data-action="promote_to_mod" href="#">Promote to Support Staff</a></li>
                                                            <li><a data-action="promote_to_admin" href="#">Promote to Admin</a></li>
                                                            <li ><a data-action="remove_from_group" href="#">Remove</a></li>
                                                        </ul>
                                                    </div>

                                                    <div class="member-type-header">Administrator</div>
                                                    <ul class="row member-list" id="admins-list">
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Eric(Site Admin) So</span>
                                                                <span class="member-email">haohaneric+001@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Kseniya Shychko</span>
                                                                <span class="member-email">k.shychko@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Michael (Site Admin) a very long first name Leditschke</span>
                                                                <span class="member-email">michael.leditschke+site.admin@compliancetest.net</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">alena zaitsava</span>
                                                                <span class="member-email">alena.zaitsava@compliancetest.net</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Sergiu Vasilachi</span>
                                                                <span class="member-email">Sergiu@mail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Eric(Site Admin) So</span>
                                                                <span class="member-email">haohaneric+001@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Eric(Site Admin) So</span>
                                                                <span class="member-email">haohaneric+001@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                    </ul>

                                                    <div class="member-type-header">Support Staff</div>
                                                    <ul class="row member-list" id="mods-list">
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Support 1</span>
                                                                <span class="member-email">support@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Support 1</span>
                                                                <span class="member-email">support@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Support 1</span>
                                                                <span class="member-email">support@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Support 1</span>
                                                                <span class="member-email">support@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Support 1</span>
                                                                <span class="member-email">support@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Support 1</span>
                                                                <span class="member-email">support@gmail.com</span>
                                                                <a href="#" class="btn btn-success btn-sm">Demote to Member</a>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                    </ul>

                                                    <div class="member-type-header">Members</div>
                                                    <ul class="row member-list" id="mods-list">
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Member 1</span>
                                                                <span class="member-email">member@gmail.com</span>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Member 1</span>
                                                                <span class="member-email">member@gmail.com</span>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Member 1</span>
                                                                <span class="member-email">member@gmail.com</span>
                                                            </span>
                                                        </li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Member 1</span>
                                                                <span class="member-email">member@gmail.com</span>
                                                            </span>
                                                        </li>
                                                        <li class="clearfix hidden-sm"></li>
                                                        <li class="col-sm-6">
                                                            <label>
                                                                <input type="checkbox" value="1" name="id[]">
                                                                <img width="28" height="28" alt="" class="avatar" src="images/gravatar.jpg">
                                                            </label>
                                                            <span class="member-info">
                                                                <span class="member-name">Member 1</span>
                                                                <span class="member-email">member@gmail.com</span>
                                                            </span>
                                                        </li>
                                                    </ul>

                                                    <div class="pagination-wrapper">
                                                        <div class="pagination">
                                                            <a href="#" class="prev">prev</a>
                                                            <a href="#">1</a>
                                                            <span class="current">2</span>
                                                            <a href="#">3</a>
                                                            <span class="pager-dots">...</span>
                                                            <a href="#">22</a>
                                                            <a href="#" class="next">next</a>
                                                        </div>
                                                    </div>

                                                    <input type="hidden" id="action" name="action" value="" />
                                                </form>
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
                                            <div class="colored-box-footer">
                                                <a href="#" class="btn btn-success btn-with-icon btn-confirm">Save</a>
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
                                        <div class="colored-box-footer">
                                            <a href="#" class="btn btn-success btn-with-icon btn-confirm">Save</a>
                                        </div>
                                    </div>

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
                                        <div class="colored-box-footer">
                                            <a href="#" class="btn btn-success btn-with-icon btn-confirm">Save</a>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Generate JSON</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <div class="upload-file-field">
                                                    <input type="file" name="file[]" class="input-file" data-file-type="image" data-file-extensions="(.xls, .xlsx file)" />
                                                </div>
                                                <a href="#" class="btn btn-success btn-with-icon btn-confirm">Generate JSON</a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="colored-box">
                                        <div class="colored-box-header">Generate FVS</div>
                                        <div class="colored-box-body">
                                            <div class="colored-box-content">
                                                <div class="upload-file-field">
                                                    <input type="file" name="file[]" class="input-file" data-file-type="image" data-file-extensions="(.xls, .xlsx file)" />
                                                </div>
                                                <a href="#" class="btn btn-success btn-with-icon btn-upload">Upload</a>
                                                <ul class="loaded-files">
                                                    <li>SPR_28135_consolidated.xls</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="colored-box-footer">
                                            <a href="#" class="btn btn-success btn-with-icon btn-confirm">Generate FVS</a>
                                        </div>
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
    <script src="js/vendor/redactor.js"></script>
    <script>
        jQuery(document).ready(function($) {
            Page.communityAdmin.init();
        });
    </script>

    </body>
</html>
