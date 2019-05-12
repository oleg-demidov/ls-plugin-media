<?php


class PluginMedia_ActionAdmin extends PluginAdmin_ActionPlugin
{
    
    protected $oUserCurrent = null;

    public function Init()
    {
        /*
         * Если нет прав доступа - перекидываем на 404 страницу
         */
        if (!$this->oUserCurrent = $this->User_GetIsAdmin(true)) {
            return parent::EventNotFound();
        }
        $this->SetDefaultEvent('media');
        
    }

    /**
     * Регистрируем евенты
     *
     */
    protected function RegisterEvent()
    {
        /**
         * Для ajax регистрируем внешний обработчик
         */
       
        $this->RegisterEventExternal('Media', 'PluginMedia_ActionAdmin_EventMedia');        
        $this->AddEventPreg( '/^media$/i',  'Media::EventLibrary');
        
    }
    
    public function EventShutdown() {
        parent::EventShutdown();
        $this->Viewer_Assign('oUserCurrent', $this->oUserCurrent);
    }

}