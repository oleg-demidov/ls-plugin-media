
{component_define_params params=[ 'aMedias', 'attributes', 'classes', 'mods' ]}

{foreach $aMedias as $oMedia}
    {component "media:media.item" oMedia = $oMedia}
{/foreach}

{if !$aMedias}
    {component "blankslate" classes="w-100 mt-1" text=$aLang.plugin.media.library.blankslate}
{/if}

