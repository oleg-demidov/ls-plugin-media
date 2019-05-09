<?php

class PluginTest_ActionTest_EventBilet extends Event
{
    private $oBilet;
    
    private $oBiletSession;

    public function Init() {
        $iIdBilet = getRequest('id', $this->GetParam(0));
        
        if(!$this->oBilet = $this->PluginTest_Test_GetBiletByFilter(['id' => $iIdBilet])){
            return Router::ActionError($this->Lang_Get('plugin.test.bilet.error.no_find'));
        }
        
        $this->oBiletSession = $this->oBilet->getSession();
        
        $this->oTest = $this->oBilet->getTest();
        
        
        $this->sMenuHeadItemSelect = $this->oTest->getCode();
    }
    
    public function EventAsk($iNumberAsk = 0) {
        
        if(!$iNumberAsk){
            $iNumberAsk = $this->GetParamEventMatch(1, 2);
        }
        
        if(!$iNumberAsk){
            $iNumberAsk = 1;
        }
                
        $this->oBiletSession->setNowAsk($iNumberAsk);
        
        $oAsk = $this->PluginTest_Test_GetBiletAsk($this->oBilet, $iNumberAsk);
        
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
        $this->Viewer_Assign('oBilet', $this->oBilet);
        $this->Viewer_Assign('oBiletSession', $this->oBiletSession);
        $this->Viewer_Assign('iAnsId', getRequest('ans'));
        $this->Viewer_Assign('showSubmit', $showSubmit);
        $this->Viewer_Assign('showHint', $showHint);
        $this->Viewer_Assign('nextAsk', $this->oBiletSession->getNextAsk());
        
        if($this->oBiletSession->isFinished()){
            $this->Viewer_Assign('urlFinish', Router::GetPath('test/bilet/'.$this->oBilet->getId().'/finish'));
            $this->Viewer_Assign('hideNow', 1);
        }elseif(!$this->oBiletSession->isLast()){
            $this->Viewer_Assign('sNextUrl', Router::GetPath('test/bilet/'.$this->oBilet->getId().'/next'.$iNumberAsk));
        }
        
        $iCountAsk = $this->oBilet->getCountAsks();
        $this->Viewer_Assign('iCountAsk', $iCountAsk);
        
        $this->SetTemplateAction('bilet/ask');
    }
    
    public function EventAjaxAsk() {
        $this->Viewer_SetResponseAjax('json');
        
        $iNumberAsk = getRequest('iNumberAsk');
        
        $this->oBiletSession->setNowAsk($iNumberAsk);
        
        $oAsk = $this->PluginTest_Test_GetBiletAsk($this->oBilet, $iNumberAsk);
        
        if(!$oAsk){
            $this->Message_AddError($this->Lang_Get('plugin.test.ask.error.no_find'));
            return;
        }
        
        $showSubmit = true;
        $showHint = 0;
        
        if(getRequest('action')){ $this->Logger_Notice((int)getRequest('ans'));
            if($oResult = $this->SubmitResultAns($oAsk, getRequest('ans'))){
                $showSubmit = false;
                $showHint = !$oResult->getResult();
            }
        }
        
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->GetSmartyObject()->addPluginsDir(Config::Get('path.application.server').'/classes/modules/viewer/plugs');
        
        $oViewer->Assign('componentAskWebPath', $this->Component_GetWebPath('test:ask'));
        $oViewer->Assign('oAsk', $oAsk, true);
        $oViewer->Assign('iNumberAsk', $iNumberAsk, true);
        //$oViewer->Assign('oBilet', $this->oBilet);
        $oViewer->Assign('aPaginationAsks', $this->oBiletSession->getData());
        $oViewer->Assign('iAnsId', getRequest('ans'), true);
        $oViewer->Assign('showSubmit', $showSubmit, true);
        $oViewer->Assign('showHint', $showHint, true);
        $oViewer->Assign('aAnses', $oAsk->getAnses(), true);
        
        if($this->oBiletSession->isFinished()){
            $oViewer->Assign('urlFinish', Router::GetPath('test/bilet/'.$this->oBilet->getId().'/finish'), true);
            $this->Viewer_AssignAjax('hideNow', 1);
        }elseif(!$this->oBiletSession->isLast()){
            $oViewer->Assign('nextUrl', Router::GetPath('test/bilet/'.$this->oBilet->getId().'/next'.$iNumberAsk), true);
        }
        
        $iCountAsk = $this->oBilet->getCountAsks();
        $this->Viewer_AssignAjax('iNumberAsk', $iNumberAsk);
        $this->Viewer_AssignAjax('iCountAsk', $iCountAsk);
        $this->Viewer_AssignAjax('showSubmit', $showSubmit);
        $this->Viewer_AssignAjax('aPaginationAsks', $this->oBiletSession->getData());
        $this->Viewer_AssignAjax('hideNow', $this->oBiletSession->isFinished());
        $this->Viewer_AssignAjax('nextAsk', $this->oBiletSession->getNextAsk());
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
        }

        $oResult->setAskId($oAsk->getId());
        $oResult->setTestId($this->oTest->getId());
        $oResult->setAsk($oAsk);
        $oResult->setType('bilet');
        $oResult->setAnsId($iAnsId);
        $oResult->setUserId($oUser->getId());
        $oResult->setBiletId($this->oBilet->getId());

        if($oResult->_Validate()){            
            $this->oBiletSession->setAskResult( (int)$oResult->getResult() );
            $this->oBiletSession->Save();

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
        
        $this->oBiletSession->setNowAsk($iNumberAsk);
        
        Router::LocationAction('test/bilet/'. $this->oBilet->getId(). '/ask'. $this->oBiletSession->getNextAsk());
    }
    
    public function EventFinish() {
        
        if($this->oBiletSession){
            $this->oBiletSession->Drop();
        }
        
        if($this->oTest){
            Router::LocationAction('test/'.$this->oTest->getCode().'/bilets');
        }
        
        Router::LocationAction('/');

    }
    
}