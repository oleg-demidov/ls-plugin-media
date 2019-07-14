<?php

class PluginMedia_ModuleMedia extends ModuleORM
{
    
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_DOC = 'doc';
    
    /*
     * Списки типов файлов допустимые к загрузке
     */
    protected $aTypesAllow = [
        self::MEDIA_TYPE_IMAGE => [
            'image/jpeg',
            'image/png'
        ]
    ];
    
    
    /**
     * Инициализация
     *
     */
    public function Init()
    {
        parent::Init();
    }

    public static function getMediaTypesAllow(){
        return $this->aTypesAllow;
    }

    public function CheckMediaType($sType) {
        foreach ($this->aTypesAllow as $sKeySection => $aTypeSection) {
            if(in_array($sType, $aTypeSection)){
                return $sKeySection;
            }
        }
        return false;
    }
    
    /**
     * Возвращает каталог для сохранения контента медиа
     *
     * @param string $sType
   
     *
     * @return string
     */
    public function GetSaveDir($sType, $sPostfix = '')
    {
        $sPostfix = trim($sPostfix, '/');
        return Config::Get('path.uploads.base') . "/media/{$sType}/" . date('Y/m/d/H/') . ($sPostfix ? "{$sPostfix}/" : '');
    }
    
    public function Upload($oMedia) {
        
        /**
         * Определяем тип медиа по файлу и запускаем обработку
         */
        switch ($oMedia->getType()) {
            case self::MEDIA_TYPE_IMAGE:
                $mResult = $this->ProcessingFileImage($oMedia);
                break;
            default :
                $mResult = $this->Lang_Get('plugin.media.uploader.notices.error_no_type', ['type' => $oMedia->getType()]);
        }
        
        if(is_string($mResult)){
            return $mResult;
        }
        
        return $oMedia;
    }
    
    public function ProcessingFileImage($oMedia)
    {
        $aParams = $this->Image_BuildParams();
        /**
         * Если объект изображения не создан, возвращаем ошибку
         */
        if (!$oImage = $this->Image_Open($oMedia->getPath(), $aParams)) {
            $this->Fs_RemoveFileLocal($oMedia->getPath());
            return $this->Image_GetLastError();
        }
        $iWidth = $oImage->getWidth();
        $iHeight = $oImage->getHeight();

        $sPath = $this->GetSaveDir(self::MEDIA_TYPE_IMAGE);
        /**
         * Уникальное имя файла
         */
        $sFileName = func_generator(20);
        /**
         * Сохраняем оригинальную копию
         * Оригинал храним без вотермарка
         */
        $sFileResult = null;
        $mOriginalSize = $this->GetConfigParam( 'original', self::MEDIA_TYPE_IMAGE);
        if ($mOriginalSize !== false && $oImage->getFormat() == 'gif') {
            /**
             * Если gif, то сохраняем без изменений
             */
            if (!$sFileResult = $oImage->saveOriginalSmart($sPath, $sFileName)) {
                $this->Fs_RemoveFileLocal($oMedia->getPath());
                return $this->Image_GetLastError();
            }
        } else {
            if (!is_string($mOriginalSize) ) {
                if (!$sFileResult = $oImage->saveSmart($sPath, $sFileName, array('skip_watermark' => true))) {
                    $this->Fs_RemoveFileLocal($oMedia->getPath());
                    return $this->Image_GetLastError();
                }
            } else {
                /**
                 * Ресайзим оригинал
                 */
                $aOriginalSize = $this->ParsedImageSize($mOriginalSize);
                if ($aOriginalSize['crop']) {
                    $oImage->cropProportion($aOriginalSize['w'] / $aOriginalSize['h'], 'center');
                }
                if (!$sFileResult = $oImage->resize($aOriginalSize['w'], $aOriginalSize['h'], true)->saveSmart($sPath, $sFileName,
                    array('skip_watermark' => true))
                ) {
                    $this->Fs_RemoveFileLocal($oMedia->getPath());
                    return $this->Image_GetLastError();
                }
                
            }
        }
        $oMedia->setSize($this->Fs_GetFileSize($sFileResult));
        $oMedia->setPath($sFileResult);
        
        /**
         * Перед запуском генерации подчищаем память
         */
        unset($oImage);
        
        /**
         * Генерируем варианты с необходимыми размерами
         */
        $aSizes = $this->GetConfigParam('sizes', self::MEDIA_TYPE_IMAGE);
        
        $sFileResultLast = $this->GenerateImageBySizes($oMedia->getPath(), $sPath, $sFileName, $aSizes, $aParams);
        
        /**
         * Сохраняем медиа
         */
        $oMedia->setDataOne('sizes', $aSizes);
        /**
         * Теперь можно удалить временный файл
         */
//        $this->Fs_RemoveFileLocal($oMedia->getPath());
        /**
         * Добавляем в БД
         */
        return $oMedia->Add();
    }
    
