The Update of [alirezax5](https://github.com/alirezax5/XuiApi) 

This is an exercise project to increase coding skills and the user is responsible for using it.

## About the project

It is a web service project to manage the x-ui panel

## Features

* Support for most x-ui panels(just change PATH const)
* Xray management by API
* Show status
* Change xray version
* Inbound management
* Client management
* Settings management
* User(admin) management.

## Install

``
composer require dgithuba/XuiApi
``

## Use  
```php 
<?php
require './vendor/autoload.php';
$xui = new \XuiApi\Panel\MHSanaei('YOU_PANEL_URL', 'YOU_PANEL_USERNAME', 'YOU_PANEL_PASSWORD');
$xui->login();
var_dump($xui->getInbounds());
```

## Example
[view Example](https://github.com/DgithubA/XuiApi/tree/master/Examples)
