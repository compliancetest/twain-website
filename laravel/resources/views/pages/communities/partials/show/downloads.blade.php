<?php
$communityLicense = $community->getMeta('license_agreements');
?>
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
                <tr>
                    <td>
                        <a href="#">Contributions_1.0_SoapUI_Reference_Client_V2.5.xml </a>

                        <p>
                            Version: <strong>2.5</strong><br/>
                            SOAP UI Project file including Contributions 1.0 Conformance Level A test cases. Intended
                            for use as a reference to support testing of customer products.
                        </p>

                        <p>Pull requests now require the soap:role="ebms" attribute in line with the ebMS3
                            specification. The security header also needs a soap:mustUnderstand attribute.</p>
                    </td>
                    <td class="text-nowrap text-center">475.79 RB</td>
                    <td></td>
                    <td class="text-nowrap text-center">2014-08-04</td>
                    <td class="text-nowrap">
                        <a href="#" class="btn btn-icon btn-primary btn-edit" data-tooltip="tooltip" title="Edit"></a>
                        <a href="#" class="btn btn-icon btn-danger btn-delete" data-tooltip="tooltip"
                           title="Delete"></a>
                    </td>
                </tr>
                <tr style="display: none;">
                    <td colspan="5" class="empty-row ">No file uploaded yet</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="add-new-download-section">
        <div class="add-new-download-default">
            <a href="#add-new-download-section" id="add-new-download" class="add-new-download-link">Upload New
                File(s)</a>
        </div>
        <div id="add-new-download-section" style="display: none;">
            <div id="file-description-template" style="display: none;">
                <div class="file-description-section">
                    <div class="upload-file-field">
                        <input type="file" name="file[]" class="input-file"/>
                    </div>

                    <div class="file-description-fields">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">File Version:</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="file_version[]" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Description:</label>

                                <div class="col-sm-9">
                                    <textarea cols="20" rows="5" name="file_description[]"
                                              class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">File License Agreement:</label>

                                <div class="col-sm-9">
                                    <textarea cols="20" rows="5" name="file_license[]" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-danger btn-with-icon btn-delete">Remove</a>
                </div>
            </div>
            <form name="newfileform" id="newfileform" action="" enctype="multipart/form-data" method="post">
                <h3>Upload New File(s)</h3>

                <div class="file-description-section">
                    <div class="upload-file-field">
                        <input type="file" name="file[]" class="input-file"/>
                    </div>

                    <div class="file-description-fields">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">File Version:</label>

                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="file_version[]" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Description:</label>

                                <div class="col-sm-9">
                                    <textarea cols="20" rows="5" name="file_description[]"
                                              class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">File License Agreement:</label>

                                <div class="col-sm-9">
                                    <textarea cols="20" rows="5" name="file_license[]" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="btn btn-danger btn-with-icon btn-delete">Remove</a>
                </div>
                <div class="form-actions">
                    <a href="#" id="add-more-file" class="add-new-download-link">Add New File</a>

                    <div class="pull-right">
                        <a href="#" class="btn btn-success btn-with-icon btn-upload">Upload &amp; Save</a>
                        <a href="#" class="btn btn-default btn-with-icon btn-cancel"
                           id="cancel-add-new-files">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        Page.communityDownloads.init();
    });
</script>
