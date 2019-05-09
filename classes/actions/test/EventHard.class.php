<?php

class PluginTest_ActionTest_EventHard extends Event
{
    private $oHard;
    
    private $oHardSession;

    public function Init() {
        $this->oTest = $this->PluginTest_Test_GetTestByCode( $this->sCurrentEvent );
        
        if(!$this->oTest){
            $iIdTest = getRequest('id', $this->GetParam(0));
            $this->oTest = $this->PluginTest_Test_GetTestById( $iIdTest );
        }
        
        if(!$this->oTest){
            return parent::EventNotFound();
        }
        
        $this->oHard = $this->oTest->getHard(Config::Get('plugin.test.test.hard.count'));
        
        $this->oHardSession = $this->oHard->getSession();
                
        $this->sMenuHeadItemSelect = $this->oTest->getCode();
    }
    
    public function EventAsk($iNumberAsk = 0) {
        
        if(!$iNumberAsk){
            $iNumberAsk = $this->GetParamEventMatch(1, 2);
        }
        
        if(!$iNumberAsk){
            $iNumberAsk = 1;
        }
                
        $this->oHardSession->setNowAsk($iNumberAsk);
        
        $oAsk = $this->PluginTest_Test_GetAskById($this->oHard->getNowAskId($iNumberAsk) );
        
        if(!$oAsk){
            return Router::ActionError($this->Lang_Get('plugin.test.ask.error.no_find'));
        }
        
        $showSubmit = 1;
        $showHint = 0;
        
        if(isPost()){
            if($oResult = $this->SubmitResultAns($oAsk, getRequest('ans'))){
                $showSubmit = 0;
                $showHint = !$oResult->getResult();
            }
        }
        
        $this->Viewer_Assign('componentAskWebPath', $this->Component_GetWebPath('test:ask'));
        $this->Viewer_Assign('oAsk', $oAsk);
        $this->Viewer_Assign('iNumberAsk', $iNumberAsk);
        $this->Viewer_Assign('oHard', $this->oHard);
        $this->Viewer_Assign('oHardSession', $this->oHardSession);
        $this->Viewer_Assign('iAnsId', getRequest('ans'));
        $this->Viewer_Assign('showSubmit', $showSubmit);
        $this->Viewer_Assign('showHint', $showHint);
        $this->Viewer_Assign('nextAsk', $this->oHardSession->getNextAsk());
        
        if($this->oHardSession->isFinished()){
            $this->Viewer_Assign('urlFinish', Router::GetPath('test/'.$this->oTest->getCode().'/hard-test/finish'));
            $this->Viewer_Assign('hideNow', 1);
        }elseif(!$this->oHardSession->isLast()){
            $this->Viewer_Assign('sNextUrl', Router::GetPath('test/'.$this->oTest->getCode().'/hard-test/next'.$iNumberAsk));
        }
        
        $iCountAsk = $this->oHard->getCountAsks();
        $this->Viewer_Assign('iCountAsk', $iCountAsk);
        
        $this->SetTemplateAction('hard/ask');
    }
    
    public function EventAjaxAsk() {
        $this->Viewer_SetResponseAjax('json');
        
        $iNumberAsk = getRequest('iNumberAsk');
        
        $this->oHardSession->setNowAsk($iNumberAsk);
        
        $oAsk = $this->PluginTest_Test_GetAskById($this->oHard->getNowAskId($iNumberAsk) );
        
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
        //$oViewer->Assign('oHard', $this->oHard);
        $oViewer->Assign('aPaginationAsks', $this->oHardSession->getData());
        $oViewer->Assign('iAnsId', getRequest('ans'), true);
        $oViewer->Assign('showSubmit', $showSubmit, true);
        $oViewer->Assign('showHint', $showHint, true);
        $oViewer->Assign('aAnses', $oAsk->getAnses(), true);
        
        if($this->oHardSession->isFinished()){
            $oViewer->Assign('urlFinish', Router::GetPath('test/'.$this->oTest->getCode().'/hard-test/finish'), true);
            $this->Viewer_AssignAjax('hideNow', 1);
        }elseif(!$this->oHardSession->isLast()){
            $oViewer->Assign('nextUrl', Router::GetPath('test/'.$this->oTest->getCode().'/hard-test/next'.$iNumberAsk), true);
        }
        
        $iCountAsk = $this->oHard->getCountAsks();
        $this->Viewer_AssignAjax('iNumberAsk', $iNumberAsk);
        $this->Viewer_AssignAjax('iCountAsk', $iCountAsk);
        $this->Viewer_AssignAjax('aPaginationAsks', $this->oHardSession->getData());
        $this->Viewer_AssignAjax('hideNow', $this->oHardSession->isFinished());
        $this->Viewer_AssignAjax('nextAsk', $this->oHardSession->getNextAsk());
        $this->Viewer_AssignAjax('html', $oViewer->Fetch('component@test:ask'));
    }
    
    private function SubmitResultAns($oAsk, $iAnsId) {
        $oUser = $this->User_GetUserCurrent();

        $aFilter = [
            'user_id' => $oUser->getId(),
            'ask_id'  => $oAsk->getId(),
            'type'    => 'bilet'
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
        $oResult->setType('bilet');
        $oResult->setAnsId($iAnsId);
        $oResult->setUserId($oUser->getId());

        if($oResult->_Validate()){
            $this->oHardSession->setAskResult( (int)$oResult->getResult() );
            $this->oHardSession->Save();

           // $oResult->Save();
            
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
        
        $this->oHardSession->setNowAsk($iNumberAsk);
        
        Router::LocationAction('test/'.$this->oTest->getCode().'/hard-test/ask'. $this->oHardSession->getNextAsk());
    }
    
    public function EventFinish() {
        
        if($this->oHardSession){
            $this->oHardSession->Drop();
        }
        
        if($this->oTest){
            Router::LocationAction('test/'.$this->oTest->getCode().'/hard');
        }
        
        Router::LocationAction('/');

    }
    
}