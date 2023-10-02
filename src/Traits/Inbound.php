<?php


namespace XuiApi\Traits;

use XuiApi\Models\Client;
use XuiApi\Models\InboundSetting;
use XuiApi\Models\Sniffing;
use XuiApi\Models\Inbound as InboundModel;
use Exception;

trait Inbound{

    /** get array of inbounds
     * @return array inbounds
     * @throws Exception
     */
    public function getInbounds():array{
        $result = $this->do('inbound/list');
        $return = [];
        foreach (($result['obj'] ?? []) as $inbound){
            $return[] = (new InboundModel())->fromArray($inbound);
        }
        return $return;
    }


    /** get inbound
     * @param int $inbound_id id of inbound.
     * @return InboundModel|null Inbound on success null on failure.
     * @throws Exception
     */
    public function getInbound(int $inbound_id = 0):InboundModel|null{
        if($inbound_id !== 0) $this->setInbound($inbound_id);
        $result =  $this->do('inbound/inbound');
        if($result['success'] ?? false) {
            return (new InboundModel())->fromArray($result['obj']);
        }else {
            $error_message = $result['msg'];
            if ($error_message == 'ObtainFail: record not found') {
                $error_message = 'record not found.';
            }
            throw new \Exception($error_message);
        }
    }

    /** get client traffic.
     * @param string $client_email client email
     * @return array array of traffics
     * @throws Exception
     */
    public function getClientTraffics(string $client_email = ''):array{
        if (!empty($client_email)) $this->setClient($client_email);
        if(empty($this->client_email)) throw new \Exception("client email is not set.");
        $result = $this->do('inbound/getClientTraffics');
        return $result['obj'] ?? [];
    }


    public function addInbound(int $port,string $protocol,array|null $streamSettings,string $remark = "",array|InboundSetting|null $settings = null,int $total = 0,bool $enable = true,int $up = 0,int $down = 0,string $sniffing = null, $expiryTime = 0, $listen = ''): InboundModel|false{
        //todo:
        $settings = is_null($settings) ? (new InboundSetting([((new Client())->toArray($protocol))]))->toArray($protocol) : $settings;
        $sniffing = is_null($sniffing) ? ((new Sniffing())->toArray()) : $sniffing;

        //$sniffing = jsonEncode($sniffing);
        //$settings = jsonEncode($settings);
        //$streamSettings = jsonEncode($streamSettings);
        $post_data = compact('remark', 'port', 'protocol', 'settings', 'streamSettings', 'total', 'enable', 'up', 'down', 'sniffing', 'expiryTime', 'listen');
        $result =  $this->do('inbound/add', $post_data);
        if($result['success'] ?? false){
            return ((new InboundModel())->fromArray($result['obj']));
        }else throw new \Exception($result['msj']);
    }

    /** delete inbound
     * @param InboundModel|int|null $inbound inbound integer id or Inbound model
     * @return bool true on successfully.
     * @throws Exception
     */
    public function deleteInbound(InboundModel|int $inbound = null):bool{
        if(!is_null($inbound)) $this->setInbound($inbound->id ?? $inbound);
        if(($this->inbound_id) === 0) throw new \Exception("inbound id not set.");
        $result = $this->do('inbound/del');
        return ($result['success'] ?? false);
    }

    /** update inbound
     * @param InboundModel $inbound inbound to update.
     * @return bool true on successfully
     * @throws Exception
     */
    public function updateInbound(InboundModel $inbound):bool{
        $this->setInbound($inbound->id);
        $result = $this->do('inbound/update',$inbound->toArray());
        return ($result['success'] ?? false);
    }

    /** get client ip
     * @param Client|null $client client to get ip.
     * @return array array of ips.
     * @throws Exception
     */
    public function getClientIP(Client|null $client = null):array{
        if(!is_null($client)) $this->setClient($client->email,$client->id);
        if(empty($this->getClientEmail())) throw new \Exception("client email not set.");
        $result = $this->do('inbound/clientIps');
        return json_decode($result['obj'],true) ?? [];
    }

    /** clear client ip
     * @param Client|null $client client to clear ip.
     * @return bool true on success.
     * @throws Exception
     */
    public function clearClientIP(Client|null $client = null):bool{
        if(!is_null($client)) $this->setClient($client->email,$client->id);
        if(empty($this->getClientEmail())) throw new \Exception("client email not set.");
        $result =  $this->do('inbound/clearClientIps');
        return $result['success'] ?? false;
    }


    /** add new client
     * @param Client $client client to add.
     * @param InboundModel|null $inbound inbound to client add in it.
     * @return bool true on success.
     * @throws Exception
     */
    public function addNewClient(Client $client, InboundModel|null $inbound = null):bool{
        $inbound = $this->checkInboundIsSet($inbound);
        $result = $this->addClient($inbound->id ?? $this->getInboundId(),['clients'=>[$client->toArray()]]);
        return ($result['success'] ?? false);
    }

