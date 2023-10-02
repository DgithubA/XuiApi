<?php

$xui = new \XuiApi\Panel\MHSanaei('http://1.1.1.1:8443','USERNAME','PASSWORD');
$xui->login();
$status = $xui->getServerStatus();
$setting = $xui->getAllSetting();

