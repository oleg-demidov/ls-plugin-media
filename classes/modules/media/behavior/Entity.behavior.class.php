<?php
/*
 * LiveStreet CMS
 * Copyright © 2013 OOO "ЛС-СОФТ"
 *
 * ------------------------------------------------------
 *
 * Official site: www.livestreetcms.com
 * Contact e-mail: office@livestreetcms.com
 *
 * GNU General Public License, version 2:
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * ------------------------------------------------------
 *
 * @link http://www.livestreetcms.com
 * @copyright 2013 OOO "ЛС-СОФТ"
 * @author Oleg Demidov
 *
 */

/**
 * Поведение, которое необходимо добавлять к сущности (entity) у которой добавляются категории
 *
 * @since 2.0
 */
class PluginMedia_ModuleMedia_BehaviorEntity extends Behavior
{
    /**
     * Дефолтные параметры
     *
     * @var array
     */
    protected $aParams = array(
        'field_name'                     => 'media',
        //Обрезать ли фото
        'crop'                           => false,
        // Имя размера для обрезанного фото
        'crop_size_name'                 => 'cropped',
        // Пропорции области вырезки
        'crop_aspect_ratio'              => 1/1,
        
        'field_label'                    => 'plugin.media.media.field_label',
        // Уникальный код
        'target_type'                    => null,
        // Автоматическая валидация media (актуально при ORM)
        'validate_enable'                => true,
        // Минимальное количество media, доступное для выбора
        'validate_min'                   => 0,
        // Максимальное количество media, доступное для выбора
        'validate_max'                   => 5,
        'validate_msg'                   => 'plugin.media.media.notices.error_validate_count'
        
    );
    /**
     * Список хуков
     *
     * @var array
     */
    protected $aHooks = array(
        'validate_after' => 'CallbackValidateAfter',
        'after_save'     => 'CallbackAfterSave',
        'after_delete'   => 'CallbackAfterDelete',
    );

    /**
     * Инициализация
     */
    protected function Init()
    {
        parent::Init();
        
    }

    /**
     * Коллбэк
     * Выполняется при инициализации сущности
     *
     * @param $aParams
     */
    public function CallbackValidateAfter($aParams)
    {        $this->Logger_Notice(print_r($aParams, true));
        if ($aParams['bResult'] and $this->getParam('validate_enable')) {
            $aFields = $aParams['aFields'];
            $oValidator = $this->Validate_CreateValidator('media', $this,
                $this->getParam('field_name'));
            $oValidator->validateEntity($this->oObject, $aFields);
            $aParams['bResult'] = !$this->oObject->_hasValidateErrors();
        }
    }

    /**
     * Коллбэк
     * Выполняется после сохранения сущности
     */
    public function CallbackAfterSave()
    {
        if($this->oObject->getMedia() == null){
            return;
        }
        
        $aMedia = $this->PluginMedia_Media_GetMedias($this->oObject, $this->getParam('target_type') );
        if ($this->getParam('crop') and $aMedia) {
            $this->PluginMedia_Media_RemoveCroppedImages(
                current($aMedia), 
                $this->getParam('crop_size_name'),
                Config::Get('plugin.media.avatar.sizes')
            );
        }
        
        $this->PluginMedia_Media_SaveMedias(
            $this->getParam('target_type'), 
            $this->oObject->getId(),
            $this->oObject->getMedia()?$this->oObject->getMedia():[]);
    }

    /**
     * Коллбэк
     * Выполняется после удаления сущности
     */
    public function CallbackAfterDelete()
    {
        $this->PluginMedia_Media_RemoveMedias($this->oObject, $this->getParam('target_type'));
    }

    public function getTargetType() {
        return $this->getParam('target_type');
    }
    /**
     * Дополнительный метод для сущности
     * Запускает валидацию дополнительных полей
     *
     * @param $mValue
     *
     * @return bool|string
     */
    public function ValidateMedia($mValue)
    {
        if (!$mValue) {
            $mValue = getRequest($this->getParam('field_name'), []);
        }
        
        $aMedia = $this->PluginMedia_Media_GetMediaItemsByFilter([
            'id in' => array_merge($mValue, [0]),
            '#index-from' => 'id'
        ]);
        
        
        if($this->getParam('validate_min') > sizeof($aMedia)){
            return $this->Lang_Get('plugin.media.media.notices.error_validate_count_min', [
                'min' => $this->getParam('validate_min'),
            ]);
        }
        if(sizeof($aMedia) > $this->getParam('validate_max')){
            return $this->Lang_Get('plugin.media.media.notices.error_validate_count_max', [
                'max' => $this->getParam('validate_max')
            ]);
        }
        
  
        $this->oObject->setMedia($aMedia);
        
        return true;
    }

    /**
     * Возвращает список 
     *
     * @return array
     */
    public function getMedia()
    {
        if($this->oObject->getMedia()){
            return $this->oObject->getMedia();
        }
        return $this->PluginMedia_Media_GetMedias($this->oObject, $this->getParam('target_type') );
    }    
}