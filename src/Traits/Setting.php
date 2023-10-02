<?php


namespace XuiApi\Traits;

trait Setting{

    public function getAllSetting():array{
        return $this->do('Setting/all')['obj'] ?? [];
    }

    public function getDefaultSetting():array{
        $result = $this->do('setting/defaultSettings');
        return ($result['obj'] ?? []);
    }

    public function updateSetting($webPort, $webCertFile, $webKeyFile, $webBasePath, $xrayTemplateConfig, bool $tgBotEnable = false,
                                  $tgExpireDiff = 0, $tgTrafficDiff = 0, $tgCpu = 0, string $tgBotToken = null, $tgBotChatId = null,
                                  $tgRunTime = '@daily', $tgBotBackup = false, $tgLang = 'fa_IR', $secretEnable = false, $subEnable = false,
                                  $subListen = '', $subPort = '2096', $subPath = 'sub/', $subDomain = '', $subCertFile = '', $subKeyFile = '',
                                  $subUpdates = '12', $timeLocation = 'Asia/Tehran', $webListen = ''):bool{
        $com = compact('webPort', 'webCertFile', 'webKeyFile', 'webBasePath', 'xrayTemplateConfig', 'tgBotEnable', 'tgExpireDiff', 'tgTrafficDiff', 'tgCpu', 'tgBotToken', 'tgBotChatId', 'tgRunTime', 'timeLocation', 'webListen', 'tgBotBackup', 'tgLang', 'secretEnable', 'subEnable', 'subListen', 'subPort', 'subPath', 'subDomain', 'subCertFile', 'subKeyFile', 'subUpdates');
        $result =  $this->do('setting/update', $com);
        return ($result['success'] ?? false);
    }

    public function updateUser($oldUsername, $oldPassword, $newUsername, $newPassword):bool{
        $result =  $this->do('updateUser', compact('oldPassword', 'oldUsername', 'newPassword', 'newUsername'), true);
        return ($result['success'] ?? false);
    }
    public function resetPanel():bool{
        $result = $this->do('setting/resetPanel');
        return ($result['success'] ?? false);
    }

    public function getDefaultJsonConfig():array{
        $result = $this->do('setting/getDefaultJsonConfig');
        return $result['obj'] ?? [];
    }

    public function updateUserSecret():array{
        $result = $this->do('setting/updateUserSecret');
        return ($result['obj'] ?? []);
    }

    public function getUserSecret():array{
        $result = $this->do('setting/getUserSecret');
        return ($result['success'] ?? []);
    }



}