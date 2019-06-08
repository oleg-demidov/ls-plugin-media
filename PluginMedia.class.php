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
        $this->Lang_AddLangJs([
            'plugin.media.library.button.tooltip',
            'plugin.media.media.remove'
        ]);

        $this->Component_Add('media:library');
        $this->Component_Add('media:tinymce-plugin');

        $this->Viewer_AppendScript(Plugin::GetTemplatePath('media'). '/assets/js/init.js');
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