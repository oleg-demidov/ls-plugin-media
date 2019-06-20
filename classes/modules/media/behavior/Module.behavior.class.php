<?php
/*
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Oleg Demidov
 *
 */

/**
 *
 * @package application.modules.category
 * @since 2.0
 */
class PluginMedia_ModuleMedia_BehaviorModule extends Behavior
{
    public $sName;
    
    protected $aParams = array(
        // Уникальный код
        'target_type'                    => null,
        
    );
    /**
     * Список хуков
     *
     * @var array
     */
    protected $aHooks = array(
        'module_orm_GetItemsByFilter_after'  => array(
            'CallbackGetItemsByFilterAfter',
            1000
        )        
    );
    
    
    /**
     * Модифицирует фильтр в ORM запросе
     *
     * @param $aParams
     */
    public function CallbackGetItemsByFilterAfter($aParams)
    {
        $aEntities = $aParams['aEntities'];
        $aFilter = $aParams['aFilter'];
        $this->PluginMedia_Media_RewriteGetItemsByFilter(
            $aEntities, 
            $aFilter,
            $this->GetBehaviors()
        );
    }
    

}