<?php

sleep(1);
$arr = array ('status'=>'success','schema'=>'schema','message'=>'some message', 'responseText'=>'some responseText', 'type_id'=>'1');
header('Content-Type: application/json');
echo json_encode($arr);