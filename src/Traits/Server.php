<?php


namespace XuiApi\Traits;

use Exception;

trait Server{

    /** get panel status
     * @return array array of server status.
     * @throws Exception
     */
    public function getServerStatus():array{
        $result =  $this->do('server/status');
        return ($result['obj'] ?? []);
    }

    /** get available xray version.
     * @return string[] of available xray version.
     * @throws Exception
     */
    public function getXrayVersion():array{
        $result = $this->do('server/getXrayVersion');
        return ($result['obj'] ?? []);
    }

    /** stop xray service.
     * @return bool true on success.
     * @throws Exception
     */
    public function stopXrayService():bool{
        $result =  $this->do('stopXrayService');
        return ($result['success'] ?? false);
    }

    /** restart Xray Service.
     * @return bool true on success.
     * @throws Exception
     */
    public function restartXrayService():bool{
        $result = $this->do('restartXrayService');
        return ($result['success'] ?? false);
    }


    /** install xray version.
     * @param string $version version to install(ex:1.8.4)
     * @return bool true on success.
     * @throws Exception
     */
    public function installXray(string $version = 'v1.8.4'):bool{
        $url = $this->getUrl('server/installXray');
        $url = strtr($url,['{VERSION}'=>$version]);
        $result = $this->curl($url);
        return ($result['success'] ?? false);
    }

    /** get logs
     * @param int $count count of log
     * @return array array of logs
     * @throws Exception
     */
    public function logs(int $count = 1):array{
        $url = $this->getUrl('server/logs');
        $url = strtr($url,['{COUNT}'=>$count]);
        $result = $this->curl($url);
        return ($result['obj'] ?? []);
    }

    /** get configs array
     * @return array configs array
     * @throws Exception
     */
    public function getConfigJson():array{
        $result = $this->do('server/getConfigJson');
        return ($result['obj'] ?? []);
    }

    /** download x-ui.db(not implemented)
     * @return string path of downloaded file.
     */
    public function getDb(string $path =''):string{
        /*$result = $this->do('server/getDb');
        file_put_contents($path.'/x-ui.db',$result);*/
        return "";
    }

    /** import database file(not implemented)
     * @param string $file_path file path to import
     * @return bool true on success.
     */
    public function importDB(string $file_path):bool{
        return false;
    }

    /** generate new X25519Cert
     * @return array array of X25519Cert.(private and public key)
     * @throws Exception
     */
    public function getNewX25519Cert():array{
        $result = $this->do('server/getNewX25519Cert');
        return ($result['obj'] ?? []);
    }
}