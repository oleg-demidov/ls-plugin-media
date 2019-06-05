<?php


class PluginMedia_HookLayout extends Hook{
    public function RegisterHook()
    {
        /**
         * Хук на отображение админки
         */
        
        if(Router::GetParam(0) != 'media'){
            $this->AddHook('template_admin_body_end', 'AddLibrary');
            $this->AddHook('template_body_end', 'AddLibrary');
        }
        
    }

    /**
     * Добавляем в главное меню админки свой раздел с подпунктами
     */
    public function AddLibrary()
    { 
        if($oUser = $this->User_GetUserCurrent()){   
            $this->Viewer_Assign('oUser', $oUser);
            return $this->Viewer_Fetch('component@media:library.modal');
        }
    }
}
