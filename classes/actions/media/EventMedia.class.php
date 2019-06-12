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
        
        /**
         * Файл был загружен?
         */
        if (!isset($_FILES['file']['tmp_name'])) {
            $this->Message_AddError( $this->Lang_Get('plugin.media.uploader.notices.error_no_file'));
            return;
        }
        
        /**
         * Создаем медиа
         */       
        $oMedia = Engine::GetEntity('PluginMedia_Media_Media', $_FILES['file']);
        $oMedia->setUserId($this->oUserCurrent->getId());
        /*
         * Проверяем 
         */
        $oMedia->_setValidateScenario('upload');
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
         * Загружаем
         */       
        if ($mResult = $this->PluginMedia_Media_Upload($oMedia) and is_object($mResult)) {
            $oViewer = $this->Viewer_GetLocalViewer();
            $oViewer->Assign('oMedia', $mResult, true);
            $sTemplate = $oViewer->Fetch('component@media:media.item');
            $this->Viewer_AssignAjax('html', $sTemplate);
            
        } else {
            $this->Message_AddError(is_string($mResult) ? $mResult : $this->Lang_Get('common.error.system.base'),
                $this->Lang_Get('common.error.error'));
        }
       
    }
    
    
    
//    public function EventMediaSubmitInsert()
//    {
//        $aIds = array(0);
//        foreach ((array)getRequest('ids') as $iId) {
//            $aIds[] = (int)$iId;
//        }
//
//        if (!($aMediaItems = //$this->Media_GetAllowMediaItemsById($aIds))) {
//            $this->Message_AddError($this->Lang_Get('media.error.need_choose_items'));
//            return false;
//        }
//
//        $aParams = array(
//            'align'        => getRequestStr('align'),
//            'size'         => getRequestStr('size', '500x'),
//            'relative_web' => true
//        );
//        /**
//         * Если изображений несколько, то генерируем идентификатор группы для лайтбокса
//         */
//        if (count($aMediaItems) > 1) {
//            $aParams['lbx_group'] = rand(1, 100);
//        }
//
//        $sTextResult = '';
//        foreach ($aMediaItems as $oMedia) {
//            $sTextResult .= //$this->Media_BuildCodeForEditor($oMedia, $aParams) . "\r\n";
//        }
//        $this->Viewer_AssignAjax('sTextResult', $sTextResult);
//    }

}
