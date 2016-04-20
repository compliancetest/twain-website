<?php
/*
 * Template Name: Add/Edit Test Suite
 */

$suiteID = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;

if (($suiteID != null && !can_edit_suite($suiteID)) || ($suiteID == null && !can_create_suite())) {
    if (!$suiteID)
        addMessage('Sorry, you are not allowed to create a Test Suite. Only community admin can create new one.', 'error');
    else
        addMessage('Sorry, you are not allowed to edit the Test Suite', 'error');
    wp_redirect("/");
    exit;
}

$suite = new TestSuite($suiteID);
if ($suiteID)
    $suite->load();

get_header();
$groups = getUserAdminGroups(get_current_user_id());

if ($suite->id) {
    $newMajorVersionExist = isNewSuiteVersionExist($suite->familyMark, $suite->version_major);
    $newMinorVersionExist = isNewSuiteVersionExist($suite->familyMark, $suite->version_major, $suite->version_minor);
    $newPatchVersionExist = isNewSuiteVersionExist($suite->familyMark, $suite->version_major, $suite->version_minor, $suite->version_patch);
} else {
    $newMajorVersionExist = false;
    $newMinorVersionExist = false;
    $newPatchVersionExist = false;
}

if (!$suite->community_id)
    $suite->community_id = isset($_GET['community_id']) ? htmlspecialchars($_GET['community_id']) : $groups[0]->id;

