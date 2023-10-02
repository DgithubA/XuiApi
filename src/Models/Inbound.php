<?php

namespace XuiApi\Models;

class Inbound implements Model {

    public int $id = 0;
    public int $up = 0;
    public int $down = 0;
    public int $total = 0;
    public string $remark = '';
    public bool $enable = true;
    public int $expiry_time = 0;
    public $client_stats = null;
    public string $listen = '';
    public int $port  = 0;
    public string $protocol = Protocol::VLESS;
    public InboundSetting|null $settings = null;
    public array|null $stream_settings = null;
    public string $tag = '';
    public Sniffing|null $sniffing = null;


    public function __construct(int $id = 0,int $up = 0,int $down = 0,int $total = 0,string $remark = '',bool $enable = true,int $expiry_time = 0,$client_stats = null,string $listen = '',int $port = 0,string $protocol = Protocol::VLESS,InboundSetting|null $settings = null, array $stream_settings = [],string $tag = '',Sniffing|null $sniffing = null){
        $this->id = $id;
        $this->up = $up;
        $this->down = $down;
        $this->total = $total;
        $this->remark = $remark;
        $this->enable = $enable;
        $this->expiry_time = $expiry_time;
        $this->client_stats = $client_stats;
        $this->listen = $listen;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->settings = is_null($settings) ? new InboundSetting([]): $settings;
        $this->stream_settings = $stream_settings;
        $this->tag = $tag;
        $this->sniffing = is_null($sniffing) ? new Sniffing() : $sniffing;
    }

    public function toArray():array{
        $return =  [
            'id'=>$this->id,
            'up' => $this->up,
            'down' => $this->down,
            'total' => $this->total,
            'remark' => $this->remark,
            'enable' => $this->enable,
            'expiryTime' => $this->expiry_time,
            'clientStats' => $this->client_stats,
            'listen'=> $this->listen,
            'port' => $this->port,
            'protocol' => $this->protocol,
            'tag' => $this->tag,
        ];
        if(!is_null($this->settings)){
            $return['settings'] = $this->settings->toArray($this->protocol);
        }else $return['settings'] = "";

        if(!is_null($this->sniffing)){
            $return['sniffing'] = $this->sniffing->toArray();
        }else $return['sniffing'] = "";
        if(!is_null($this->stream_settings)){
            $return['streamSettings'] = $this->stream_settings;
        }else $return['streamSettings'] = "";

        return $return;
    }
    public function fromArray(array $data):static{
        foreach (get_class_vars(self::class) as $key => $value){
            if(in_array($key,['settings','sniffing','streamSettings'])) continue;
            if(isset($data[$key])) $this->$key = $data[$key];
        }


        if(!empty($data['settings'])) {
            $this->settings = (new InboundSetting())->fromArray(json_decode($data['settings'], true), $this->protocol ?? Protocol::VLESS);
        }else $this->settings = null;
        if(!empty($data['sniffing'])){
            $this->sniffing = (new Sniffing())->fromArray(json_decode($data['sniffing'],true));
        }else $this->sniffing = null;

        if(!empty($data['streamSettings'])) {
            $this->stream_settings = json_decode($data['streamSettings'], true);
        }else $this->stream_settings = null;

        return $this;
    }

    public function getClient(string $email = '',string $id = '',$index = -1):Client|false
    {
        return $this->settings->getClient($email,$id,$index);
    }
}