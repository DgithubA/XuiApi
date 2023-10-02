<?php

namespace XuiApi\Panel;
class MHSanaei extends Base{

    const PATH = [
        'BASS'=>"http://1.1.1.1:443/",
        'setting'=>[
            'BASS'=>'/panel/setting',
            'all'=>'/all',
            'defaultSettings'=>'/defaultSettings',
            'update'=>'/update',
            'updateUser'=>'/updateUser',
            'restartPanel'=>'/restartPanel',
            'getDefaultJsonConfig'=>'GET:/getDefaultJsonConfig',
            'updateUserSecret'=>'/updateUserSecret',
            'getUserSecret'=>'/getUserSecret'
        ],
        'server'=>[
            'BASS' =>'/server',
            'status' => '/status',
            'getXrayVersion' => '/getXrayVersion',
            'stopXrayService' => '/stopXrayService',
            'restartXrayService' => '/restartXrayService',
            'installXray' => '/installXray/{VERSION}',
            'logs' => '/logs/{COUNT}',
            'getConfigJson' => '/getConfigJson',
            'getDb' => 'GET:/getDb',
            'importDB' => '/importDB',
            'getNewX25519Cert' => '/getNewX25519Cert',
        ],
        'inbound'=>[
            'BASS'=>"/panel/api/inbounds",
            //in file api.go
            'list' => 'GET:/list',
            'inbound' => 'GET:/get/{inbound_id}',
            'getClientTraffics' => 'GET:/getClientTraffics/{client_email}',
            'add' => '/add',
            'delete' => '/del/{inbound_id}',
            'update' => '/update/{inbound_id}',
            'clientIps' => '/clientIps/{client_email}',
            'clearClientIps' => '/clearClientIps/{client_email}',//this one don't support bass
            'addClient' => '/addClient',
            'delClient' => '/{inbound_id}/delClient/{client_id}',
            'updateClient' => '/updateClient/{client_id}',
            'resetClientTraffic' => '/{inbound_id}/resetClientTraffic/{client_email}',
            'resetAllTraffics'=>'/resetAllTraffics',
            'resetAllClientTraffics' => '/resetAllClientTraffics/{inbound_id}',
            'delDepletedClients'=> '/delDepletedClients',
            'createbackup' => 'GET:/createbackup'
        ],
        'auth'=>[
            'login' => '/login',
            'logout'=>'GET:/logout',
            'getSecretStatus'=>'/getSecretStatus'
        ],
    ];
}