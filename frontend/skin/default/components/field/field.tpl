 
{extends "component@bs-form.field"}

{component_define_params params=[ 'multiple', 'oBehavior']}

{block name="field_options"}
    {$multiple = $multiple|default:false}
    {$label = {lang name=$oBehavior->getParam('field_label')}}
    {$attributesGroup['data-media-field'] = true}
    {$attributesGroup['data-multiple'] = "{if $multiple}true{else}false{/if}"}
    {$attributesGroup['data-crop'] = "{if $oBehavior->getParam('crop')}true{else}false{/if}"}
    
    {$validateRules['type'] = "number"}
    {$validateRules['min'] = $oBehavior->getParam('validate_min')}
    {$validateRules['max'] = $oBehavior->getParam('validate_max')}
    {$validate['msgError'] = {lang 
        name=$oBehavior->getParam('validate_msg') 
        min=$validateRules['min'] 
        max=$validateRules['max']}}
{/block}





{block name="field_input"}

    {$aMedias = $oBehavior->getMedia()}

    <input class="d-none form-control" data-media-count-field name="{$oBehavior->getParam('field_name')}_count" 
        {cattr list=$validateRules} value="{$aMedias|@sizeof}">
    
    {capture name="content"}
        
        {foreach $aMedias as $oMedia}
            {component "media:media.item" oMedia=$oMedia}
        {/foreach}

        
    {/capture}
    
    {capture name="footer"}
      
        {if $multiple}
            {component "bs-button.group" 
                items =[ 
                    [
                        attributes  => ["data-media-count" => true],
                        bmods       => "outline-primary", 
                        text        => {$aMedias|sizeof}
                    ],
                    [
                        attributes  => ["data-add-btn" => true],
                        bmods       => "outline-primary", 
                        icon        => "plus", 
                        text        => $aLang.common.add
                    ]
            ]}
        {else}
            {component "bs-button" 
                attributes  = ["data-add-btn" => true]
                bmods       = "outline-primary"
                icon        = "plus" 
                text        = $aLang.plugin.media.media.choose_btn
            }
        {/if}
        
            
        {component "bs-button" 
            attributes  = ["data-remove-btn" => true] 
            bmods       = "outline-danger"
            classes     = "d-none"
            icon        = "minus" 
            text        = $aLang.common.remove}
        
    {/capture}
    {component "bs-card" content=[
        [   
            type => 'body',
            attributes => ['data-field-body' => true],
            classes => "media-short d-flex flex-wrap p-3",
            content => $smarty.capture.content
        ],
        [
            type => 'footer',
            content => $smarty.capture.footer,
            classes => 'p-3'
        ]
    ]}
    
        
{/block}