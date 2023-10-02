<?php

namespace XuiApi\Models;


use XuiApi\Panel\Base;

class Client implements Model {

    public bool $enable = true;
    public string $flow = '';
    public int $limitIp = 0;
    public string $tgId = '';

    public string $subId = '';
    public string $email;
    public string $id;
    public int $TotalGB;
    public int $expiryTime = 0;
    public int $used_GB;


    public function __construct(bool $enable = true, string $email = '', string $id = '', int $limit_ip = 0, int $used_GB = 0, int $total_GB = 0, int $expire_time = 0, string $tg_id = '', string $sub_id = '', string $flow = Flow::None){
        $this->enable = $enable;
        $this->email = $email ?? uniqid();
        $this->id = $id ?? Base::generateUUID();
        $this->limitIp = $limit_ip;
        $this->used_GB = $used_GB;
        $this->TotalGB = $total_GB;
        $this->expiryTime = $expire_time;
        $this->tgId = $tg_id;
        $this->subId = $sub_id;
        $this->flow = $flow;
    }

    public function changeStatus(bool $enable = true): static{
        $this->enable = $enable;
        return $this;
    }
    public function toArray(string $protocol = Protocol::VLESS):array{
        $return =  [
            'email'=>$this->email,
            'enable'=>$this->enable,
            'expiryTime' => $this->expiryTime,
            'limitIp' => $this->limitIp,
            'subId'=>$this->subId,
            'tgId'=>$this->tgId,
            'totalGB' => $this->TotalGB
        ];
        if($protocol !== Protocol::SHUDOWSOCKS){
            $return['flow'] = $this->flow;
        }else $return ['method'] = '';
        if($protocol === Protocol::TROJAN){
            $return['password'] = $this->id;
        }else $return['id'] = $this->id;

        return $return;
    }
    public function fromArray(array $data): static{
        foreach (get_class_vars(self::class) as $key => $value){
            $property_name = $key;
            //$key = str_replace(['expire_time','limit_ip','sub_id','tg_id','total_GB'],['expiryTime','limitIp','subId','tgId','TotalGB'],$key);
            if(isset($data[$key])) $this->$property_name = $data[$key];
        }
        if(isset($data['password'])) $this->id = $data['password'];//for trojan clients
        return $this;
    }
}