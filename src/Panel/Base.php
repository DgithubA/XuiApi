<?php

namespace XuiApi\Panel;


use XuiApi\Models\Client;
use XuiApi\Models\Inbound;
use XuiApi\Models\Protocol;
use XuiApi\Traits\Inbound as InboundTrait;
use XuiApi\Traits\Server;
use XuiApi\Traits\Setting;
use Exception;
abstract class Base{
    use Setting,Server,InboundTrait;
    private string $client_email = '';
    private string $client_id = '';
    private int $inbound_id = 0;

    protected string $url, $username, $password, $cookie;
    /**
     * @var string[] path of panels api url.
     */
    const PATH = [
        'login' => '/login',
        'status' => '/server/status',
        'getConfigJson' => '/server/getConfigJson',
        'getDb' => '/server/getDb',
        'getNewX25519Cert' => '/server/getNewX25519Cert',
        'restartXrayService' => '/server/restartXrayService',
        'stopXrayService' => '/server/stopXrayService',
        'getXrayVersion' => '/server/getXrayVersion',
        'installXray' => '/server/installXray/{id}',
        'logs' => '/server/logs',
        'restartPanel' => '/setting/restartPanel',
        'allSetting' => '/xui/setting/all',
        'updateSetting' => '/xui/setting/update',
        'updateUser' => '/xui/setting/updateUser',
        'listInbound' => '/xui/inbound/list',
        'inbound' => '/xui/inbound/get/{id}',
        'delInbound' => '/xui/inbound/del/{id}',
        'updateInbound' => '/xui/inbound/update/{id}',
        'addInbound' => '/xui/inbound/add',
        'addClient' => '/xui/inbound/addClient/',
        'delClient' => '/xui/inbound/delClient/{id}',
        'resetClientTraffic' => '/xui/inbound/{id}/resetClientTraffic/{client}',
        'updateClient' => '/xui/inbound/updateClient/{id}',
        'clientIps' => '/xui/inbound/clientIps/{id}',
        'clearClientIps' => '/xui/clearClientIps/{id}',
    ];
    const DEFAULT_STEAM_SETTINGS = [
        'default' => ['network'=>'tcp','security'=>'none','rcpSettings'=>['acceptProxyProtocol'=>false,'header'=>['type'=>'none']]],
        'reality'=>['network'=>"tcp",'security'=>'reality','realitySetting'=>['show'=>'false','xver'=>0,'dest'=>'zula.ir','serverNames'=>['zula.ir','www.zula.ir']]
                    ,'privateKey'=>'ICZi7LMSNfM1mYFsj6UH0ik8VYA4arDFq8fyzX_x-FA','minClient'=>'','maxClient'=>'','maxTimediff'=>0,'shortIds'=>['c8e536c7']
                    ,'settings'=>['publicKey'=>'S9YtLRzwvBM0zGipIyg0Hl4kcFpxsmOqVqKwp4fpCkE','fingerprint'=>'chrome','serverName'=>'','spiderX'=>'/']
                    ,'tcpSettings'=>['acceptProxyProtocol'=>false,'header'=>['type'=>'none']]],
    ];
    const CONFIG = ['echo'=>false,'report_unsuccessful_result'=>false];
    public function __construct($url, $username, $password){
        if(substr($url,-1,1) === DIRECTORY_SEPARATOR) $url = substr($url,0,-1);
        $this->url = $url;
        $this->cookie = getcwd() . DIRECTORY_SEPARATOR. "cookie.txt";
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param string $path path of command(must be set in $this->>path)
     * @param array $body array of data to post.
     * @return array result of request
     * @throws Exception on problem to post data and curl errors.
     */
    public function do(string $path, array $body = []):array{
        $url = $this->getUrl($path);
        //echo $url."<br>";
        $method = $this->getPathMethod($path);
        return $this->curl($url,$body,$method === 'POST');
    }

    protected function getPathMethod(string $path):string{
        $url_path = self::getPathData($path);
        if(str_contains($url_path,'GET:')) return 'GET';
        return 'POST';
    }
    /**
     * @param string $url url.
     * @param array $data array of data to post.
     * @param bool $POST method is post.
     * @return array result of request.
     * @throws Exception
     */
    protected function curl(string $url, array $data = [],bool $POST = true):array{

        $echo = self::CONFIG['echo'];
        $ch = curl_init();
        $method = $POST ? 'POST' : 'GET';
        $data = self::jsonize($data);
        if($echo) {
            echo '<hr> data:';
            var_dump($data);
            echo '<hr>';
        }
        //echo $url.'<br>'.'method:'.$method.'<br><hr>';
        $options = [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => $POST,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE => $this->getCookie(),
            CURLOPT_COOKIEJAR => $this->getCookie(),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);
        $StatusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if(curl_errno($ch) !== 0) throw new Exception("Error happened in ".__FUNCTION__.': status code:'.$StatusCode.' error message:'.curl_error($ch));
        if ($StatusCode != 200) throw new \Exception("status code of request: $StatusCode");
        curl_close($ch);
        if($echo) {
            echo '<hr> result:';
            var_dump($res);
            echo '<hr>';
        }
        if(self::CONFIG['report_unsuccessful_result'] and (!$res['success'] ?? false)) throw new \Exception($res['msj'] ?? 'unsuccessful result');
        return json_decode($res, true);
    }

    //====================setter and getters====================
    //region setter and getter
    /** set client to use in commands and requests.
     * @param string $client_email client email to set.
     * @param string $client_id client id(uuid) to set.
     * @return $this
     */
    public function setClient(string $client_email = "", string $client_id = ""): static
    {
        if(!empty($client_email)) $this->client_email = $client_email;
        if (!empty($client_id)) $this->client_id = $client_id;
        return $this;
    }

    /** set inbound to use in commands and requests.
     * @param int $inbound_id id of inbound.
     * @return $this
     */
    public function setInbound(int $inbound_id): static
    {
        $this->inbound_id = $inbound_id;
        return $this;
    }

    /** get set inbound id.
     * @return int
     */
    protected function getInboundId():int{
        return $this->inbound_id;
    }

    /**get Adjusted Client Email
     * @return string client email(empty string if not set)
     */
    protected function getClientEmail():string{
        return $this->client_email;
    }

    /** get Adjusted Client Id
     * @return string id of Client(empty string means not set)
     */
    protected function getClientId():string{
        return $this->client_id;
    }

    /** set coolie file path
     * @param string $dir path to save cookie file.
     * @return $this
     */
    public function setCookie(string $dir): static{
        $this->cookie = $dir;
        return $this;
    }

    /** get cookie path
     * @return string cookie path
     */
    public function getCookie():string{
        return $this->cookie;
    }

    /** is inbound set
     * @return bool true means is set.
     */
    protected function isInboundSet():bool{
        return ($this->inbound_id !== 0);
    }

    /** is Client set
     * @return bool true means is set.
     */
    protected function isClientSet():bool{
        if(!empty($this->client_email)) return true;
        if(!empty($this->client_id)) return true;
        return false;
    }
    //endregion
    //===================================
    /** get full url request: bassPanelUrl + path of method.
     * @param string $path name of path.
     * @return string full url.
     * @throws Exception if passed wrong path and not founded in self::PATH
     */
    protected function getUrl(string $path): string{
        $url_path = self::getPathData($path);
        $url_path = strtr($url_path, ['GET:'=>'','{inbound_id}' => $this->getInboundId(), '{client_email}' => $this->getClientEmail(), '{client_id}' => $this->getClientId()]);
        if (substr($url_path,0,1) !== '/') $url_path = '/'.$url_path;
        return $this->url . $url_path;
    }

    protected static function getPathData(string $path):string{
        $path_ex = explode('/',$path);
        $hare_path = "";
        $url_path = "";
        $paths_data = MHSanaei::PATH;
        foreach ($path_ex as $key){
            if (isset($paths_data[$key])) {
                if(is_array($paths_data[$key])) {
                    $hare_path .= $key . '=>';
                    $paths_data = $paths_data[$key];
                }else {
                    $have_bass = $paths_data['BASS'] ?? false;
                    if($have_bass and !empty($have_bass)) $url_path .= $have_bass;
                    $url_path .= $paths_data[$key];
                }
            }else throw new \Exception('bad path provided:'.$hare_path . "`$key`");
        }
        return $url_path;
    }

    private function checkInboundIsSet(Inbound|null $inbound = null):Inbound{
        if(is_null($inbound)){
            $inbound_id = $this->getInboundId();
            if($inbound_id !== 0) $inbound = $this->getInbound($inbound_id);
        }
        if (is_null($inbound)) throw new \Exception("inbound is not set.");
        return $inbound;
    }
    //=============cookie=============
    //region cookie file
    /** initialise cookie file.
     * @return $this
     */
    protected function initCookieFile(): static
    {
        if (!$this->checkCookieFile())
            file_put_contents($this->cookie, '');
        return $this;
    }

    /** delete and remake cookie file.
     * @return $this
     */
    protected function resetCookieFile(): static
    {
        if ($this->checkCookieFile())
            unlink($this->cookie);

        $this->initCookieFile();
        return $this;
    }

    /** check cookie file is exist.
     * @return bool true if cookie file is exist.
     */
    protected function checkCookieFile(): bool
    {
        return file_exists($this->cookie);
    }

    /** check cookie file is valid.
     * @return bool true if cookie not expired and exist.
     */
    protected function checkCookieIsValid():bool{
        if($this->checkCookieFile()){
            $cookie_file_content = file_get_contents($this->cookie);
            if(preg_match('~(\d{10})~',$cookie_file_content,$m)){
                $timestamp = (int)$m[1];
                return (time() < $timestamp);
            }
        }
        return false;
    }
    //endregion
    //===============================
    //region authentication
    /** login and save cookie.
     * @return $this
     * @throws Exception
     */
    protected function auth(): static{
        if (!$this->checkCookieFile()) {
            $this->initCookieFile();
            $this->do('auth/login', ['username' => $this->username, 'password' => $this->password]);
        }
        return $this;
    }

    /** login Force and reset cookie.
     * @return $this
     * @throws Exception if login unsuccessfully
     */
    protected function authForce(): static{
        $this->resetCookieFile();
        $this->do('auth/login', ['username' => $this->username, 'password' => $this->password]);

        return $this;
    }


    /** login
     * @param bool $force force login
     * @return $this
     * @throws Exception if login unsuccessfully.
     */
    public function login(bool $force = false):static{
        if(!$this->checkCookieIsValid()) $force = true;
        return ($force ? $this->authForce() : $this->auth());
    }

    /** logout
     * @return bool true on successfully.
     * @throws Exception
     */
    public function logout():bool{
        $result =  $this->do('auth/logout');
        return ($result['success'] ?? false);
    }

    /** get secret status.
     * @return bool secret status.
     * @throws Exception
     */
    public function getSecretStatus():bool{
        $result =  $this->do('auth/getSecretStatus');
        return ($result['obj'] ?? false);
    }

    //endregion

    //region helper functions
    protected static function jsonize(array $data):array{
        foreach ($data as $key => $value){
            if(is_array($value)) $data[$key] = json_encode($value);
        }
        return $data;
    }


    public static function generateUUID($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected static function jsonEncode($json): false|string{
        return json_encode($json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function getclientUrl(string $server,Client|string $client,Inbound $inbound):string{
        //its sooo Complicated and boring. u can see sub/subservice.go to implement this.
        if($inbound->protocol === Protocol::SHUDOWSOCKS) {
            $return = 'ss';
        }else $return = $inbound->protocol;
        $return .= '://';
        //ss://MjAyMi1ibGFrZTMtYWVzLTI1Ni1nY206TFRPTE03R2xwUVBxTnpqam9XOEFqcnd0L2c3SExLS05zL04vUHpvRklNWT06RExlZU5HUVpDQ3FzK2JsOUt2emNLSjRmQllTVEpjaTdGZ2o3aTIvSnpqST0@185.139.7.173:32437?type=tcp#shadowsocks%3Achanged%20by%20api-yx8pm7mx
        return $return;
    }
    //endregion
}