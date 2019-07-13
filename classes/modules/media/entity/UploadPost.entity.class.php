<?php

class PluginMedia_ModuleMedia_EntityUploadPost extends Entity
{
    

    protected $aValidateRules = array(
        [ 'tmp_name', 'tmp_name'],
        [ 'size', 'size'],
        [ 'type', 'type'],
    );
    
    public function ValidateType($sType) {
        if(!$sTypeMedia = $this->PluginMedia_Media_CheckMediaType($sType)){
            return $this->Lang_Get('plugin.media.uploader.notices.error_no_type', ['type' => $sType]);
        }
        
        $this->setType($sTypeMedia);
        
        return true;
    }
    
    public function ValidateTmpName($sPath) {
        if (!$this->getTmpName()) {
            return $this->Lang_Get('plugin.media.uploader.notices.error_no_file');
        }
        
        /*
         * Копируем во временное место для обработки
         */      
        $sDirTmp = Config::Get('path.tmp.server') . '/media/tmp/';
        
        if (!is_dir($sDirTmp)) {
            if(!mkdir($sDirTmp, 0777, true)){
                return $this->Lang_Get('media.error.upload');
            }
        }
        
        $sFileTmp = $sDirTmp . $this->getName();
        if (!move_uploaded_file($this->getTmpName(), $sFileTmp)) {
            return $this->Lang_Get('media.error.upload');
        }
        $this->setPath($sFileTmp);
        
        return true;
    }
    
    public function ValidateSize($mValue) {
        if ($this->getError() != UPLOAD_ERR_OK) {
            switch ($this->getError()) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $sMessError =  $this->Lang_Get(
                        'plugin.media.uploader.notices.error_too_large', 
                        array('size' => @func_ini_return_bytes(ini_get('upload_max_filesize')) / 1024)
                    );
                default:
                    $sMessError = $this->Lang_Get('plugin.media.uploader.notices.error_upload');
                    return $sMessError;
            }
        }
        
        $iMaxSize = $this->PluginMedia_Media_GetConfigParam( 'max_size', $this->getType());
        
        if($this->getSize() > $iMaxSize * 1024){
            return $this->Lang_Get(
                'plugin.media.uploader.notices.error_too_large', 
                array('size' => $iMaxSize)
            );
        }
        
        return true;
    }
   
}