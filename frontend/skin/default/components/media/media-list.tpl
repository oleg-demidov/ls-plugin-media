
{component_define_params params=[ 'aMedias', 'attributes', 'classes', 'mods' ]}

{foreach $aMedias as $oMedia}
    {component "media:media.item" oMedia = $oMedia}
{/foreach}