    /**
     * Создает набор отресайзанных изображений
     * Варианты наименований результирующих файлов в зависимости от размеров:
     *    file_100x150 - w=100 h=150 crop=false
     *    file_100x150crop - w=100 h=150 crop=true
     *    file_x150 - w=null h=150 crop=false
     *    file_100x - w=100 h=null crop=false
     *
     * @param      $sFileSource
     * @param      $sDirDist
     * @param      $sFileName
     * @param      $aSizes
     * @param null $aParams
     */
    public function GenerateImageBySizes($sFileSource, $sDirDist, $sFileName, $aSizes, $aParams = null)
    {
        if (!$aSizes) {
            return;
        }
        /**
         * Преобразуем упрощенную запись списка размеров в полную
         */
        foreach ($aSizes as $k => $v) {
            if (!is_array($v)) {
                $aSizes[$k] = $this->ParsedImageSize($v);
            }
        } 
        $sFileResult = null;
        foreach ($aSizes as $aSize) {
            /**
             * Для каждого указанного в конфиге размера генерируем картинку
             */
            $sNewFileName = $sFileName . '_' . $aSize['w'] . 'x' . $aSize['h'];
            if ($oImage = $this->Image_Open($sFileSource, $aParams)) {
                if ($aSize['crop']) {
                    $oImage->cropProportion($aSize['w'] / $aSize['h'], 'center');
                    $sNewFileName .= 'crop';
                }
                if (!$sFileResult = $oImage->resize($aSize['w'], $aSize['h'], true)->saveSmart($sDirDist,
                    $sNewFileName)
                ) {
                    // TODO: прерывать и возвращать false?
                }
            }
        }
        /**
         * Возвращаем путь до последнего созданного файла
         */
        return $sFileResult;
    }
 
    /**
     * Возвращает параметр конфига с учетом текущего target_type
     *
     * @param string $sParam Ключ конфига относительно module.media
     *
     * @return mixed
     */
    public function GetConfigParam($sParam, $sType)
    {
        $mValue = Config::Get("plugin.media.{$sType}.{$sParam}");
        if (!$mValue) {
            $mValue = Config::Get("plugin.media.{$sParam}");
        }
        return $mValue;
    }
    
    /**
     * Парсит строку с размером изображения
     * Варианты входной строки:
     * 100
     * 100crop
     * 100x150
     * 100x150crop
     * x150
     * 100x
     *
     * @param string $sSize
     *
     * @return array    Массив вида array('w'=>100,'h'=>150,'crop'=>true)
     */
    public function ParsedImageSize($sSize)
    {
        $aSize = array(
            'w'    => null,
            'h'    => null,
            'crop' => false,
        );

        if (preg_match('#^([\D\d]{5,}\_)?(\d+)?(x)?(\d+)?([a-z]{2,10})?$#Ui', $sSize, $aMatch)) {
            $iW = (isset($aMatch[2]) and $aMatch[2]) ? $aMatch[2] : null;
            $iH = (isset($aMatch[4]) and $aMatch[4]) ? $aMatch[4] : null;
            $bDelim = (isset($aMatch[3]) and $aMatch[3]) ? true : false;
            $sMod = (isset($aMatch[5]) and $aMatch[5]) ? $aMatch[5] : '';

            if (!$bDelim) {
                $iW = $iH;
            }
            $aSize['w'] = $iW;
            $aSize['h'] = $iH;
            if ($sMod) {
                $aSize[$sMod] = true;
            }
        }
        return $aSize;
    }
    
