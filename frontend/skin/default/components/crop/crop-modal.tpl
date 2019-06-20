{**
 * Обрезка загруженного изображения
 *
 * @param string  $title
 * @param string  $desc
 * @param string  $image
 * @param integer $width
 * @param integer $height
 * @param integer $originalWidth
 * @param integer $originalHeight
 *}

{component_define_params params=[ 'title', 'mods', 'classes', 'attributes', 'oBehavior' ]}


{component "bs-modal" 
    backdrop        = "static"
    header          = $title|escape|default:{lang 'crop.title'} 
    centered        = true 
    content         = {component 'media:crop' oBehavior=$oBehavior}
    closed          = false
    showFooter      = true
    primaryButton  = [
        'attributes' => ['data-toggle'=>"modal", 'data-target'=>"#cropModal"],
        'text'    => {lang 'common.save'},
        'bmods' => "success"
    ]
    id              = "cropModal"}

