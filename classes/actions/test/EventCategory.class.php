<?php

class PluginTest_ActionTest_EventCategory extends Event
{
    private$oCategory;
    
    private $oCategorySession;

    public function Init() {
        $iIdCategory = $this->GetParam(0);
        
        if(!$this->oCategory = $this->PluginTest_Test_GetCategoryByFilter(['id' => $iIdCategory])){
            return Router::ActionError($this->Lang_Get('plugin.test.bilet.error.no_find'));
        }
        
        $this->oCategorySession = $this->oCategory->getSession();
        
        $this->oTest = $this->oCategory->getTest();
        
        
        $this->sMenuHeadItemSelect = $this->oTest->getCode();
    }
    
    public function EventAsk($iNumberAsk = 0) {
        
        if(!$iNumberAsk){
            $iNumberAsk = $this->GetParamEventMatch(1, 2);
        }
        
        if(!$iNumberAsk){
            $iNumberAsk = 1;
        }
                
        $this->oCategorySession->setNowAsk($iNumberAsk);
        
        $oAsk = $this->PluginTest_Test_GetCategoryAsk($this->oCategory, $iNumberAsk);
        
        if(!$oAsk ){
            return Router::ActionError($this->Lang_Get('plugin.test.ask.error.no_find'));
        }
        
        $showSubmit = 1;
        $showHint = 0;
        
        if(isPost()){
            $bResult = $this->PluginTest_Test_Ans($oAsk, getRequest('ans'));
            
            $this->oCategorySession->setAskResult($iNumberAsk, (int)$bResult);
            $this->oCategorySession->Save();
                        
            $showHint = !$bResult;
            $showSubmit = 0;
        }
        
        $this->Viewer_Assign('componentAskWebPath', $this->Component_GetWebPath('test:ask'));
        $this->Viewer_Assign('oAsk', $oAsk);
        $this->Viewer_Assign('iNumberAsk', $iNumberAsk);
        $this->Viewer_Assign('oCategory', $this->oCategory);
        $this->Viewer_Assign('oCategorySession', $this->oCategorySession);
        $this->Viewer_Assign('iAnsId', getRequest('ans'));
        $this->Viewer_Assign('showSubmit', $showSubmit);
        $this->Viewer_Assign('showHint', $showHint);
        
        if($this->oCategorySession->isFinished()){
            $this->Viewer_Assign('urlFinish', Router::GetPath('test/category/'.$this->oCategory->getId().'/finish'));
            $this->Viewer_Assign('hideNow', 1);
            $this->Viewer_Assign('showSubmit', false);
        }elseif(!$this->oCategorySession->isLast()){
            $this->Viewer_Assign('sNextUrl', Router::GetPath('test/category/'.$this->oCategory->getId().'/next'.$iNumberAsk));
        }
        
        $iCountAsk = $this->oCategory->getCountAsks();
        $this->Viewer_Assign('iCountAsk', $iCountAsk);
        
        $this->SetTemplateAction('category/ask');
    }
    
    public function EventAjaxAsk() {
        $this->Viewer_SetResponseAjax('json');
        
        $iNumberAsk = getRequest('iNumberAsk');
        
        $this->oCategorySession->setNowAsk($iNumberAsk);
        
        $oAsk = $this->PluginTest_Test_GetCategoryAsk($this->oCategory, $iNumberAsk);
        
        if(!$oAsk){
            $this->Message_AddError($this->Lang_Get('plugin.test.ask.error.no_find'));
            return;
        }
        
        $showSubmit = 1;
        $showHint = 0;
        
        if(getRequest('action')){
            if($oResult = $this->SubmitResultAns($oAsk, getRequest('ans'))){
                $showSubmit = 0;
                $showHint = !$oResult->getResult();
            }
        }
        
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->GetSmartyObject()->addPluginsDir(Config::Get('path.application.server').'/classes/modules/viewer/plugs');
        
        $oViewer->Assign('componentAskWebPath', $this->Component_GetWebPath('test:ask'));
        $oViewer->Assign('oAsk', $oAsk, true);
        $oViewer->Assign('iNumberAsk', $iNumberAsk, true);
        $oViewer->Assign('aPaginationAsks', $this->oCategorySession->getData());
        $oViewer->Assign('iAnsId', getRequest('ans'), true);
        $oViewer->Assign('showSubmit', $showSubmit, true);
        $oViewer->Assign('showHint', $showHint, true);
        $oViewer->Assign('aAnses', $oAsk->getAnses(), true);
        
        if($this->oCategorySession->isFinished()){
            $oViewer->Assign('urlFinish', Router::GetPath('test/category/'.$this->oCategory->getId().'/finish'), true);
            $this->Viewer_AssignAjax('hideNow', 1);
        }elseif(!$this->oCategorySession->isLast()){
            $oViewer->Assign('nextUrl', Router::GetPath('test/category/'.$this->oCategory->getId().'/next'.$iNumberAsk), true);
        }
        
        $iCountAsk = $this->oCategory->getCountAsks();
        $this->Viewer_AssignAjax('iNumberAsk', $iNumberAsk);
        $this->Viewer_AssignAjax('iCountAsk', $iCountAsk);
        $this->Viewer_AssignAjax('aPaginationAsks', $this->oCategorySession->getData());
        $this->Viewer_AssignAjax('hideNow', $this->oCategorySession->isFinished());
        $this->Viewer_AssignAjax('nextAsk', $this->oCategorySession->getNextAsk());
        $this->Viewer_AssignAjax('html', $oViewer->Fetch('component@test:ask'));
    }
    
    private function SubmitResultAns($oAsk, $iAnsId) {
        $oUser = $this->User_GetUserCurrent();

        $aFilter = [
            'user_id' => $oUser->getId(),
            'ask_id'  => $oAsk->getId(),
            'type'    => 'category'
        ];
        if(!$oResult = $this->PluginTest_Test_GetResultByFilter($aFilter)){
            $oResult = Engine::GetEntity('PluginTest_Test_Result');
        }

        if(!$oUser){
            $oResult->setTmp(1);
        }else{
            $oResult->setUserId($oUser->getId());
        }

        $oResult->setAskId($oAsk->getId());
        $oResult->setTestId($this->oTest->getId());
        $oResult->setAsk($oAsk);
        $oResult->setType('category');
        $oResult->setAnsId($iAnsId);
        $oResult->setUserId($oUser->getId());
        $oResult->setCategoryId($this->oCategory->getId());

        if($oResult->_Validate()){
            $this->oCategorySession->setAskResult( (int)$oResult->getResult() );
            $this->oCategorySession->Save();

            $oResult->Save();
            
            return $oResult;

        }else{
            foreach($oResult->_getValidateErrors() as $aError){
                $this->Message_AddError($aError[0], $this->Lang_Get('common.error.error'));
            }
            
            return false;
        }
    }
    
    public function EventNext() {
        $iNumberAsk = $this->GetParamEventMatch(1, 2);
        
        if(!$iNumberAsk){
            return Router::ActionError($this->Lang_Get('plugin.test.bilet.error.no_find'));
        }
        
        $this->oCategorySession->setNowAsk($iNumberAsk);
        
        Router::LocationAction('test/category/'. $this->oCategory->getId(). '/ask'. $this->oCategorySession->getNextAsk());
    }
    
    public function EventFinish() {
        
        if($this->oCategorySession){
            $this->oCategorySession->Drop();
        }
        
        if($this->oTest){
            Router::LocationAction('test/'.$this->oTest->getCode().'/categories');
        }
        
        Router::LocationAction('/');
    }
    
}