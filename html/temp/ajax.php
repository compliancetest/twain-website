<?php

sleep(2);
$arr = array ('status'=>'success','schema'=>'schema','message'=>'some message', 'responseText'=>'some responseText', 'type_id'=>'1');

echo json_encode($arr);