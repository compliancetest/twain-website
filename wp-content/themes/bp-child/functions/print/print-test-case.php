<?php
/**
* Print Test Case Page
*/
$case = new TestCase(get_the_ID());
$case->load();
?>
<!DOCTYPE HTML>
<html>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php wp_title( '', true, 'right' ); ?></title>
</head>
<body onload="print_page()">
    <style type="text/css">
        body{
            font-family: 'Open Sans',Arial,Tahoma,Helevetica,sans-serif; font-size: 12px; line-height: 14px; color: #111;            
            padding: 20px 20px;
        }
        a{color: #2c80e8; text-decoration: none;}
        h2{font-size: 20px; margin:0 0 20px;}
        h3{font-size: 18px; margin:0 0 15px;}
        h4{font-size: 16px;  margin:0 0 10px;}
        h5{font-size: 14px;  margin:0 0 8px;}
        table{border: solid 1px #999; border-collapse: collapse; vertical-align: top; width: 100%}
        th{text-align: left; font-weight: bolid; border: solid 1px #999; padding: 5px;}
        td{border: solid 1px #999;  padding: 5px; vertical-align: top;}
        .clear{
            clear: both;
        }
        .block{
            padding: 10px 0;
            border-top: solid 1px #999;
        }
        .noborder,
        .noborder td,
        .noborder th{ border: none;}
        .td-label{
            font-weight: bold;
            font-size: 13px;
            vertical-align: top;
        }
    </style>
    
    <h3 style="float: left">Test case ID: <a href="<?php echo get_permalink()?>"><?php echo $case->testCaseID ; ?></a></h3>
    <h5 style="float: right; line-height: 24px">(Test Suite: <a href="<?php echo get_permalink($case->testSuite)?>"><?php echo get_the_title($case->testSuite) ?></a>)</h5>
    <div class="clear"></div>
    
    <table class="noborder" style="width: 100%">
        <tr>
            <td class="td-label">Info:</td>
            <td>Version: <b><?php echo $case->version; ?></b></td>
            <td>Issued: <b>
                                <?php 
                                    echo formatDate($case->publishedDate);
                                ?>
                                </b>
            </td>
            <td colspan="3">Initiating Messsage: <b><?php echo $case->initiationgMessage; ?></b></td>
        </tr>
        <tr>
            <td class="td-label">Roles:</td>
            <td>Tester Role: <b><?php echo $case->testerRole; ?></b></td>
            <td>Harness Role: <b>
                                <?php 
                                    echo $case->harnessRole;
                                ?>
                                </b>
            </td>
            <td colspan="3">Initiator: <b><?php echo $case->Initiator; ?></b></td>
        </tr>
        <tr>
            <td class="td-label">Properties:</td>
            <td>Conformance Level: <b><?php echo $case->conformanceLevel; ?></b></td>
            <td>Outcome Type: <b>
                                <?php 
                                    echo $case->outcomeType;
                                ?>
                                </b>
            </td>
            <td>Test Pattern: <b><a href="<?php echo get_site_url() ?>/help-faq/test-patterns/"><?php echo $case->testPattern; ?></a></b></td>
            <td>Bulk: <b><?php echo $case->bulk; ?></b></td>
        </tr>
        <tr>
            <td class="td-label">Scenario:</td>
            <td colspan="4">
                <?php
                $test_suite_id = isset($_SESSION['test_suite_id']) ? $_SESSION['test_suite_id'] : $case->testSuite[0];
                $scenarioDetail = $case->getScenario($test_suite_id);
                echo '<b>' . $scenarioDetail->code . ': </b>';
                echo $scenarioDetail->description;
                ?>
            </td>
        </tr>
        
    </table>
    <br />
    <br />
    <h5>Test Execution</h5>
    <table>
        <?php if (!empty($case->testEndpointURL) || !empty($case->protocolBinding) ): ?>
        <tr>
            <td><b>Test trigger URL:</b></td>
            <td><a href="<?php echo $case->testEndpointURL?>" class="blue_txt"><?php echo $case->testEndpointURL ; ?></a></td>
        </tr>
        <tr>
            <td><b>Protocol Binding:</b></td>
            <td><?php echo $case->protocolBinding ; ?></td>
        </tr>
        <?php endif; ?>
        <?php
        foreach($case->testExecutionData as $key => $row){
        ?>
            <tr>
                <td><b><?php  echo $row['name'].':';?></b></td>
                <td>
                    <?php if(strpos($row['value'], 'http://') !== false || strpos($row['value'], 'https://') !== false){ ?>
                    <a href="<?php echo $row['value']; ?>" class="blue_txt"><?php echo $row['value']; ?></a>
                    <?php }else{ ?>
                    <?php echo $row['value']?>
                    <?php } ?>
                </td>
            </tr>
        <?php    
        } 
        ?>
    </table>
    <br />
    <br />
    <h5>Test Data</h5>
    <table>
        <?php
        foreach($case->testData as $key => $row){
        ?>
            <tr>
                <td><b><?php  echo $row['name'].':';?></b></td>
                <td>
                    <?php if(strpos($row['value'], 'http://') !== false || strpos($row['value'], 'https://') !== false){ ?>
                    <a href="<?php echo $row['value']; ?>" class="blue_txt"><?php echo $row['value']; ?></a>
                    <?php }else{ ?>
                    <?php echo $row['value']?>
                    <?php } ?>
                </td>
            </tr>
        <?php    
        } 
        ?>
    </table>
    <br />
    <br />
    <h5>Test Steps</h5>
    <table>
        <thead>
            <tr>
                <th>Steps</th>
                <th>Action</th>
                <th>Expected Result</th>
            </tr>
        </thead>
        <tbody>
        <?php                    
        foreach($case->testSteps as $key => $row){
        ?>
                <tr>
                    <td><?php echo ($key+1); ?></td>
                    <td><?php echo _convertLineSymbolToBR($row['action']); ?></td>
                    <td><?php echo _convertLineSymbolToBR($row['result']); ?></td>
                </tr>
        <?php    
        } ?>
        </tbody>
    </table>
    <br />
    <br />
</body>
<script type="text/javascript">
    function print_page()
    {
            window.print();
    }
</script>
</html>