    public function GetFileWebPath($oMedia, $sSize = null)
    {
        if ($oMedia->getType() == self::MEDIA_TYPE_IMAGE) {
            /**
             * Проверяем необходимость автоматического создания превью нужного размера - если разрешено настройками и файл НЕ существует
             */
            if ($sSize and $this->GetConfigParam('image.autoresize', $oMedia->getType()) 
                    and !$this->Image_IsExistsFile($this->GetImagePathBySize($oMedia->getPath(),
                    $sSize))
            ) {             
                /**
                 * Запускаем генерацию изображения нужного размера
                 */
                $aSize = $this->ParsedImageSize($sSize);

                $aParams = $this->Image_BuildParams('media.' . $oMedia->getType());
                $sNewFileName = $this->GetImagePathBySize($oMedia->getPath(), $sSize);
                if ($oImage = $this->Image_OpenFrom($oMedia->getPath(), $aParams)) {
                    if ($aSize['crop']) {
                        $oImage->cropProportion($aSize['w'] / $aSize['h'], 'center');
                    }
                    $oImage->resize($aSize['w'], $aSize['h'], true)->save($sNewFileName);
                    /**
                     * Обновляем список размеров
                     */
                    $aSizeOld = (array)$oMedia->getDataOne('sizes');
                    $aSizeOld[] = $aSize;
                    $oMedia->setDataOne('sizes', $aSizeOld);
                    $oMedia->Update();
                }
            }
            return $this->GetImageWebPath($oMedia->getPath(), $sSize);
        }
        return null;
    }
    
    /**
     * Возвращает веб путь до файла изображения
     *
     * @param $sPath
     * @param null $sSize
     * @return string
     */
    public function GetImageWebPath($sPath, $sSize = null)
    {
        $sPath = $this->Fs_GetPathWeb($sPath);
        if ($sSize) {
            return $this->GetImagePathBySize($sPath, $sSize);
        } else {
            return $sPath;
        }
    }
    
    /**
     * Возвращает путь до изображения конкретного размера
     * Варианты преобразования размеров в имя файла:
     *    100 - file_100x100
     *    100crop - file_100x100crop
     *    100x150 - file_100x150
     *  100x150crop - file_100x150crop
     *  x150 - file_x150
     *  100x - file_100x
     *
     * @param string $sPath
     * @param string $sSize
     *
     * @return string
     */
    public function GetImagePathBySize($sPath, $sSize)
    {
        $aPathInfo = pathinfo($sPath);
        if (is_array($sSize)) {
            $aSize = $sSize;
            $sSize = $aSize['w'] . 'x' . $aSize['h'];
            if ($aSize['crop']) {
                $sSize .= 'crop';
            }
        } else {
            if (preg_match('#^(\d+)([a-z]{2,10})?$#i', $sSize, $aMatch)) {
                $sSize = $aMatch[1] . 'x' . $aMatch[1];
                if (isset($aMatch[2])) {
                    $sSize .= strtolower($aMatch[2]);
                }
            }
        }
        
        return $aPathInfo['dirname'] . '/' . $aPathInfo['filename'] . '_' . $sSize . '.' . $aPathInfo['extension'];
    }

    /**
     * Выполняет удаление файлов медиа-объекта
     *
     * @param $oMedia
     */
    public function DeleteFiles($oMedia)
    {
        /**
         * Сначала удаляем все файлы
         */
        if ($oMedia->getType() == self::MEDIA_TYPE_IMAGE) {
            $aSizes = $oMedia->getDataOne('sizes');
            $this->RemoveImageBySizes($oMedia->getPath(), $aSizes);
        }
    }
    
    public function RemoveImageBySizes($sPath, $aSizes, $bRemoveOriginal = true)
    {
        if ($aSizes) {
            /**
             * Преобразуем упрощенную запись списка размеров в полную
             */
            foreach ($aSizes as $k => $v) {
                if (!is_array($v)) {
                    $aSizes[$k] = $this->ParsedImageSize($v);
                }
            }
            foreach ($aSizes as $aSize) {
                $sSize = $aSize['w'] . 'x' . $aSize['h'];
                if ($aSize['crop']) {
                    $sSize .= 'crop';
                }
                $this->Image_RemoveFile($this->GetImagePathBySize($sPath, $sSize));
            }
        }
        /**
         * Удаляем оригинал
         */
        if ($bRemoveOriginal) {
            $this->Image_RemoveFile($sPath);
        }
    }
    
    public function GetMaxSizeUpload($sType = '') {
        
        function file_upload_max_size() {
            static $max_size = -1;

            if ($max_size < 0) {
              // Start with post_max_size.
              $post_max_size = parse_size(ini_get('post_max_size'));
              if ($post_max_size > 0) {
                $max_size = $post_max_size;
              }

              // If upload_max_size is less, then reduce. Except if upload_max_size is
              // zero, which indicates no limit.
              $upload_max = parse_size(ini_get('upload_max_filesize'));
              if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
              }
            }
            return $max_size;
          }

          function parse_size($size) {
            $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
            $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
            if ($unit) {
              // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
              return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            }
            else {
              return round($size);
            }
          }
          
