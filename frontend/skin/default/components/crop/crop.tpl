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

{$component = 'bs-crop'}
{component_define_params params=[ 'desc', 'oBehavior',  'title', 'mods', 'classes', 'attributes' ]}

{$attributes = ["data-cropper" => true]}

{if $oBehavior}
    {$attributes["data-aspect-ratio"] = $oBehavior->getParam('crop_aspect_ratio')}
    {$attributes["data-param-size-name"] = $oBehavior->getParam('crop_size_name')}
{/if}

{block 'crop_modal_options'}{/block}
{$desc = $desc|escape}

{if $desc}
    <p class="{$component}-desc">{$desc}</p>
{/if}
<div class="w-100 d-flex justify-content-center cropper-wrapper">
    <img src="{$image|escape}?v{mt_rand()}"
     class="w-100 d-none {$component}-image" {cattr list=$attributes}>
</div>


