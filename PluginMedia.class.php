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
    protected $aInherits = [
        'entity' => [
            /*
             * Для добавления аватарок
             */
            'ModuleUser_EntityUser' => '_ModuleMedia_EntityUser'
        ]
    ];
    
    public function Init()
    {
        $this->Lang_AddLangJs([
            'plugin.media.library.button.tooltip',
            'plugin.media.media.remove'
        ]);

        $this->Component_Add('media:library');
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