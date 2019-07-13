<?php

/**
 * Description of ActionProfile_EventSettings
 *
 * @author oleg
 */
class PluginMedia_ActionMedia_EventMedia extends Event {
    
    public $oUserCurrent;


    public function Init() {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent = $this->User_GetUserCurrent()) {
            $this->Message_AddErrorSingle($this->Lang_Get('common.error.need_authorization'), $this->Lang_Get('common.error.error'));
            return;
        }
        
        $this->Viewer_SetResponseAjax('json');
    }
    
    

    public function EventRemove()
    {
        
        $sId = getRequestStr('id');
        if (!$oMedia = $this->PluginMedia_Media_GetMediaById($sId)) {
            $this->Message_AddError($this->Lang('common.error'));
            return;
        }
        
        if($oMedia->getAuthor()->getId() != $this->oUserCurrent->getId()){
            $this->Message_AddError($this->Lang('plugin.media.media.notices.error_remove_access'));
            return;
        }
        
        if ($aMediaTargets = $this->PluginMedia_Media_GetTargetItemsByFilter(['media_id' => $oMedia->getId()])) {
            foreach ($aMediaTargets as $oMediaTarget) {
                $oMediaTarget->Delete();
            }
        }
        
        if (!$oMedia->Delete()) {
            $this->Message_AddErrorSingle(is_string($res) ? $res : $this->Lang_Get('common.error.system.base'));
        }else{
            $this->Message_AddNotice($this->Lang_Get('common.success.remove'));
        }
    }


    public function EventLoad()
    {
        
        $iPage = (int)getRequestStr('page');
        $iPage = $iPage < 1 ? 1 : $iPage;

        if(getRequestStr('author')){
            $oUser = $this->User_GetUserByLogin(getRequestStr('author'));
        }
        
        if(!isset($oUser)){
            $oUser = $this->oUserCurrent;
        }
        
        $aFilter = [
            'user_id'       => $oUser->getId(),
            '#page'         => array($iPage, Config::Get('plugin.media.library.per_page')),
            '#order'        => ['date_create' => 'desc']
        ];
        
        if(getRequestStr('type') and getRequestStr('type') != 'all'){
            $aFilter['type'] =  getRequestStr('type');
        }
        
        if(getRequest('order')){
            $aFilter['#order'] = [
                explode('-', getRequest('order'))[0] => explode('-', getRequest('order'))[1]
            ];
        }
        
        /**
        * Получаем все медиа, созданные пользователем 
        */
        $aResult = $this->PluginMedia_Media_GetMediaItemsByFilter($aFilter);
        
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('plugin.media.library.per_page'), 
                Config::Get('pagination.pages.count'), null);
        $aMedias = $aResult['collection'];

        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('aMedias', $aMedias, true);
        $oViewer->Assign('aPaging', $aPaging, true);
        $sTemplate = $oViewer->Fetch('component@media:media.list');
        
        $this->Viewer_AssignAjax('html', $sTemplate);
        $this->Viewer_AssignAjax('moreCount', $aResult['count'] - (Config::Get('plugin.media.library.per_page')*$iPage));
    }
    

    
    
    public function EventUpload()
    {
        if(isset($_FILES['file'])){
            $oPostFile = Engine::GetEntity(PluginMedia_ModuleMedia_EntityUploadPost::class, $_FILES['file']);
            
        }elseif(getRequest('url')){
            $oPostFile = Engine::GetEntity(PluginMedia_ModuleMedia_EntityUploadUrl::class, [
                'url' => getRequest('url')
            ]);
        }
        
        if(!$oPostFile->_Validate()){
            $this->Message_AddError($oPostFile->_getValidateError());
            return;
        }
        /**
         * Создаем медиа
         */ 
        $oMedia = Engine::GetEntity('PluginMedia_Media_Media', $oPostFile->_getData());
        $oMedia->setUserId($this->oUserCurrent->getId());       
        
        /*
         * Проверяем 
         */
        $oMedia->_setValidateScenario('create');
        if(!$oMedia->_Validate()){
            $this->Message_AddError( $oMedia->_getValidateError());
            return;
        }
        
         /**
         * Проверяем лимит медиа для пользователя
         */
        $iUserMediaCount = $this->PluginMedia_Media_GetCountFromMediaByFilter(['user_id' => $this->oUserCurrent->getId()]);
        $iMaxCount = $this->PluginMedia_Media_GetConfigParam( 'max_user_count', $oMedia->getType());
        if($iUserMediaCount >= $iMaxCount and !$this->oUserCurrent->isAdmin()){
            $this->Message_AddError( $this->Lang_Get('plugin.media.uploader.notices.error_upload_count', ['count' => $iMaxCount]));
            return;
        }
        /*
         * Сохраняем
         */       
        if ($mResult = $this->PluginMedia_Media_Upload($oMedia) and is_object($mResult)) {
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('oMedia', $mResult, true);
            $sTemplate = $oViewer->Fetch('component@media:media.'.$mResult->getType());
            $this->Viewer_AssignAjax('html', $sTemplate);
            
        } else {
            $this->Message_AddError(is_string($mResult) ? $mResult : $this->Lang_Get('common.error.system.base'),
                $this->Lang_Get('common.error.error'));
        }
       
    }
    
    
    public function EventCropImage() {
        if(!$oMedia = $this->PluginMedia_Media_GetMediaById(getRequest('id'))){
            $this->Message_AddError( $this->Lang_Get('plugin.media.library.notices.error_no_media'));
            return;
        }

        if(($sResult = $this->PluginMedia_Media_NewSizeFromCrop($oMedia, getRequest('size'), getRequest('canvasWidth'), 
                getRequest('sizeName')) ) !== true){
            $this->Message_AddError($sResult);
        }
        
        $sPreviewSize = getRequest('sizeName').'_preview';
        if(($sResult = $this->PluginMedia_Media_NewSizeFromCrop($oMedia, getRequest('size'), getRequest('canvasWidth'), 
                $sPreviewSize, [null, 100]) ) !== true){
            $this->Message_AddError($sResult);
        }
       
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oMedia', $oMedia, true);
        $oViewer->Assign('size', $sPreviewSize, true);
        $sMedia = $oViewer->Fetch('component@media:media.image');
        $this->Viewer_AssignAjax('html', $sMedia);
    }
    
    public function EventFormInsert()
    {
        if(!$oMedia = $this->PluginMedia_Media_GetMediaById(getRequest('id'))){
            $this->Message_AddError( $this->Lang_Get('plugin.media.library.notices.error_no_media'));
            return;
        }
        
        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oMedia', $oMedia, true);
        $sForm = $oViewer->Fetch('component@media:media.form-insert-'. $oMedia->getType() );
        $this->Viewer_AssignAjax('html', $sForm);
        
        $sTemplate = $oViewer->Fetch('component@media:media.template-'. $oMedia->getType() );
        $this->Viewer_AssignAjax('template', $sTemplate);
    }

}
