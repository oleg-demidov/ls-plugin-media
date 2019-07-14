<?php

class PluginMedia_ModuleMedia_EntityMedia extends EntityORM
{
    

    protected $aValidateRules = array(
        [ 'type', 'string', 'on' => ['create']],
        [ 'name', 'string', 'min' => 1, 'on' => ['create']],
        [ 'size', 'size', 'on' => ['create']],
        [ 'path', 'path', 'on' => ['create']]
    );
    
    protected $aJsonFields = array(
        'data'
    );

    protected $aRelations = array(
        'targets' => array(self::RELATION_TYPE_HAS_MANY, 'PluginMedia_ModuleMedia_EntityTarget', 'media_id'),
        'author' => [self::RELATION_TYPE_BELONGS_TO, 'ModuleUser_EntityUser', 'user_id']
    );

    protected function beforeSave()
    {
        if ($bResult = parent::beforeSave()) {
            if ($this->_isNew()) {
                $this->setDateCreate(date("Y-m-d H:i:s"));
            }
        }
        return $bResult;
    }

    protected function beforeDelete()
    {
        if ($bResult = parent::beforeDelete()) {
            /**
             * Удаляем все связи
             */
            $aTargets = $this->getTargets();
            foreach ($aTargets as $oTarget) {
                $oTarget->Delete();
            }
            /**
             * Удаляем все файлы медиа
             */
            $this->PluginMedia_Media_DeleteFiles($this);
        }
        return $bResult;
    }

    public function getObject() {        
        switch ($this->getType()) {
            case PluginMedia_ModuleMedia::MEDIA_TYPE_IMAGE:
                return Engine::GetEntity('PluginMedia_ModuleMedia_EntityImage', ['media' => $this]);
                break;

        }
        return null;
    }

    public function getDataOne($sKey)
    {
        $aData = $this->getData();
        if (isset($aData[$sKey])) {
            return $aData[$sKey];
        }
        return null;
    }

    public function setDataOne($sKey, $mValue)
    {
        $aData = $this->getData();
        $aData[$sKey] = $mValue;
        $this->setData($aData);
    }
    
    public function getCountTargets() {
        return sizeof($this->getTargets());
    }
        
    public function ValidatePath($sPath) {
        if(!file_exists($sPath)){
            return $this->Lang_Get('plugin.media.uploader.notices.error_no_file');
        }
        return true;
    }
    
    public function ValidateSize($mValue) {
        
        $iMaxSize = $this->PluginMedia_Media_GetConfigParam( 'max_size', $this->getType());
        
        if($this->getSize() > $iMaxSize * 1024){
            return $this->Lang_Get(
                'plugin.media.uploader.notices.error_too_large', 
                array('size' => $iMaxSize)
            );
        }
        
        return true;
    }
   
    
    public function getPath($bWithType = false) {
        return $this->Fs_GetPathServer(parent::getPath(), $bWithType );
    }
    
    
    public function getWebPath($bWithType = false) {
        return $this->Fs_GetPathWeb(parent::getPath(), $bWithType );
    }
    
    public function getDateCreateFormat($format = 'd.m.y') {
        $date = new DateTime($this->getDateCreate());
        return $date->format($format);
    }
    
    public function addSize($aSize) {
        $aSizesData = $this->getDataOne('image_sizes');
        
        $aSizesData[] = $aSize;
        
        $this->setDataOne('image_sizes', $aSizesData);
    }
}