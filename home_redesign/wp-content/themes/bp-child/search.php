<?php

if ($_GET['post_type']=='test-suite')
{
    require('search-suite.php');
}else if($_GET['post_type']=='product-service'){
    require('search-productservice.php');
}