    private function addClient(int $inbound_id,array $settings): array{
        return $this->do('addClient', ['id'=>$inbound_id,'settings'=>$settings] );
    }

    /** delete client
     * @param string|Client|null $client client to delete.
     * @param int|InboundModel|null $inbound client inbound.
     * @return bool true on success.
     * @throws Exception
     */
    public function deleteClient(string|Client $client = null, int|InboundModel $inbound = null):bool{
        if (!is_null($client)){
            if ($client instanceof Client){
                $client_email = $client->email;
            }else $client_email = $client;
        }else $client_email = $this->getClientEmail();
        if(empty($client_email)) throw new \Exception("client email is not set.");
        if(!is_null($inbound)){
            if($inbound instanceof InboundModel){
                $inbound_id = $inbound->id;
            }else $inbound_id = $inbound;
        }else $inbound_id = $this->getInboundId();
        if($inbound_id == 0) throw new \Exception("inbound id is not set");

        $this->setInbound($inbound_id);
        $this->setClient($client_email);
        $result = $this->do('inbound/delClient');
        return ($result['success'] ?? false);
    }

    /** update client
     * @param Client $client client to update
     * @param InboundModel|null $inbound inbound of client.
     * @return bool true on success
     * @throws Exception
     */
    public function updateClient(Client $client, InboundModel|null $inbound = null):bool{
        $inbound = $this->checkInboundIsSet($inbound);
        $settings = ['clients' => [$client->toArray()]];
        $inbound_id = $inbound->id;
        $result =  $this->do('inbound/updateClient', ['id'=>$inbound_id,'setting'=>$settings]);
        return ($result['success'] ?? false);
    }

    /*public function updateClient(int $inbound_id,array $settings){

        return
    }*/

    /** reset client traffic
     * @param Client|null $client client to reset traffic
     * @param InboundModel|null $inbound inbound of client
     * @return bool true on success.
     * @throws Exception
     */
    public function resetClientTraffic(Client|null $client = null , InboundModel|null $inbound = null):bool{
        if(!is_null($client)) $this->setClient($client->email,$client->id);
        if(!is_null($inbound)) $this->setInbound($inbound->id);
        if (empty($this->getClientEmail())) throw new \Exception("client email is not set");
        if($this->getInbound() === 0) throw new \Exception("inbound is not set");
        $result =  $this->do('resetClientTraffic');
        return (is_array($result) and $result['success']);
    }

    /** reset all traffics
     * @return bool true on success.
     * @throws Exception
     */
    public function resetAllTraffics():bool{
        $result = $this->do('inbound/resetAllTraffics');
        return ($result['success'] ?? false);
    }

    /** reset all client traffic inbound
     * @param int|InboundModel|null $inbound inbound to reset clients traffics.
     * @return bool true on success.
     * @throws Exception
     */
    public function resetAllClientTraffics(int|InboundModel $inbound = null):bool{
        if(!is_null($inbound)){
            if($inbound instanceof InboundModel){
                $inbound_id = $inbound->id;
            }else $inbound_id = $inbound;
            $this->setInbound($inbound_id);
        }
        if($this->inbound_id === 0) throw new \Exception('inbound id is not set.');
        $result = $this->do('inbound/resetAllClientTraffics');
        return ($result['success'] ?? false);
    }

    /** delete duplicated client
     * @return bool true on success.
     * @throws Exception
     */
    public function delDepletedClients():bool{
        $result = $this->do('inbound/delDepletedClients');
        return ($result['success'] ?? false);
    }

    /** create backup file in server
     * @return bool true on success.
     * @throws Exception
     */
    public function createBackup():bool{
        $result = $this->do('inbound/createbackup');
        return ($result['success'] ?? false);
    }
    //===================clients=============================

    /** get client by email.
     * @param string|null $client_email client email to get.
     * @param InboundModel|null $inbound inbound of client to get.
     * @return Client|false client on success. false on failure.
     * @throws Exception
     */
    public function getClientByEmail(string|null $client_email = null, InboundModel|null $inbound = null):Client|false{
        $inbound = $this->checkInboundIsSet($inbound);
        if(is_null($client_email)) $client_email = $this->getClientEmail();
        if(empty($client_email)) throw new \Exception("provide \$client_email or user ->setClientEmail()");
        return $inbound->getClient($client_email);
    }

    /** get client by id(uuid)
     * @param string|null $client_id client id(uuid)
     * @param InboundModel|null $inbound inbound of client to get.
     * @return false|Client client on success. false on failure.
     * @throws Exception
     */
    public function getClientById(string|null $client_id = null, InboundModel|null $inbound = null): false|Client{
        $inbound = $this->checkInboundIsSet($inbound);
        if(is_null($client_id)) $client_id = $this->getClientId();
        if(empty($client_id)) throw new \Exception("provide \$client_uuid or user ->setClientId()");
        return $inbound->getClient(id:$client_id);
    }
    //===========================================

}