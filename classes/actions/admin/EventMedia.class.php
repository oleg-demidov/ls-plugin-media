<?php

class PluginMedia_ActionAdmin_EventMedia extends Event
{
    protected $oUserCurrent = null;

    public function Init()
    {
        $this->oUserCurrent = $this->User_GetUserCurrent();
    }

    
    public function EventLibrary() {
        $this->SetTemplateAction('library');
        
    }
    
    
}