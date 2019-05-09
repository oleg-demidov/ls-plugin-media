<?php

class PluginTest_ActionTest_EventPanel extends Event
{
    
    public function Init()
    {
        $this->oTest = $this->PluginTest_Test_GetTestByCode( $this->sCurrentEvent );
        
        
        $this->sMenuHeadItemSelect = $this->oTest->getCode();
        
        
    }
    
    public function EventIndex() {
        
        /**
         * Загружаем переменные в шаблон
         */
        $this->SetTemplateAction('panel/index');        
        
        
    }

    public function EventBilets() {
        
        $aBilets = $this->PluginTest_Test_GetBiletItemsByFilter([
            'test_id' => $this->oTest->getId(),
            '#select' => ['t.*','count(a.id) as count_ask'],
            '#join' => ['LEFT JOIN '.Config::Get('db.table.test_test_ask').' a ON a.bilet_id = t.id'],
            '#group' => ['id'],
            '#index-from' => 'id'
        ]);
        
        if ($this->oUserCurrent){
            $this->PluginTest_Test_AttachResultsToBilets($aBilets, $this->oUserCurrent);
        }
        
        $this->SetTemplateAction('panel/bilets');        
        $this->Viewer_Assign('sMenuItemSelect', 'bilets');
        $this->Viewer_Assign('aBilets', $aBilets);
    }
    
    public function EventCategories() {
        
        $oCategory = $this->PluginTest_Test_GetCategoryByFilter([ 'url' => $this->oTest->getCode(), '#index-from' => 'id' ]);
        
        $aCategories = $oCategory->getDescendants();
        
        if ($this->oUserCurrent){
            $this->PluginTest_Test_AttachResultsToCategories($aCategories, $this->oUserCurrent);
        }
        
        $this->SetTemplateAction('panel/categories');        
        $this->Viewer_Assign('sMenuItemSelect', 'categories');
        $this->Viewer_Assign('aCategories', $aCategories);
    }
    
    public function EventHard() {
        
        $this->SetTemplateAction('panel/hard');        
        $this->Viewer_Assign('sMenuItemSelect', 'hard');
        
    }
    
    public function EventShutdown() {
        $this->Viewer_Assign('oTest', $this->oTest);
    }
}