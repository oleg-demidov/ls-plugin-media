
{component_define_params params=[ 'name', 'attributes', 'classes', 'mods' ]}



<div class="{$classes} mt-2 file-upload position-relative" data-file-tpl {cattr list=$attributes}>
    <div class=" position-absolute w-100 h-100 progress-info">
            <span class="text-white" data-file-name>{$name}</span>
            <button type="button" class="close media-close text-danger mr-1" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
    </div>
            
    <div class="progress progress-container h-100 bg-secondary">
        <div class="progress-bar bg-success " role="progressbar" data-progress  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>
