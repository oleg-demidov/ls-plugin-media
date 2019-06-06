
{component_define_params params=[ 'oMedia', 'alt', 'attributes', 'classes', 'mods' ]}

{if $oMedia}
    {$img = $oMedia->getObject()->getWebPath('100x100crop')}
    {$attributes['data-id'] = $oMedia->getId()}
{/if}


<div class="m-1 {$classes} position-relative media-item" data-media-item {cattr list=$attributes} {if $img}style="background-image: url({$img});"{/if}>
    <div class="checker p-1">
        {component "bs-icon" icon="check" display="s"}
    </div>
    
    {if $oMedia}
        <div class="media-info">
            <div class="d-flex flex-column p-1 w-100">
                <div data-media-name>{$oMedia->getName()}</div>
                <div data-media-type>{*{lang "plugin.media.library.type.{$oMedia->getType()}"}*}</div>
                <div class="d-flex justify-content-between">
                    <div class="text-muted">{$aLang.plugin.media.media.added}:</div>
                    <div  class="pl-2" data-media-author><em>{$oMedia->getAuthor()->getLogin()}</em></div>
                    <div  class="pl-2" data-media-date><small>{$oMedia->getDateCreateFormat()}</small></div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>размер</div>
                    <div class="pl-2 align-self-end">
                        {component "bs-button" 
                            bmods="success" 
                            text=$aLang.plugin.media.library.button_select.text}
                    </div>
                </div>
            </div>
        </div>
    {/if}
    
    <button type="button" class="close media-close text-danger mr-1 d-none" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    {component "bs-progressbar" 
        height=10 
        classes="w-100 mx-2 progress-media d-none" 
    }
</div>