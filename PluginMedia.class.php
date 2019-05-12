<?php
/**
 * 
 * @author Oleg Demidov
 *
 */

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attempt!');
}

class PluginMedia extends Plugin
{
    
    
    public function Init()
    {
//        $this->Lang_AddLangJs([
//            'plugin.wiki.markitup.punkt'
//        ]);
////        
//        $this->Component_Add('media:media');
//        $this->Component_Add('media:uploader');

//        $this->Viewer_AppendScript(Plugin::GetTemplatePath('media'). '/assets/js/init.js');
    }

    public function Activate()
    {
        
        return true;
    }

    public function Deactivate()
    {
        
        return true;
    }
    
    public function Remove()
    {
        
        return true;
    }
}