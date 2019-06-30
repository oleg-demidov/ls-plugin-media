<?php


class PluginMedia_HookAvatar extends Hook{
    public function RegisterHook()
    {
        /**
         * Хук на отображение админки
         */
        
        $this->AddHook('template_layout_body_end', 'AddCropModal');
        $this->AddHook('template_profile_settings_start', 'AddAvatarField');
        $this->AddHook('profile_settings_save_after', 'AddSizesAvatar');
        
        
    }
    
    
    public function AddSizesAvatar($param) {
        $aMedia = $this->PluginMedia_Media_GetMedias($param['oUser'], 'useravatar' );
        if(!$aMedia){
            return;
        }
        $sPath = current($aMedia)->getObject()->getWebPath('useravatar');
        $sPath = $this->Fs_GetPathServerFromWeb($sPath);
       
        $mResult = $this->PluginMedia_Media_GenerateImageBySizes(
            $sPath, 
            dirname($this->Fs_GetPathRelativeFromServer($sPath)), 
            basename(preg_replace('/\\.[^.\\s]{3,4}$/', '', $sPath)), 
            Config::Get('plugin.media.avatar.sizes')
        );
    }

    /**
     * Добавляем в главное меню админки свой раздел с подпунктами
     */
    public function AddCropModal()
    { 
        if(!$oUser = $this->User_GetUserCurrent()){   
            return;
        }
        
        if(Router::GetActionEventName() != "settings"){
            return;
        }
        
        $this->Viewer_Assign('oBehavior', $oUser->avatar, true);
        return $this->Viewer_Fetch('component@media:crop.modal');
        
    }
    
    public function AddAvatarField($param) {
        if(!isset($param['oUser'])){   
            return;
        }

        $this->Viewer_Assign('oBehavior', $param['oUser']->avatar);
        return $this->Viewer_Fetch('component@media:mfield');
    }
}
