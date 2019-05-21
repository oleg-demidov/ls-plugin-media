<?php

class PluginMedia_ModuleMedia_EntityImage extends Entity
{


    /**
     * Возвращает URL до файла нужного размера, в основном используется для изображений
     *
     * @param null $sSize
     *
     * @return null
     */
    public function getWebPath($sSize = null)
    {
        if ($this->getMedia()->getPath()) {
            return $this->PluginMedia_Media_GetFileWebPath($this->getMedia(), $sSize);
        } else {
            return null;
        }
    }
}