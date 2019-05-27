
{component_define_params params=[ 'targetType', 'targetId', 'targetTmp', 'name', 'attributes', 'url', 'isMultiple' ]}

{$attributes['data-uploder'] = true}
{$attributes['data-param-target_type'] = $targetType}
{$attributes['data-param-target_id'] = $targetId}
{$attributes['data-param-security_ls_key'] = $LIVESTREET_SECURITY_KEY}
{$attributes['data-url'] = {$url|default:{router page="media/upload"}}}
{$attributes['data-uploader'] = true}


<div class="{$classes} uploader p-2 media-item" {cattr list=$attributes}>
    
    {* Drag & drop зона *}
    <label class="media-upload-area h-100 p-2 text-center mb-0" data-upload-area>
        <div class="d-flex flex-column"> 
            <span class="btn btn-primary">{component "bs-icon" icon="upload" display="s"} {$label|default:{lang name='plugin.media.uploader.label'}}</span>
            <span class="mt-2">{lang name='plugin.media.uploader.max_size' max_size=$iMaxSizeUpload}</span>
        </div>
        <input data-file-input type="file" name="{$name|default:'file'}"  {$isMultiple|default:'multiple'}>
    </label>
    
    <div class="d-none row mt-2" data-media-list>
        <div class="col-12 col-md-6">
            <div class="name-file">
    
            </div>
        </div>
        
        <div class="col-11 col-md-5">
            <div class="progress mt-1 w-100">
                <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>        
        </div>
        <div class="col-1 col-md-1">
            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
</div>
