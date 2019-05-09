<?php


class PluginMedia_ActionAdmin extends PluginAdmin_ActionPlugin
{

  

    public function Init()
    {
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

}