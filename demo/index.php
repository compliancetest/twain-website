<html>
<head>
    <title>
        demo
    </title>
    <script src="Resources/dynamsoft.webtwain.config.js"></script>
    <script src="Resources/dynamsoft.webtwain.initiate.js"></script>
    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="data.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div id="dwtcontrolContainer" style="display: none !important;" ></div>
            <input type="button" value="Scan" onclick="AcquireImage();"/>
        </div>
    </div>
    <div class="row">
        <table id="results" class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>CAP NAME</th>
                <th>Current Value</th>
                <th>Default Value</th>
                <th>Value Type</th>
                <th>Capability Container Type</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    function AcquireImage() {
        $("#results tbody").html('');
        var DWObject = Dynamsoft.WebTwainEnv.GetWebTwain('dwtcontrolContainer');
        DWObject.IfDisableSourceAfterAcquire = true;
        DWObject.SelectSource();
        DWObject.OpenSource();
        DWObject.AcquireImage();

        for(i = 0; i < capsArray.length; i++){
            DWObject.Capability = EnumDWT_Cap[capsArray[i]];
            if(DWObject.CapGet() === true ) {
                    $rowString = "<tr>" +
                                    "<td>" + EnumDWT_Cap[capsArray[i]] + "</td>" +
                                    "<td>" + capsArray[i] + "</td>";
                                        DWObject.CapGet();
                $rowString += "<td>" + DWObject.CapValue + "</td>";
                DWObject.CapGet();
                $rowString += "<td>" + DWObject.CapDefaultValue + "</td>";
                DWObject.CapGet();
                $rowString += "<td>" + valueTypes[DWObject.CapValueType] + "</td>";
                DWObject.CapGet();
                $rowString += "<td>" + types[DWObject.CapType] + "</td>" +
                                "</tr>"
            } else {
                    $rowString = "<tr>" +
                                    "<td>" + EnumDWT_Cap[capsArray[i]] + "</td>" +
                                    "<td>" + capsArray[i] + "</td>" +
                                    "<td colspan='4' style='text-align: center; color:red;'>" +DWObject.ErrorString + "</td>" +
                                "</tr>"
            }
            $("#results tbody").append($rowString);
        }
    }
</script>
</body>
</html>