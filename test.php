<?php
require_once 'curl.class.php';
$curl = new Hscurl();
//$ret1 = $curl->get('http://github.site.com/curl/r.php?tt=22');
$ret1 = $curl->post(array('username'=>'lishoujie','password'=>'123456'))->get('http://github.site.com/curl/r.php?tt=22');



p($ret1);



function p($obj = array()){
    echo '<pre>';
    print_r($obj);
    echo '</pre>';
}