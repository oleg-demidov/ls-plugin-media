<?php


class PluginMedia_HookAdmin extends Hook{
    public function RegisterHook()
    {
        /**
         * Хук на отображение админки
         */
        $this->AddHook('init_action_admin', 'InitActionAdmin');
        
        $this->AddHook('admin_delete_content_after', 'MediaDelete', __CLASS__, 10);
    }

    /**
     * Добавляем в главное меню админки свой раздел с подпунктами
     */
    public function InitActionAdmin()
    {
        /**
         * Получаем объект главного меню
         */
        $oMenu = $this->PluginAdmin_Ui_GetMenuMain();
        /**
         * Добавляем новый раздел
         */
        $oSection =  Engine::GetEntity('PluginAdmin_Ui_MenuSection');
        
        $oSection->SetCaption($this->Lang_Get('plugin.media.admin.nav.media'))->SetName('media')->SetUrl('plugin/media')->setIcon('image');
        
        $oMenu->AddSection( $oSection );
    }
    
    public function MediaDelete(&$aParams) {
        $aMedia = $this->PluginMedia_Media_GetMediaItemsByFilter(['user_id' => $aParams['oUser']->getId()]);
        foreach ($aMedia as $media) 
        {
            $media->Delete();
        }
        
    }
}
