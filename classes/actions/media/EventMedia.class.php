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


    public function EventMediaLoadGallery()
    {
        

        $sType = getRequestStr('target_type');
        $sId = getRequestStr('target_id');
        $sTmp = getRequestStr('target_tmp');
        $iPage = (int)getRequestStr('page');
        $iPage = $iPage < 1 ? 1 : $iPage;

        /**
        * Получаем все медиа, созданные пользователем без учета временных
        */
        $aResult = $this->Media_GetMediaItemsByFilter(array(
            'user_id'       => $this->oUserCurrent->getId(),
            '#with'         => ['targets'],
            '#page'         => array($iPage, 12),
            '#group'        => 'id',
            '#order'        => array('id' => 'desc')
        ));
        
        $aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, 12, Config::Get('pagination.pages.count'), null);
        $aMediaItems = $aResult['collection'];

        $oViewer = $this->Viewer_GetLocalViewer();
        $sTemplate = '';
        $oViewer->Assign('items', $aMediaItems, true);
        $oViewer->Assign('paging', $aPaging, true);
        $sTemplate .= $oViewer->Fetch('component@bs-media.files');
        
        $this->Viewer_AssignAjax('html', $sTemplate);
    }

    
    public function EventMediaUpload()
    {
        
        /**
         * Файл был загружен?
         */
        if (!isset($_FILES['files']['tmp_name'])) {
            $this->Message_AddError( $this->Lang_Get('plugin.media.uploader.notices.error_no_file'));
            return;
        }
        
        /**
         * Выясняем тип медиа
         */
        
        $this->Logger_Notice($sType);
        
        //$oMedia = $this->PluginMedia_Media_GetMediaFromRequest($_REQUEST);
        
        /**
         * Проверяем лимит медиа для пользователя
         */
        
        return;
        /**
         * Проверяем корректность target'а
         */
        $sTargetType = getRequestStr('target_type', "user");
        $sUserId = getRequestStr('user_id', getRequestStr('target_id', 0));

        /**
         * TODO: необходима проверка на максимальное общее количество файлов, на максимальный размер файла
         * Эти настройки можно хранить в конфиге: module.media.type.topic.max_file_count=30 и т.п.
         */
        $oMedia = $this->Media_GetMediaByFilter([
            'target_type'   => $sTargetType,
            'user_id'     => $sUserId,
            'file_name'     => substr($_FILES['filedata']['name'], 0, strrpos($_FILES['filedata']['name'], "."))
        ]);
        if($oMedia){
            $this->Message_AddError( $this->Lang_Get('media.uploader.notices.errorDublicate'));
            return;
        }
        /**
         * Выполняем загрузку файла
         */
        if ($mResult = $this->Media_Upload($_FILES['filedata'], $sTargetType , $sUserId) and is_object($mResult)) {
            $this->Viewer_AssignAjax('iMediaId', $mResult->getId());
        } else {
            $this->Message_AddError(is_string($mResult) ? $mResult : $this->Lang_Get('common.error.system.base'),
                $this->Lang_Get('common.error.error'));
        }
    }
    
    public function EventMediaGenerateTargetTmp()
    {
        $sType = getRequestStr('type');
        if ($this->Media_IsAllowTargetType($sType)) {
            $sTmp = func_generator();
            $this->Session_SetCookie('media_target_tmp_' . $sType, $sTmp, time() + 24 * 3600);
            $this->Viewer_AssignAjax('sTmpKey', $sTmp);
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
