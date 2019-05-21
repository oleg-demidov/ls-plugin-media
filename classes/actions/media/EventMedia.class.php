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
    
    

    public function EventMediaRemoveFile()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('common.error.need_authorization'), $this->Lang_Get('common.error.error'));
            return;
        }
        $sId = getRequestStr('id');
        if (!$oMedia = $this->Media_GetMediaById($sId)) {
            return $this->EventErrorDebug();
        }
        
        if ($aMediaTargets = $this->Media_GetTargetItemsByFilter(['media_id' => $oMedia->getId()])) {
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


    public function EventMediaLoad()
    {
        
        $iPage = (int)getRequestStr('page');
        $iPage = $iPage < 1 ? 1 : $iPage;

        /**
        * Получаем все медиа, созданные пользователем 
        */
        $aResult = $this->PluginMedia_Media_GetMediaItemsByFilter(array(
            'user_id'       => $this->oUserCurrent->getId(),
            '#page'         => array($iPage, 12),
        ));
        
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, 12, Config::Get('pagination.pages.count'), null);
        $aMedias = $aResult['collection'];

        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('aMedias', $aMedias, true);
        $oViewer->Assign('aPaging', $aPaging, true);
        $sTemplate = $oViewer->Fetch('component@media:media.list');
        
        $this->Viewer_AssignAjax('html', $sTemplate);
    }

    
    public function EventMediaUpload()
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
            $this->Viewer_AssignAjax('iMediaId', $mResult->getId());
        } else {
            $this->Message_AddError(is_string($mResult) ? $mResult : $this->Lang_Get('common.error.system.base'),
                $this->Lang_Get('common.error.error'));
        }
       
    }
    
    public function EventMediaLoadGalleryOld()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('common.error.need_authorization'), $this->Lang_Get('common.error.error'));
            return;
        }

        $sType = getRequestStr('target_type', 'user');
        $sId = getRequestStr('target_id', $this->oUserCurrent->getId());
        $iPage = (int)getRequestStr('page');
        $iPage = $iPage < 1 ? 1 : $iPage;

        $aMediaItems = array();
        if ($sType) {
            /**
             * Получаем медиа для конкретного объекта
             */
            if ($sId) {
                
                $aMediaItems = $this->Media_GetMediaByTarget($sType, $sId);
            } 
        } else {
            /**
             * Получаем все медиа, созданные пользователем без учета временных
             */
            $aResult = $this->Media_GetMediaItemsByFilter(array(
                'user_id'       => $this->oUserCurrent->getId(),
                'mt.target_tmp' => null,
                '#page'         => array($iPage, 20),
                '#join'         => array(
                    'LEFT JOIN ' . Config::Get('db.table.media_target') . ' mt ON ( t.id = mt.media_id and mt.target_tmp IS NOT NULL ) ' => array(),
                ),
                '#group'        => 'id',
                '#order'        => array('id' => 'desc')
            ));
            $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, 20, Config::Get('pagination.pages.count'), null);
            $aMediaItems = $aResult['collection'];
            $this->Viewer_AssignAjax('pagination', $aPaging);
        }

        $oViewer = $this->Viewer_GetLocalViewer();
        $sTemplate = '';
        foreach ($aMediaItems as $oMediaItem) {
            $oViewer->Assign('oMediaItem', $oMediaItem);
            $sTemplate .= $oViewer->Fetch('component@uploader.file');
        }
        $this->Viewer_AssignAjax('html', $sTemplate);
        $this->Viewer_AssignAjax('count_loaded', count($aMediaItems));
        $this->Viewer_AssignAjax('page', count($aMediaItems) > 0 ? $iPage + 1 : $iPage);
    }
    
    public function EventMediaSubmitInsert()
    {
        $aIds = array(0);
        foreach ((array)getRequest('ids') as $iId) {
            $aIds[] = (int)$iId;
        }

        if (!($aMediaItems = $this->Media_GetAllowMediaItemsById($aIds))) {
            $this->Message_AddError($this->Lang_Get('media.error.need_choose_items'));
            return false;
        }

        $aParams = array(
            'align'        => getRequestStr('align'),
            'size'         => getRequestStr('size', '500x'),
            'relative_web' => true
        );
        /**
         * Если изображений несколько, то генерируем идентификатор группы для лайтбокса
         */
        if (count($aMediaItems) > 1) {
            $aParams['lbx_group'] = rand(1, 100);
        }

        $sTextResult = '';
        foreach ($aMediaItems as $oMedia) {
            $sTextResult .= $this->Media_BuildCodeForEditor($oMedia, $aParams) . "\r\n";
        }
        $this->Viewer_AssignAjax('sTextResult', $sTextResult);
    }

}
