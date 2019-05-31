
{component_define_params params=[ 'targetType', 'targetId', 'targetTmp', 'name', 'attributes', 'url', 'isMultiple' ]}

{$attributes['data-uploder'] = true}
{$attributes['data-param-target_type'] = $targetType}
{$attributes['data-param-target_id'] = $targetId}
{$attributes['data-param-security_ls_key'] = $LIVESTREET_SECURITY_KEY}
{$attributes['data-url'] = {$url|default:{router page="media/upload"}}}
{$attributes['data-uploader'] = true}


<div class="{$classes} uploader  w-100" {cattr list=$attributes}>
    
    {* Drag & drop зона *}
    <label class="media-upload-area h-100 p-2 py-3 text-center mb-0" data-upload-area>
        <div class="d-flex flex-column align-items-center"> 
            <span class="btn btn-primary">{component "bs-icon" icon="upload" display="s"} {$label|default:{lang name='plugin.media.uploader.label'}}</span>
            <span class="mt-2">{lang name='plugin.media.uploader.max_size' max_size=$iMaxSizeUpload}</span>
        </div>
        <input data-file-input type="file" name="{$name|default:'file'}"  {$isMultiple|default:'multiple'}>
    </label>
    
    {component "media:media.item" classes="media-tpl d-none" }
    
</div>
