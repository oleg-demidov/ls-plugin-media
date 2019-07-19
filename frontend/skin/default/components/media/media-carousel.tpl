
{component_define_params params=[ 'aMedia', 'attributes', 'classes', 'mods', 'previewSize' ]}

{if  is_array($aMedia) and count($aMedia)}
    {$previewSize = {$previewSize|default:"x200crop"}}
    {$items = []}
    {foreach $aMedia as $oMedia}
        {$items[] = [
            href    => $oMedia->getFileWebPath(),
            src     => $oMedia->getFileWebPath($previewSize)
        ]}
    {/foreach}

    {component 'bs-carousel' 
        classes="{$classes}" 
        indicators=true
        controls=true  
        items=$items}
{/if}
