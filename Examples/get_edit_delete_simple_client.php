<?php

$xui = new \XuiApi\Panel\MHSanaei('http://1.1.1.1:8443','USERNAME','PASSWORD');
$xui->login();
$client_email = '';
$inbound_id = 2;
//get client
$inbound = $xui->getInbound($inbound_id);
$client = $xui->getClientByEmail($client_email,$inbound);
//update client
$client->enable = !$client->enable;
$client->limitIp = 0;
$updated = $xui->updateClient($client,$inbound);//true if successfully.
//delete client
$deleted = $xui->deleteClient($client,$inbound);
$deleted = $xui->setClient($client_email)->setInbound($inbound_id)->deleteClient();
