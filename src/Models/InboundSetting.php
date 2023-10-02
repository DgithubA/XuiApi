<?php

namespace XuiApi\Models;

class InboundSetting implements Model {
    /**
     * @var Client[]
     */
    public array $clients = [];
    public array $other = [];
    public function __construct(array $clients = [],array $other = []){
        if(empty($clients)) $clients = [new Client()];
        $this->other = $other;
        $this->clients = $clients;
    }

    public function toArray(string $protocol = Protocol::VLESS): array{
        $return = [];
        if(!empty($this->clients)) {
            $toset = [];
            foreach ($this->clients as $client) {
                $toset[] = ($client instanceof Client) ? $client->toArray($protocol) : $client;
            }
            $return = ['clients' => $toset];
        }
        $return = array_merge($return, $this->other);
        /*
        if($protocol === Protocol::VLESS){
            if(empty($this->other)) {
                $return['decryption'] = $this->other['decryption'] ?? 'none';
                $return['fallbacks'] = $this->other['fallbacks'] ?? [];
            }else  $return = array($return, $this->other);
        }

        if($protocol === Protocol::TROJAN){
            $return['fallbacks'] = $this->other['fallbacks'] ?? [];
        }


        if($protocol === Protocol::SHUDOWSOCKS){
            $return['method'] = $this->other['method'] ?? '';
            $return['password'] = $this->other['password'] ?? '';
            $return['network'] = $this->other['network'] ?? 'tcp,udp';
        }
        $accounts = $this->other['accounts'] ?? [['user'=>$this->other['user'] ?? '','pass'=>$this->other['pass'] ?? '']];

        if($protocol === Protocol::HTTP){
            $return = ['accounts'=> $accounts];
        }
        if($protocol === Protocol::SOCKS){
            $return = $this->other[Protocol::SOCKS] ?? ['auth'=>$this->other['auth'] ?? 'password', 'accounts'=>$accounts,'udp'=>$this->other['udp'] ?? false,$this->other['ip'] ?? '127.0.0.1'];
        }
        if($protocol === Protocol::DOKOME_DOOR){
            $return = $this->other[Protocol::DOKOME_DOOR] ?? ['network'=>$this->other['network'] ?? 'tcp,udp' , 'followRedirect'=>$this->other['followRedirect'] ?? false];
        }*/
        return $return;
    }

    public function fromArray(array $data,string $protocol = Protocol::VLESS):static{

        if(in_array($protocol,[Protocol::VMESS,Protocol::VLESS,Protocol::TROJAN,Protocol::SHUDOWSOCKS]) and isset($data['clients'])) {
            $clients = [];
            foreach ($data['clients'] as $client) {
                $clients[] = (new Client())->fromArray($client);
            }
            $this->clients = $clients;
        }else $this->clients = [];

        $others = $data;
        unset($others['clients']);
        $this->other = $others;
        return $this;
    }

    public function getClient(string $email = '',string $id = '', int $index = -1):Client|false{
        if(!empty($email)){
            foreach ($this->clients as $client){
                if($client->email === $email) return $client;
            }
        }
        if(!empty($id)){
            foreach ($this->clients as $client){
                if($client->id === $id) return $client;
            }
        }
        if($index !== -1){
            return $this->clients[$index];
        }
        return false;
    }
}