        $iMaxSize = $this->GetConfigParam( 'max_size', $sType);
        $iMaxSize2 = file_upload_max_size();
        
        
        return $iMaxSize>$iMaxSize2?$iMaxSize2:$iMaxSize;
    }
    
    
    public function GetImageSizes() {
        $aSizesConfig = Config::Get('plugin.media.image.sizes');
        
        $aSizes = [];
        foreach ($aSizesConfig as $aSizeConfig) {
            $aSizes[] = $this->GetImageSize($aSizeConfig);
        }
        
        return $aSizes;
    }
    
    public function GetImageSize($aSize) {
        return $aSize['w'] . 'x' . $aSize['h'] . ($aSize['crop']?'crop':'');
    }
    
    public function SaveMedias($sTargetType, $iTargetId, $aMedia) {
        
        $this->PluginMedia_Media_DeleteTargetItemsByFilter([
            'target_id' => $iTargetId,
            'target_type' => $sTargetType
        ]);
        
        
        foreach ($aMedia as $oMedia) {
            $oTargetMedia = Engine::GetEntity( "PluginMedia_Media_Target", [
                'media_id' => $oMedia->getId(),
                'target_type' => $sTargetType,
                'target_id' => $iTargetId
            ]);
            $oTargetMedia->Save();
        }
    }
        
    public function RemoveMedias($oTarget, $sTargetType) {
        $aTargets = $this->GetTargetItemsByFilter([
            'target_type' => $sTargetType,
            'target_id' => $oTarget->getId(),
        ]);
        
        foreach ($aTargets as $oTarget) {
            $oTarget->Delete();
        }
    }
    
    public function GetMedias($oTarget, $sTargetType) {
        $aTargets = $this->GetTargetItemsByFilter([
            'target_type' => $sTargetType,
            'target_id' => $oTarget->getId(),
            '#index-from' => 'media_id'
        ]);
        
        return $this->GetMediaItemsByFilter([
            'id in' => array_merge(array_keys($aTargets), [0])
        ]);
    }
    
    protected function getBehaviorThis($aFilterWith, $aBehaviors) {
        foreach ($aBehaviors as $sName => $oBehavior) {
            if(!($oBehavior instanceof PluginMedia_ModuleMedia_BehaviorModule)){
                continue;
            }
            if (!in_array('#'.$sName, $aFilterWith) and !array_key_exists('#'.$sName, $aFilterWith)) {
                continue;
            }
            $oBehavior->sName = $sName;
            return $oBehavior;
            
        }
        return false;
    }
    
    
    public function RewriteGetItemsByFilter($aResult, $aFilter, $aBehaviors)
    {
        
        if (!$aResult) {
            return;
        }
        
        /**
         * Проверяем необходимость цеплять media
         */
        if (isset($aFilter['#with'])) {
            $oBehavior = $this->getBehaviorThis($aFilter['#with'], $aBehaviors);
        }
        
        if(!isset($oBehavior) or !$oBehavior){
            return;
        }
        
        /**
         * Список на входе может быть двух видов:
         * 1 - одномерный массив
         * 2 - двумерный, если применялась группировка (использование '#index-group')
         *
         * Поэтому сначала сформируем линейный список
         */
        if (isset($aFilter['#index-group']) and $aFilter['#index-group']) {
            $aEntitiesWork = array();
            foreach ($aResult as $aItems) {
                foreach ($aItems as $oItem) {
                    $aEntitiesWork[] = $oItem;
                }
            }
        } else {
            $aEntitiesWork = $aResult;
        }

        if (!$aEntitiesWork) {
            return;
        }
        
        $this->AttachMediasForTargetItems($aEntitiesWork, $oBehavior);
    }
    
    public function AttachMediasForTargetItems($aEntityItems, $oBehavior)
    {
        if (!is_array($aEntityItems)) {
            $aEntityItems = array($aEntityItems);
        }
        $aEntitiesId = array();
        
        foreach ($aEntityItems as $oEntity) {
            $aEntitiesId[] = $oEntity->getId();
        }
        /**
         * Получаем media для всех объектов
         */
        $aMedias = $this->GetMediaItemsByFilter(array(
            '#join'        => array(
                "JOIN " . Config::Get('db.table.media_target') . " media_target ON
                t.id = media_target.media_id and
                media_target.target_type = ? and
                media_target.target_id IN ( ?a )
                " => array($oBehavior->getTargetType(), $aEntitiesId)
            ),
            '#select'      => array(
                't.*',
                'media_target.target_id'
            ),
            '#index-group' => 'target_id'
        ));
        /**
         * Собираем данные
         */
        foreach ($aEntityItems as $oEntity) {
            if (isset($aMedias[$oEntity->_getPrimaryKeyValue()])) {
                $oEntity->_setData(array($oBehavior->sName => $aMedias[$oEntity->_getPrimaryKeyValue()]));
            } else {
                $oEntity->_setData(array($oBehavior->sName => array()));
            }
        }
    }
    
     public function NewSizeFromCrop($oMedia, $aSize, $iCanvasWidth, $sNameCrop = 'cropped', $aSizes = null) {

        if(!$oImage = $this->Image_Open($oMedia->getPath() )){
            return $this->Image_GetLastError();
        }
        
        $oImage->cropFromSelected($aSize, $iCanvasWidth);
        
        if(is_array($aSizes)){
            call_user_func_array ( [$oImage, 'resize'] , $aSizes );
        }
        /**
         * Сохраняем
         */
        if (false === ($sFileResult = $oImage->save($this->GetImagePathBySize($oMedia->getPath(), $sNameCrop)))) {
            return $this->Image_GetLastError();
        }
        
        $aSizesData = $oMedia->getDataOne('image_sizes');
        
        
        if(is_array($aSizes)){
            $aSizesData[] = ['w' => $aSizes[0], 'h' => isset($aSizes[1])?$aSizes[1]:null, 'crop' => true];
        }else{
            $aSizesData[] = ['w' => $iCanvasWidth, 'h' => null, 'crop' => true];
        }
        
        $oMedia->setDataOne('image_sizes', $aSizesData);
        $oMedia->Save();
        
        return true;
    }
    
    public function AttachUserBehaviorAvatar($oUser) {
        $oUser->AttachBehavior('avatar', [
            'class' => 'PluginMedia_ModuleMedia_BehaviorEntity',
            'target_type' => 'useravatar',
            'crop' => true,
            'field_name' => 'useravatar',
            'field_label' => 'plugin.media.avatar.field_label',
            'crop_size_name' => 'useravatar'
        ]);
    }
    
    public function AddByUrl($oUser, $oBehavior, $sUrl) {
        
        $oUploadUrl = Engine::GetEntity(PluginMedia_ModuleMedia_EntityUploadUrl::class, [
            'url' => $sUrl
        ]);

        if(!$oUploadUrl->_Validate()){
            return false;
        }
        
        $oMedia = Engine::GetEntity('PluginMedia_Media_Media', $oUploadUrl->_getData());
        $oMedia->setUserId($oUser->getId());

        if(!$oMedia->_Validate()){
            return false;
        }
        
        $oMedia = $this->Upload($oMedia);
        
        
        if(!$oImage = $this->Image_Open($oMedia->getPath() )){
            return false;
        }
        
        $oImage->cropProportion($oBehavior->getParam('crop_aspect_ratio'));
        
        /**
         * Сохраняем
         */
        if (false === ($sFileResult = $oImage->save(
                $this->GetImagePathBySize($oMedia->getPath(), $oBehavior->getParam('crop_size_name'))))) {
            return false;
        }
        
        $oImage->resize(null, 100);
        
        if (false === ($sFileResult = $oImage->save(
                $this->GetImagePathBySize($oMedia->getPath(), $oBehavior->getParam('crop_size_name').'_preview')))) {
            return false;
        }
        
        $mResult = $this->GenerateImageBySizes(
            $this->GetImagePathBySize($oMedia->getPath(), $oBehavior->getParam('crop_size_name')), 
            dirname($this->Fs_GetPathRelativeFromServer($oMedia->getPath())), 
            basename(preg_replace('/\\.[^.\\s]{3,4}$/', '', $oMedia->getPath())).'_'.$oBehavior->getParam('crop_size_name'), 
            Config::Get('plugin.media.avatar.sizes')
        );

        $oUser->_setData([$oBehavior->getParam('field_name') => $oMedia->getId()]);      
        $oUser->_Validate([$oBehavior->getParam('field_name')]);
        $oUser->Save();
        
    }
    
    public function RemoveCroppedImages($oMedia, $sCropSizeName, $aSizes = []) {
        $sCropedPath = $this->GetImagePathBySize($oMedia->getPath(), $sCropSizeName);
        $aPath = [
            $sCropedPath,
            $this->GetImagePathBySize($sCropedPath, 'preview')
        ];
        foreach ($aSizes as $aSize) {
            $aPath[] = $this->GetImagePathBySize($sCropedPath, $aSize);
        }
        foreach ($aPath as $sPath) {
            $this->Fs_RemoveFileLocal($sPath);
        }
    }
}
