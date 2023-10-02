<?php

$xui = new \XuiApi\Panel\MHSanaei('http://1.1.1.1:8443','USERNAME','PASSWORD');
$xui->login();
$inbound_id = 2;
//get inbound
$inbound = $xui->setInbound($inbound_id)->getInbound();
$inbound = $xui->getInbound($inbound_id);
//update inbound
$inbound->remark = 'remark changed by api';
$xui->updateInbound($inbound);
//delete inbound
$xui->deleteInbound($inbound);
$xui->deleteInbound($inbound_id);