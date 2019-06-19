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
    
    public function getWebPathOriginal()
    {
        return $this->getWebPath(Config::Get('plugin.media.image.original'));
    }
    
    public function getSizes() {
        return $this->PluginMedia_Media_GetImageSizes();
    }
    
    public function getWebPathAll() {
        $aPath = [];
        
        foreach ($this->getSizes() as $sSize) {
            $aPath[$sSize] = $this->getWebPath($sSize);
        }
        
        return $aPath;
    }
    
}