<?php

class PluginMedia_ModuleMedia_EntityUploadUrl extends Entity
{
    
    private $aHeaders;


    protected $aValidateRules = array(
        [ 'url', 'headers'],
        [ 'url', 'type'],
        [ 'url', 'size'],
        [ 'url', 'path']
    );
    
    public function ValidateHeaders($sPath) {
        $this->aHeaders = get_headers($sPath, 1);
        if(!isset($this->aHeaders[0])){
            return $this->Lang_Get('plugin.media.uploader.notices.error_url_dont_work');
        }
        if(!preg_match('/200\sOK/i', $this->aHeaders[0])){
            return $this->Lang_Get('plugin.media.uploader.notices.error_url_dont_work').' '.$this->aHeaders[0];
        }
        return true;
    }
    
    public function ValidateType($sPath) {
        if(!isset($this->aHeaders['Content-Type'])){
            return $this->Lang_Get('plugin.media.uploader.notices.error_url_headers_type');
        }
        
        if(!$sTypeMedia = $this->PluginMedia_Media_CheckMediaType($this->aHeaders['Content-Type'])){
            return $this->Lang_Get('plugin.media.uploader.notices.error_no_type', ['type' => $this->aHeaders['Content-Type']]);
        }
        
        $this->setType($sTypeMedia);
        
        return true;
    }
    
    public function ValidateSize($sPath) {
        if(!isset($this->aHeaders['Content-Length'])){
            return $this->Lang_Get('plugin.media.uploader.notices.error_url_headers_length');
        }        
        
        $this->setSize($this->aHeaders['Content-Length']/1024);
        
        return true;
    }
    
    public function ValidatePath($sPath) {
        
        $rFile = fopen($sPath, 'r');
        if (!$rFile) {
            return $this->Lang_Get('plugin.media.uploader.notices.error_url_upload');
        }
        
        $iMaxSizeKb = $this->PluginMedia_Media_GetConfigParam('max_size', $this->getType());
        
        $iSizeKb = 0;
        $sContent = '';
        while (!feof($rFile) and $iSizeKb < $iMaxSizeKb) {
            $sContent .= fread($rFile, 1024 * 2);
            $iSizeKb ++;
        }
        /**
         * Если конец файла не достигнут,
         * значит файл имеет недопустимый размер
         */
        
        if (!feof($rFile)) {
            return $this->Lang_Get('plugin.media.uploader.notices.error_too_large', array('size' => $iMaxSizeKb));
        }
        
        fclose($rFile);
       
        
        /**
         * Копируем загруженный файл
         */
        $sDirTmp = Config::Get('path.tmp.server') . '/media/';
        if (!is_dir($sDirTmp)) {
            @mkdir($sDirTmp, 0777, true);
        }
        $sFileTmp = $sDirTmp . func_generator() . '.' . $this->getType();
        $rFile = fopen($sFileTmp, 'w');
        fwrite($rFile, $sContent);
        fclose($rFile);
        
        $this->setPath($sFileTmp);
        
        return true;
    }
    
}