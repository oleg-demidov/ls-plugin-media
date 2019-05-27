<?php

class PluginMedia_ModuleMedia extends ModuleORM
{
    
    const MEDIA_TYPE_IMAGE = 'image';
    const MEDIA_TYPE_VIDEO = 'video';
    const MEDIA_TYPE_DOC = 'doc';
    
    /*
     * Списки типов файлов соответствующие типам модуля
     */
    protected $aTypesAllow = [
        self::MEDIA_TYPE_IMAGE => [
            'image/jpeg'
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
        /*
         * Копируем во временное место для обработки
         */      
        $sDirTmp = Config::Get('path.tmp.server') . '/media/tmp/';
        
        if (!is_dir($sDirTmp)) {
            if(!mkdir($sDirTmp, 0777, true)){
                return $this->Lang_Get('media.error.upload');
            }
        }
        
        $sFileTmp = $sDirTmp . $oMedia->getName();
        if (!move_uploaded_file($oMedia->getTmpName(), $sFileTmp)) {
            return $this->Lang_Get('media.error.upload');
        }
        
        $oMedia->setPath($sFileTmp);
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

        if (preg_match('#^(\d+)?(x)?(\d+)?([a-z]{2,10})?$#Ui', $sSize, $aMatch)) {
            $iW = (isset($aMatch[1]) and $aMatch[1]) ? $aMatch[1] : null;
            $iH = (isset($aMatch[3]) and $aMatch[3]) ? $aMatch[3] : null;
            $bDelim = (isset($aMatch[2]) and $aMatch[2]) ? true : false;
            $sMod = (isset($aMatch[4]) and $aMatch[4]) ? $aMatch[4] : '';

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
            if ($sSize and $this->GetConfigParam('image.autoresize',
                    $oMedia->getType()) and !$this->Image_IsExistsFile($this->GetImagePathBySize($oMedia->getPath(),
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
    
}
