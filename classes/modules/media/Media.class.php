<?php

class PluginMedia_ModuleMedia extends ModuleORM
{
    
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_DOC = 'doc';
    
    protected $aTypesAllow = [
        self::MEDIA_TYPE_IMAGE => [
            'image/jpeg'
        ]
    ];
    
    
    /**
     * Инициализация
     *
     */
    public function Init()
    {
        parent::Init();
    }

    public static function getMediaTypesAllow(){
        return $this->aTypesAllow;
    }

    public function CheckMediaType($sType) {
        foreach ($this->aTypesAllow as $sKeySection => $aTypeSection) {
            if(in_array($sType, $aTypeSection)){
                return $sKeySection;
            }
        }
        return false;
    }
    
    public function GetMediaByRequest($aFile) {
        if(!$sType = $this->CheckMediaType(current($_FILES['files']['type']))){
            return $this->Lang_Get('plugin.media.uploader.notices.error_no_type');
        }
        
        return $this->GetMediaByTypeParams($sType, $aFile);
    }

    public function GetMediaByTypeParams($sType, $aParams) {
        return Engine::GetEntity('PluginMedia_Media_Media', [
            'type' => $sType
        ]);
    }
}
