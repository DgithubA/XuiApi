<?php

namespace XuiApi\Models;

class Sniffing implements Model {
    const AllDestOverride = ['http','tls','quic','fakedns'];
    public bool $enabled = true;
    public array $destOverride = ['http','tls','quic','fakedns'];
    public function __construct(bool $enabled = true,array $destOverride = self::AllDestOverride){
        $this->enabled = $enabled;
        $this->destOverride = $destOverride;
    }

    public function toArray():array{
        return ['enabled'=>$this->enabled,'destOverride'=>$this->destOverride];
    }

    public function fromArray(array $data):static{
        $this->enabled = $data['enabled'] ?? true;
        $this->destOverride = $data['destOverride'] ?? self::AllDestOverride;
        return $this;
    }
}