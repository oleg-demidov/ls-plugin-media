<?php

class PluginTest_ActionTest extends ActionPlugin
{
    /**
     * Текущий пользователь
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent = null;
    
    public $oTest = null;
    
    protected $sMenuHeadItemSelect = null;
    
    /**
     * Инициализация
     *
     * @return string
     */
    public function Init()
    {
        
        $this->oUserCurrent = $this->User_GetUserCurrent();
        
        if(!$this->oUserCurrent and Router::GetParam(0) !== 'index'){
            return Router::Action(Router::GetAction(), Router::GetActionEvent(), ['index']);
        }
        
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        
        
        $this->RegisterEventExternal('Bilet','PluginTest_ActionTest_EventBilet');
        $this->AddEventPreg('/^bilet$/i', '/^[0-9]{1,50}$/i', '/^(ask([0-9]{1,50}))?$/i', 'Bilet::EventAsk');
        $this->AddEventPreg('/^bilet$/i', '/^[0-9]{1,50}$/i', '/^(next([0-9]{1,50}))?$/i', 'Bilet::EventNext');
        $this->AddEventPreg('/^bilet$/i', '/^[0-9]{1,50}$/i', 'Bilet::EventFinish');
        $this->AddEventPreg('/^ajax-bilet$/i', '/^[0-9]{1,50}$/i', 'Bilet::EventAjaxAsk');
        
        $this->RegisterEventExternal('Category','PluginTest_ActionTest_EventCategory');
        $this->AddEventPreg('/^category$/i', '/^[0-9]{1,50}$/i', '/^(ask([0-9]{1,50}))?$/i', 'Category::EventAsk');
        $this->AddEventPreg('/^category$/i', '/^[0-9]{1,50}$/i', '/^(next([0-9]{1,50}))?$/i', 'Category::EventNext');
        $this->AddEventPreg('/^category$/i', '/^[0-9]{1,50}$/i', 'Category::EventFinish');
        $this->AddEventPreg('/^ajax-category$/i', '/^[0-9]{1,50}$/i', 'Category::EventAjaxAsk');
        
        $this->RegisterEventExternal('Hard','PluginTest_ActionTest_EventHard');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i', '/^hard-test$/i', '/^(ask([0-9]{1,50}))?$/i', 'Hard::EventAsk');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i','/^hard-test$/i', '/^(next([0-9]{1,50}))?$/i', 'Hard::EventNext');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i','/^hard-test$/i', '/^finish$/i', 'Hard::EventFinish');
        $this->AddEventPreg('/^ajax-hard-test$/i', '/^[0-9]{1,50}$/i', 'Hard::EventAjaxAsk');        
        
        $this->RegisterEventExternal('Panel','PluginTest_ActionTest_EventPanel');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i', '/^(index)?$/i', 'Panel::EventIndex');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i', '/^bilets$/i', 'Panel::EventBilets');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i', '/^categories$/i', 'Panel::EventCategories');
        $this->AddEventPreg('/^[a-z_0-9]{1,50}$/i', '/^hard$/i', 'Panel::EventHard');        
    }

    public function EventShutdown() {
        $this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        $this->Viewer_Assign('oTest', $this->oTest);
    }

}