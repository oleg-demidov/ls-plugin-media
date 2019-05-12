<?php

class PluginMedia_ModuleMedia_EntityMedia extends EntityORM
{

    protected $aValidateRules = array();
    

    protected $aJsonFields = array(
        'data'
    );

    protected $aRelations = array(
        'targets' => array(self::RELATION_TYPE_HAS_MANY, 'ModuleMedia_EntityTarget', 'media_id')
    );

    protected function beforeSave()
    {
        if ($bResult = parent::beforeSave()) {
            if ($this->_isNew()) {
                $this->setDateAdd(date("Y-m-d H:i:s"));
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
            $this->Media_DeleteFiles($this);
        }
        return $bResult;
    }

    public function getObject() {
        switch ($this->getType()) {
            case PluginMedia_ModuleMedia::MEDIA_TYPE_IMAGE:
                return $this->Image_Open($this->getPath());
                break;

        }
        return null;
    }
        
    public function getPathServer() {
        return $this->Fs_GetPathServer($this->getFilePath());
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
}