$xeroItems = ct_get_xero_items();
?>
<div class="content edit-item-wrapper" id="edit_test_suite_wrapper">
    <div class="column container relative">
        <form name="suiteForm" id="suiteForm" action="" method="post" enctype="multipart/form-data">
            <?php if (!$suite->id) { ?>
                <h2>Add New Test Suite</h2>
            <?php } else { ?>
                <h2>Edit Test Suite: <?php echo $suite->name ?></h2>
            <?php } ?>
            <div class="grid-box grid-box-expandable grid-box-opened" id="community-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Choose Community</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell">
                                <select name="community_id" id="community_id" class="select">
                                    <!--<option></option>-->
                                    <?php foreach ($groups as $row) { ?>
                                        <option
                                            value="<?php echo $row->id ?>" <?php echo cp_selected($row->id, $suite->community_id) ?>><?php echo $row->title; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="suite-type-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Test Suite Types</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell checkbox-cell">
                                <?php
                                $suiteTypes = $suite->getAllTestSuiteTypes();

                                foreach ($suiteTypes as $row) { ?>
                                    <label><input type="checkbox" name="test_suite_type[]"
                                                  value="<?php echo $row->term_id ?>" <?php echo isset($suite->type[$row->term_id]) ? 'checked="checked"' : '' ?>> <?php echo $row->name ?>
                                    </label>
                                <?php } ?>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <div class="space25"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="suite-info-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Test Suite Information</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell">
                                <label for="ts_name">Title: </label>
                                <input type="text" id="ts_name" name="ts_name" class="input required half-width"
                                       value="<?php echo $suite->name ?>"/>
                            </div>
                            <div class="grid-cell">
                                <label for="ts_identifier">Name: </label>
                                <input type="text" id="ts_identifier" name="ts_identifier" class="input half-width"
                                       value="<?php echo $suite->identifier ?>" onchange="getAvailableTemplates()"/>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <div class="grid-cell">
                                <label for="ts_issue_date">Published: </label>
                                <input type="text" id="ts_issue_date" name="ts_issue_date" class="input datepicker"
                                       value="<?php echo formatDate($suite->issueDate) ?>"/>
                            </div>
                            <div class="grid-cell">
                                <label for="ts_name">Issuer: </label>
                                <input type="text" id="ts_issuer" name="ts_issuer" class="input"
                                       value="<?php echo $suite->issuer ?>"/>
                            </div>
                            <div class="grid-cell">
                                <label for="ts_issue_date">Revision Description: </label>
                                <input type="text" id="ts_revision_description" name="ts_revision_description"
                                       class="input" value="<?php echo $suite->revisionDescription ?>"/>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <div class="grid-cell status-cell">
                                <label>Status: </label>
                                <input type="radio" name="ts_status" id="ts_status_draft"
                                       value="Draft" <?php echo $suite->status == 'Draft' ? 'checked="checked"' : '' ?> />
                                <span class="label"><span
                                        class="status_btn status_circle status_draft">D</span> Draft</span>
                                <input type="radio" name="ts_status" id="ts_status_active"
                                       value="Active" <?php echo $suite->status == 'Active' ? 'checked="checked"' : '' ?> />
                                <span class="label"><span class="status_btn status_circle status_active">A</span> Active</span>
                                <input type="radio" name="ts_status" id="ts_status_deprecated"
                                       value="Deprecated" <?php echo $suite->status == 'Deprecated' ? 'checked="checked"' : '' ?> />
                                <span class="label"><span class="status_btn status_circle status_deprecated">C</span> Deprecated</span><br/>
                                <input type="radio" name="ts_status" id="ts_status_obsolete"
                                       value="Obsolete" <?php echo $suite->status == 'Obsolete' ? 'checked="checked"' : '' ?> />
                                <span class="label"><span class="status_btn status_circle status_obsolete">O</span> Obsolete</span>
                                <input type="radio" name="ts_status" id="ts_status_partial"
                                       value="Partial" <?php echo $suite->status == 'Partial' ? 'checked="checked"' : '' ?> />
                                <span class="label"><span class="status_btn status_circle status_partial">P</span> Partial</span>
                            </div>
                            <div class="grid-cell version-cell">
                                <label for="ts_name">Version: </label>
                           <span>
                               <b>Major</b> <input type="text" id="ts_version_major" name="ts_version_major"
                                                   class="input input-readonly" readonly="readonly"
                                                   value="<?php echo $suite->version_major ?>"
                                                   data-default="<?php echo $suite->version_major ?>"/>
                               <a href="#"
                                  class="action-btn icon-btn blue-plus-btn <?php if ($newMajorVersionExist) { ?>disabled-btn has-tooltip<?php } ?>"><span
                                       class="p"></span>
                                   <?php if ($newMajorVersionExist) { ?><span class="simple_tooltip"><span></span>Later version already exists.</span><?php } ?>
                               </a>
                           </span>
                           <span>
                               <b>Minor</b> <input type="text" id="ts_version_minor" name="ts_version_minor"
                                                   class="input input-readonly" readonly="readonly"
                                                   value="<?php echo $suite->version_minor ?>"
                                                   data-default="<?php echo $suite->version_minor ?>"/>
                               <a href="#"
                                  class="action-btn icon-btn blue-plus-btn <?php if ($newMinorVersionExist) { ?>disabled-btn has-tooltip<?php } ?>"><span
                                       class="p"></span>
                                   <?php if ($newMinorVersionExist) { ?><span class="simple_tooltip"><span></span>Later version already exists.</span><?php } ?>
                               </a>
                           </span>
                           <span>
                               <b>Patch</b> <input type="text" id="ts_version_patch" name="ts_version_patch"
                                                   class="input input-readonly" readonly="readonly"
                                                   value="<?php echo $suite->version_patch ?>"
                                                   data-default="<?php echo $suite->version_patch ?>"/>
                               <a href="#"
                                  class="action-btn icon-btn blue-plus-btn <?php if ($newPatchVersionExist) { ?>disabled-btn has-tooltip<?php } ?>"><span
                                       class="p"></span>
                                   <?php if ($newPatchVersionExist) { ?><span class="simple_tooltip"><span></span>Later version already exists.</span><?php } ?>
                               </a>
                           </span>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="field-row">
                            <div class="grid-cell width95P">
                                <label for="ts_identifier">Description: </label>
                                <?php //wp_editor($suite->description, 'ts_description', array('textarea_name' => 'ts_description', 'media_buttons' => false)); ?>
                                <textarea cols="" rows="" class="textarea large-textarea" name="ts_description"
                                          id="ts_description"><?php echo $suite->description ?></textarea>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Operational Triplet</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell">
                                <textarea cols="" rows="" class="textarea" name="init_message"
                                          id="init_message"><?php echo $suite->initiatingMessage ?></textarea>
                            </div>
                            <div class="grid-cell">
                                <label class="light-desc"><i>Type Operational Triplet (comma separated)</i></label>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Pricing Plans</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <?php
                        $pricing_plans = PricingPlan::getAllPlans();
                        foreach ($pricing_plans AS $plan) { ?>
                            <div class="field-row">
                                <div class="grid-cell checkbox-cell width70P padding20-10">
                                    <label><input type="checkbox" name="test_suite_plans[]"
                                                  value="<?php echo $plan->id ?>" <?php echo in_array($plan->id, $suite->test_suite_plans) ? 'checked="checked"' : '' ?>> <?php echo $plan->title ?>
                                    </label>
                                </div>
                                <div class="grid-cell width8P tocenter">
                                    <label>Order:</label>
                                    <input type="text" class="input width70P tocenter"
                                           name="pricing_plans_sequence_<?php echo $plan->id; ?>"
                                           value="<?php echo isset($suite->test_suite_plans_order[$plan->id]) ? $suite->test_suite_plans_order[$plan->id] : '0'; ?>"/>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="conf-level-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Conformance Levels</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell">
                                <label>Conformance Level Code:</label>
                                <input type="text" class="input" name="lvl_code[]"
                                       value="<?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE ?>"
                                       readonly="readonly"/>
                            </div>
                            <div class="grid-cell">
                                <label>Conformance Level Description:</label>
                                <textarea cols="" rows="" class="textarea" name="lvl_desc[]"
                                          readonly="readonly"><?php echo TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_DESCRIPTION ?></textarea>
                            </div>
                            <div class="grid-cell">
                                <label>&nbsp;</label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <?php foreach ($suite->conformanceLevel as $row) { ?>
                            <?php
                            if ($row['code'] == TEST_SUITE_DEFAULT_CONFORMANCE_LEVEL_CODE)
                                continue;
                            ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Conformance Level Code:</label>
                                    <input type="text" class="input" name="lvl_code[]"
                                           value="<?php echo $row['code'] ?>"/>
                                </div>
                                <div class="grid-cell">
                                    <label>Conformance Level Description:</label>
                                    <textarea cols="" rows="" class="textarea"
                                              name="lvl_desc[]"><?php echo $row['desc'] ?></textarea>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <?php if (!$suite->id) { ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Conformance Level Code:</label>
                                    <input type="text" class="input" name="lvl_code[]" value=""/>
                                </div>
                                <div class="grid-cell">
                                    <label>Conformance Level Description:</label>
                                    <textarea cols="" rows="" class="textarea" name="lvl_desc[]"></textarea>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <div class="btn-row">
                            <div class="grid-cell">
                                <a href="#" class="action-btn add-new-btn" id="add-conformance-level"><span
                                        class="p"></span><span class="t">New Conformance Level</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>

                </div>
            </div>
            <?php
            $lastScenarioID = 1;
            ?>
            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="scenarios-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Scenarios</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell width22P">
                                <label>Code:</label>
                                <?php if (!$suite->scenarios): ?>
                                    <input type="hidden" name="scenario_id[]" value=""/>
                                <?php else: ?>
                                    <?php
                                    foreach ($suite->scenarios as $row) {
                                        if ($row['code'] == TEST_SUITE_DEFAULT_SCENARIO_CODE) {
                                            echo '<input type="hidden" name="scenario_id[]" value="' . $row['id'] . '" />';
                                            break;
                                        }
                                    }
                                    ?>
                                <?php endif; ?>
                                <input type="text" class="input width98P" name="scenario_code[]"
                                       value="<?php echo TEST_SUITE_DEFAULT_SCENARIO_CODE ?>" readonly="readonly"/>
                            </div>
                            <div class="grid-cell width55P">
                                <label>Description:</label>
                                <textarea cols="" rows="" class="textarea width98P default-scenario"
                                          name="scenario_desc[]"
                                          readonly="readonly"><?php echo TEST_SUITE_DEFAULT_SCENARIO_DESCRIPTION ?></textarea>
                            </div>
                            <div class="grid-cell width8P tocenter">
                                <label>Sequence:</label>
                                <input type="text" class="input width70P tocenter" name="scenario_sequence[]"
                                       value="999" readonly="readonly"/>
                            </div>
                            <div class="grid-cell">
                                <label>&nbsp;</label>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <?php foreach ($suite->scenarios as $row) { ?>
                            <?php
                            if ($row['code'] == TEST_SUITE_DEFAULT_SCENARIO_CODE)
                                continue;

                            $lastScenarioID = max($row['id'], $lastScenarioID);
                            ?>
                            <div class="field-row">
                                <div class="grid-cell width22P">
                                    <label>Code:</label>
                                    <input type="hidden" name="scenario_id[]" value="<?php echo $row['id'] ?>"/>
                                    <input type="text" class="input width98P" name="scenario_code[]"
                                           value="<?php echo $row['code'] ?>"/>
                                </div>
                                <div class="grid-cell width55P">
                                    <label>Description:</label>
                                    <?php //wp_editor($row['description'], 'scenario_desc' . $row['id'], array('textarea_name' => 'scenario_desc[]', 'media_buttons' => false, 'editor_height' => 300)); ?>
                                    <textarea cols="" rows="" class="textarea width98P"
                                              name="scenario_desc[]"><?php echo $row['description'] ?></textarea>
                                </div>
                                <div class="grid-cell width8P tocenter">
                                    <label>Sequence:</label>
                                    <input type="text" class="input width70P tocenter" name="scenario_sequence[]"
                                           value="<?php echo $row['sequence'] ?>"/>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <div class="btn-row">
                            <div class="grid-cell">
                                <a href="#" class="action-btn add-new-btn" id="add-scenario"><span
                                        class="p"></span><span class="t">New Scenario</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="test-data-profiles-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Test Data</h5>

                    <div class="clear"></div>
                </div>
                <?php
                $availableTemplates = array();//$suite->getAvailableTemplates();
                ?>
                <select class="select medium-input availableTemplates" style="display: none;" id="availableTemplates">
                    <option value="">Select a Template</option>
                    <?php foreach ($availableTemplates as $t): ?>
                        <option value="<?php echo $t ?>"><?php echo $t ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="grid-box-body">
                    <div class="column">
                        <div id="suite-template-data">
                            <h6><b>Message Templates</b></h6>
                            <?php foreach ($suite->messageTemplates as $row) { ?>
                                <div class="field-row">
                                    <div class="grid-cell">
                                        <label>Template Title:</label>
                                        <input type="text" name="message_template_name[]"
                                               value="<?php echo $row['name'] ?>" class="input"/>
                                    </div>
                                    <div class="grid-cell">
                                        <label>Template URI:</label>
                                        <select name="message_template_url[]"
                                                class="select medium-input availableTemplates"
                                                data-default='<?php echo $row['url'] ?>'>
                                            <?php foreach ($availableTemplates as $t): ?>
                                                <option
                                                    value="<?php echo $t ?>" <?php echo $t == $row['url'] ? 'selected="selected"' : '' ?>><?php echo $t ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="grid-cell">
                                        <label>&nbsp;</label>
                                        <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            <?php } ?>
                            <?php if (!$suite->id) { ?>
                                <div class="field-row">
                                    <div class="grid-cell">
                                        <label>Template Name:</label>
                                        <input type="text" name="message_template_name[]" value="" class="input"/>
                                    </div>
                                    <div class="grid-cell">
                                        <label>Template URI:</label>
                                        <select name="message_template_url[]"
                                                class="select medium-input availableTemplates">
                                            <option value="">Select a Template</option>
                                            <?php foreach ($availableTemplates as $t): ?>
                                                <option value="<?php echo $t ?>"><?php echo $t ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="grid-cell">
                                        <label>&nbsp;</label>
                                        <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            <?php } ?>
                            <div class="btn-row">
                                <div class="grid-cell">
                                    <a href="#" class="action-btn add-new-btn" id="add-message-template"><span
                                            class="p"></span><span class="t">New Template</span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                        <h6><b>Profile Type</b></h6>

                        <div class="field-row noborder" id="suite_profile_types">
                            <?php
                            $profileTypes = getCommunityProfileTypes($suite->community_id);
                            foreach ($profileTypes as $row) { ?>

                                <div class="grid-cell width50P nopadding">
                                    <input type="checkbox" class="checkbox-input" name="ts_profile_types[]"
                                           value="<?php echo $row->id ?>" <?php echo in_array($row->id, $suite->profileTypes) ? 'checked="checked"' : "" ?> />
                                    <a href="<?php echo get_site_url() ?>?td-action=<?php echo wp_create_nonce('view-profile-type') ?>&id=<?php echo $row->id ?>"
                                       rel="custom-popup" cp-type="ajax">
                                        <?php echo $row->title ?>
                                        <?php
                                        $pJSON = json_decode(base64_decode($row->schema));
                                        if ($pJSON->Version) {
                                            $version = array();
                                            foreach (get_object_vars($pJSON->Version) as $k => $v) {
                                                $version[] = $v;
                                            }
                                            echo " v" . implode(".", $version);
                                        }
                                        ?>

                                    </a>
                                </div>
                            <?php } ?>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <?php if ($suite->id) { ?>
                <div class="grid-box grid-box-expandable grid-box-opened" id="test-cases-box">
                    <div class="grid-box-header">
                        <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                        <h5 class="left">Test Cases</h5>

                        <div class="clear"></div>
                    </div>
                    <div class="grid-box-body">
                        <div class="column">
                            <div class="field-row">
                                <?php foreach ($suite->testCases as $row) { ?>
                                    <div class="grid-cell">
                                        <a href="<?php echo get_permalink($row->ID) ?>"
                                           class="test-case-link"><?php echo get_the_title($row->ID) ?></a>
                                        <a href="#" class="action-btn blue-delete-btn icon-btn"
                                           data-id="<?php echo $row->ID ?>"
                                           data-action="<?php echo wp_create_nonce('hide_testcase') ?>"><span
                                                class="p"></span></a>
                                    </div>
                                <?php } ?>

                                <a href="/add-new-test-case?suite_id=<?php echo $suite->id ?>" target="_blank"
                                   class="action-btn add-new-btn"><span class="p"></span><span
                                        class="t">New Test Case</span></a>

                                <div class="clear"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="space20"></div>
            <?php } ?>
            <?php
            $availableSuites = $suite->getBrotherSuites();

            ?>
            <div class="grid-box grid-box-expandable grid-box-opened" id="related-suites-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Related Test Suites</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <?php foreach ($suite->relatedSuites as $crow) { ?>
                            <div class="field-row">
                                <div class="grid-cell width30P">
                                    <label>Related Suites:</label>

                                    <div class="styled_select">
                                        <select name="ts[]" class="select">
                                            <option>- Select -</option>
                                            <?php foreach ($availableSuites as $row) {
                                                echo '<option value="' . $row->ID . '" ' . ($crow['id'] == $row->ID ? ' selected="selected"' : '') . '>' . $row->post_title . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid-cell width60P">
                                    <label>Description:</label>
                                    <textarea cols="" rows="" class="textarea width98P"
                                              name="ts_desc[]"><?php echo $crow['desc'] ?></textarea>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <?php if (!$suite->id) { ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Related Suites:</label>

                                    <div class="styled_select">
                                        <select name="ts[]" class="select">
                                            <option>- Select -</option>
                                            <?php foreach ($availableSuites as $row) {
                                                echo '<option value="' . $row->ID . '">' . $row->post_title . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid-cell">
                                    <label>Description:</label>
                                    <textarea cols="" rows="" class="textarea" name="ts_desc[]"></textarea>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <div style="display: none" id="brother-suites">
                            <select name="ts[]" class="select">
                                <option>- Select -</option>
                                <?php foreach ($availableSuites as $row) {
                                    echo '<option value="' . $row->ID . '">' . $row->post_title . '</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="btn-row">
                            <div class="grid-cell">
                                <a href="#" class="action-btn add-new-btn" id="add-related-suite"><span
                                        class="p"></span><span class="t">New Test Suite</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="roles-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Roles</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <?php
                        foreach ($suite->roles as $row) { ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Name:</label>
                                    <input type="text" class="input_roles" name="role_names[]"
                                           value="<?php echo $row['name'] ?>"/>
                                </div>
                                <div class="grid-cell">
                                    <label>Description:</label>
                                    <input type="text" class="input_roles" name="role_descs[]"
                                           value="<?php echo $row['desc'] ?>"/>
                                </div>
                                <div class="grid-cell">
                                    <label>Profile types:</label>
                                    <input type="text" class="input_roles" name="role_types[]"
                                           value="<?php echo $row['profileTypes'] ?>"/>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <?php if (!$suite->id) { ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Name:</label>
                                    <input type="text" class="input_roles" name="role_names[]" value=""/>
                                </div>
                                <div class="grid-cell">
                                    <label>Description:</label>
                                    <input type="text" class="input_roles" name="role_descs[]" value=""/>
                                </div>
                                <div class="grid-cell">
                                    <label>Profile types:</label>
                                    <input type="text" class="input_roles" name="role_types[]" value=""/>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <div class="btn-row">
                            <div class="grid-cell">
                                <a href="#" class="action-btn add-new-btn" id="add-new-role"><span
                                        class="p"></span><span class="t">New Role</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="specs-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Specification Documents</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <?php foreach ($suite->specDocuments as $row) { ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Document Name:</label>
                                    <input type="text" class="input" name="doc_name[]"
                                           value="<?php echo $row->doc_name ?>"/>
                                </div>
                                <div class="grid-cell width60P">
                                    <label>Document Description:</label>
                                    <textarea cols="" rows="" name="doc_desc[]"
                                              class="textarea"><?php echo $row->doc_desc ?></textarea>
                                    <label>Document Location:</label>
                                    <input type="text" class="input medium-input" name="doc_loc[]"
                                           value="<?php echo $row->doc_loc_url ?>"/>
                                    <label>Or Upload Document:</label>
                                    <input type="file" class="input" name="doc_file[]" value=""/>

                                    <div class="clear"></div>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <?php if (!$suite->id) { ?>
                            <div class="field-row">
                                <div class="grid-cell">
                                    <label>Document Name:</label>
                                    <input type="text" class="input" name="doc_name[]" value=""/>
                                </div>
                                <div class="grid-cell width60P">
                                    <label>Document Description:</label>
                                    <textarea cols="" rows="" name="doc_desc[]" class="textarea"></textarea>
                                    <label>Document Location:</label>
                                    <input type="text" class="input medium-input" name="doc_loc[]" value=""/>
                                    <label>Or Upload Document:</label>
                                    <input type="file" class="input" name="doc_file[]" value=""/>
                                </div>
                                <div class="grid-cell">
                                    <label>&nbsp;</label>
                                    <a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>
                                </div>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                        <div class="btn-row">
                            <div class="grid-cell">
                                <a href="#" class="action-btn add-new-btn" id="add-spec-doc"><span
                                        class="p"></span><span class="t">New Document</span></a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="space20"></div>
            <div class="grid-box grid-box-expandable grid-box-opened" id="excerpt-box">
                <div class="grid-box-header">
                    <a class="gbh-btn gbh-btn-expandable left" href="javascript: void(0);">Ex</a>
                    <h5 class="left">Excerpt</h5>

                    <div class="clear"></div>
                </div>
                <div class="grid-box-body">
                    <div class="column">
                        <div class="field-row">
                            <div class="grid-cell">
                                <textarea cols="" rows="" name="excerpt"
                                          class="textarea"><?php echo $suite->excerpt ?></textarea>
                            </div>
                            <div class="grid-cell width255">
                                <label class="light-desc">
                                    <i>Excerpts are optional hand-crafted summaries of your content that can be used in
                                        your theme.</i>
                                </label>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid-box">
                <div class="grid-box-footer nobackground noshadow">
                    <div class="btn-row nopaddingright nopaddingleft">
                        <?php if ($suite->id) { ?>
                            <div class="left"><label><input type="checkbox" name="send-notification"
                                                            id="send-notification" value="1" autocomplete="off"/> Send
                                    Notification to members</label></div>
                        <?php } ?>
                        <a href="#" class="action-btn process-btn submit-btn left15"><span class="p"></span><span
                                class="t">SAVE TEST SUITE</span></a>
                        <a href="<?php echo $suite->id ? get_permalink($suite->id) : cp_get_group_permalink_by_id($suite->community_id) ?>"
                           class="action-btn cancel-btn"><span class="p"></span><span class="t">Cancel</span></a>

                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $suite->id ?>"/>
            <input type="hidden" name="action" value="cp-suite-save"/>
            <?php
            wp_nonce_field('save-suite');
            ?>
            <div class="loading loading-with-text" id="saving-wrapper">
                <div><b>SAVING YOUR DATA</b><span>Please wait...</span></div>
            </div>
        </form>
    </div>
    <div class="clear space25"></div>

</div> <!--end content-->

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        //Add Loading Div
        jQuery('#edit_test_suite_wrapper .grid-box-body').append('<div class="loading1"></div>');
        jQuery('#add-conformance-level').click(function () {
            jQuery('#conf-level-box .btn-row').before('<div class="field-row">' +
                '<div class="grid-cell">' +
                '<label>Conformance Level Code:</label>' +
                '<input type="text" class="input" name="lvl_code[]" value="" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>Conformance Level Description:</label>' +
                '<textarea cols="" rows="" class="textarea" name="lvl_desc[]"></textarea>' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>&nbsp;</label>' +
                '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>' +
                '<div class="clear"></div>' +
                '</div>');
            return false;
        });
        jQuery('#add-related-suite').click(function () {
            jQuery('#related-suites-box .btn-row').before('<div class="field-row">' +
                '<div class="grid-cell width30P">' +
                '<label>Related Suites:</label>' +
                '<div class="styled_select">' +
                jQuery('#brother-suites').html() +
                '</div>' +
                '</div>' +
                '<div class="grid-cell width60P">' +
                '<label>Description:</label>' +
                '<textarea cols="" rows="" class="textarea width98P" name="ts_desc[]"></textarea>' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>&nbsp;</label>' +
                '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>' +
                '<div class="clear"></div>' +
                '</div>');
            jQuery('#related-suites-box .btn-row').prev().find('textarea').redactor({air: true, minHeight: 80});
            return false;
        })
        jQuery('#add-new-role').click(function () {
            jQuery('#roles-box .btn-row').before('<div class="field-row">' +
                '<div class="grid-cell">' +
                '<label>Name:</label>' +
                '<input type="text" class="input_roles" name="role_names[]" value="" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>Description:</label>' +
                '<input type="text" class="input_roles" name="role_descs[]" value="" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>Profile types:</label>' +
                '<input type="text" class="input_roles" name="role_types[]" value="" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>&nbsp;</label>' +
                '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>' +
                '<div class="clear"></div>' +
                '</div>');
            return false;
        })
        jQuery('#add-spec-doc').click(function () {
            jQuery('#specs-box .btn-row').before('<div class="field-row">' +
                '<div class="grid-cell">' +
                '<label>Document Name:</label>' +
                '<input type="text" class="input" name="doc_name[]" value="" />' +
                '</div>' +
                '<div class="grid-cell width60P">' +
                '<label>Document Description:</label>' +
                '<textarea cols="" rows="" name="doc_desc[]" class="textarea"></textarea>' +
                '<label>Document Location:</label>' +
                '<input type="text" class="input medium-input" name="doc_loc[]" value="" />' +
                '<label>Or Upload Document:</label>' +
                '<input type="file" class="input" name="doc_file[]" value="" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>&nbsp;</label>' +
                '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>' +
                '<div class="clear"></div>' +
                '</div>');
            customizeFileTag();
            jQuery('#specs-box .btn-row').prev().find('textarea').redactor({air: true, minHeight: 80});
            return false;
        });

        jQuery('#add-message-template').click(function () {
            jQuery('#suite-template-data .btn-row').before('<div class="field-row">' +
                '<div class="grid-cell">' +
                '<label>Template Title:</label>' +
                '<input type="text" name="message_template_name[]" value="" class="input" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>Template URI:</label>' +
                '<select name="message_template_url[]" class="select medium-input availableTemplates">' +
                $('#availableTemplates').html() +
                '</select>' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>&nbsp;</label>' +
                '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>' +
                '<div class="clear"></div>' +
                '</div>');
            return false;
        })

        //Getting Last ID
        var lastScenarioID = parseInt('<?php echo $lastTypeID?>');
        var lastScenariosSequence;
        jQuery('#add-scenario').click(function () {
            lastScenarioID += 10;
            jQuery('#scenarios-box .btn-row').before('<div class="field-row added-field-row">' +
                '<div class="grid-cell width22P">' +
                '<label>Code:</label>' +
                '<input type="hidden" name="scenario_id[]" value="" />' +
                '<input type="text" class="input width98P" name="scenario_code[]" value="" />' +
                '</div>' +
                '<div class="grid-cell width55P">' +
                '<label>Description:</label>' +
                '<textarea cols="" rows="" class="textarea width98P" name="scenario_desc[]" id="scenario_desc' + lastScenarioID + '"></textarea>' +
                '</div>' +
                '<div class="grid-cell width8P tocenter">' +
                '<label>Sequence:</label>' +
                '<input type="text" class="input width70P tocenter" name="scenario_sequence[]" value="' + (parseInt(jQuery('#scenarios-box .field-row').last().find('input[name="scenario_sequence[]"]').val()) + 1) + '" />' +
                '</div>' +
                '<div class="grid-cell">' +
                '<label>&nbsp;</label>' +
                '<a href="#" class="action-btn blue-delete-btn icon-btn"><span class="p"></span></a>' +
                '</div>' +
                '<div class="clear"></div>' +
                '</div>');
            jQuery('#scenarios-box .btn-row').prev().find('textarea').redactor({
                air: true
            });

//            sortScenarios();

            return false;
        });

        jQuery('#scenarios-box').on('click', '.blue-delete-btn', function () {
            jQuery(this).parents('.field-row').fadeOut('fast', function () {
                jQuery(this).remove();
//                sortScenarios();
            });
            return false;
        });

        function sortScenarios() {
            jQuery('#scenarios-box .field-row.added-field-row:gt(0)').each(function (idx) {
                jQuery(this).find('input[name="scenario_sequence[]"]').val(idx + 1);
            })
        }

        //Delete
        jQuery('#conf-level-box, #related-suites-box, #roles-box, #specs-box, #test-data-profiles-box').on('click', '.blue-delete-btn', function () {
            jQuery(this).parents('.field-row').fadeOut('fast', function () {
                jQuery(this).remove();
            });
            return false;
        });

        jQuery('#test-cases-box .blue-delete-btn').click(function () {
            var the_id = jQuery(this).attr('data-id');
            var the_action = jQuery(this).attr('data-action');
            var get_parent = jQuery(this);
            var link = jQuery(this);
            var field_tc = {
                suite_id: '<?php echo $suite->id?>',
                case_id: the_id,
                '_wpnonce': the_action
            };
            jQuery('#test-cases-box .loading1').show();
            jQuery('#test-cases-box .message').remove();
            jQuery.ajax({
                url: window.location.href,
                data: field_tc,
                type: 'POST',
                success: function (data) {
                    if (data == 'success') {
                        link.parents('.grid-cell').fadeOut('fast', function () {
                            link.remove();
                        })
                    } else {
                        jQuery('#test-case-box .column').append('<div class="message error">' + data + '</div>');
                    }
                    jQuery('#test-cases-box .loading1').hide();
                },
                error: function (data) {
                    jQuery('#test-cases-box .loading1').hide();
                }
            });

            return false;
        })

        //Update Related Test Suites 
        jQuery('#community_id').change(function () {
            jQuery('#community-box .loading1, #related-suites-box .loading1, #test-data-profiles-box .loading1').show();
            jQuery.ajax({
                url: '<?php echo get_site_url()?>',
                data: {
                    'community_id': jQuery(this).val(),
                    'id': '<?php echo $suite->id ?>',
                    '_wpnonce': '<?php echo wp_create_nonce('get-brother-suites-and-profile-types')?>'
                },
                type: 'POST',
                dataType: 'xml',
                complete: function () {
                    jQuery('#community-box .loading1, #related-suites-box .loading1, #test-data-profiles-box .loading1').hide();
                },
                success: function (rsp) {
                    jQuery('#related-suites-box select').replaceWith(jQuery(rsp).find('suites').text());
                    jQuery('#suite_profile_types').html(jQuery(rsp).find('profileTypes').text());
                    jQuery("#suite_profile_types a[rel='custom-popup']").cplightbox();
                }
            })
        })

        jQuery('#community-box .grid-box-body, #suite-type-box .grid-box-body').height(Math.max(jQuery('#community-box .grid-box-body').height(), jQuery('#suite-type-box .grid-box-body').height()));
        //Manage Version
        jQuery('.version-cell .action-btn').click(function () {
            if (jQuery(this).hasClass('disabled-btn'))
                return false;
            var prev = jQuery(this).prev();
            if (!prev.val())
                prev.val(0);
            prev.val(parseInt(prev.val()) + 1);
            if (prev.attr('id') == 'ts_version_major') {
                jQuery('#ts_version_minor').val(0);
                jQuery('#ts_version_patch').val(0);
                getAvailableTemplates();
            } else if (prev.attr('id') == 'ts_version_minor') {
                jQuery('#ts_version_patch').val(0);
            }
            jQuery(this).before('<a href="#" class="version-cancel has-tooltip"><span class="simple_tooltip"><span></span>Undo</span></a>');
            jQuery('.version-cell .action-btn').hide();
            return false;
        })

        jQuery('.version-cell').on('click', '.version-cancel', function () {
            var majorUpdated = false;
            if (jQuery('#ts_version_major').val() != jQuery('#ts_version_major').attr('data-default'))
                majorUpdated = true;

            jQuery('.version-cell .version-updated, .version-cell .version-cancel').remove();
            jQuery('.version-cell .action-btn').show();
            jQuery('#ts_version_major').val(jQuery('#ts_version_major').attr('data-default'));
            jQuery('#ts_version_minor').val(jQuery('#ts_version_minor').attr('data-default'));
            jQuery('#ts_version_patch').val(jQuery('#ts_version_patch').attr('data-default'));

            if (majorUpdated)
                getAvailableTemplates();

            return false;
        })

        //Form Validation
        jQuery('#suiteForm').submit(function () {
            //Title should not be empty
            if (jQuery('#ts_name').val() == '') {
                jQuery('#suite-info-box').find('.message').remove();
                jQuery('#suite-info-box .column').append('<div class="message error">Test Suite title should not be empty!</div>');
                jQuery('#ts_name').focus();
                return false;
            }

            //Check test suite name
            var nameReg = /^[A-Za-z0-9-.]+$/;
            if (!nameReg.test(jQuery('#ts_identifier').val())) {
                jQuery('#suite-info-box').find('.message').remove();
                jQuery('#suite-info-box .column').append('<div class="message error">Names must consist of only letters, numbers, dots and dashes [A-Za-z0-9-.]+</div>');
                jQuery('#ts_identifier').focus();
                return false;
            }

            jQuery('#brother-suites').remove();
            //Show Loading box
            jQuery('#saving-wrapper').show();
        })

        $('#ts_description, #scenarios-box textarea, #related-suites-box textarea, #specs-box textarea').redactor({
            air: true,
            minHeight: 80
        });

        $('#ts_identifier').on('change', function () {
            getAvailableTemplates();
        })
        function getAvailableTemplates() {
            if ($('#ts_identifier').val() != '') {
                $('#test-data-profiles-box .loading1').show();
                jQuery.ajax({
                    url: '<?php echo get_site_url()?>',
                    data: {
                        'id': '<?php echo $suite->id ?>',
                        'name': $('#ts_identifier').val(),
                        'version_major': $('#ts_version_major').val(),
                        '_wpnonce': '<?php echo wp_create_nonce('get-available-templates')?>'
                    },
                    type: 'POST',
                    dataType: 'html',
                    complete: function () {
                        $('#test-data-profiles-box .loading1').hide();
                    },
                    success: function (rsp) {
                        jQuery('select.availableTemplates').html(rsp);
                        /*jQuery('select.availableTemplates').each(function(){
                         if($(this).attr('data-default'))
                         {
                         $(this).val($(this).attr('data-default'));
                         }
                         })*/

                    }
                })
            }

        }
    })
</script>
<?php
get_footer();
?>
