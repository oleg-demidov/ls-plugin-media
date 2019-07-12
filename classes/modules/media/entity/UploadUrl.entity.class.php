<?php

class PluginMedia_ModuleMedia_EntityUploadUrl extends Entity
{
    
    private $aHeaders;


    protected $aValidateRules = array(
        [ 'path', 'headers'],
        [ 'path', 'path']
    );
    
    public function ValidateHeaders($sPath) {
        $this->aHeaders = get_headers($sPath, 1);
        if(isset($this->aHeaders[0])){
            
        }
    }
    
    public function ValidatePath($sPath) {
        $aHeaders = get_headers($sPath, 1);
        
        
        $rFile = fopen($sPath, 'r');
        if (!$rFile) {
            return $this->Lang_Get('media.error.upload');
        }
        
        $aMeta = get_headers($url, 1);

        $iMaxSizeKb = $this->PluginMedia_Media_GetConfigParam('max_size', '');
        $iSizeKb = 0;
        $sContent = '';
        while (!feof($rFile) and $iSizeKb < $iMaxSizeKb) {
            $sContent .= fread($rFile, 1024 * 2);
            $iSizeKb++;
        }
        /**
         * Если конец файла не достигнут,
         * значит файл имеет недопустимый размер
         */
        if (!feof($rFile)) {
            return $this->Lang_Get('media.error.too_large', array('size' => $iMaxSizeKb));
        }
        fclose($rFile);
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