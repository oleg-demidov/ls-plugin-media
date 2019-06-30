<?php

class PluginMedia_ModuleMedia_EntityUser extends PluginMedia_Inherits_ModuleUser_EntityUser
{
        
    public function getProfileAvatar($size = '100x100') {
        $aMedia = $this->PluginMedia_Media_GetMedias($this, 'useravatar' );
        if($aMedia){
            $oMedia = current($aMedia);
            return $oMedia->getObject()->getWebPath('useravatar_'.$size);
        }
        return $this->User_GetDefaultAvatar();
    }
    
    public function Init() {
        parent::Init();
        
        $this->PluginMedia_Media_AttachUserBehaviorAvatar($this);
    }
}