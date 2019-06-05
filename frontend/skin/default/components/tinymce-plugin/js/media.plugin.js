/**
 * LiveStreet
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Oleg Demidov
 */
jQuery(document).ready(function($){
    ls.hook.add('ls_template_init_start', function(){ 
        tinymce.PluginManager.add('lsmedia', function(editor, url) {

            // Вставка медиа-объектов
            editor.addButton('lsmedia', {
                icon: 'image',
                tooltip: ls.lang.get('plugin.media.library.button.tooltip'),
                onclick: function() {
                    $('#medialibrary').modal('show')
                }
            });
        });
    });
});

jQuery(document).ready(function($){
    ls.hook.add('ls_template_init_start', function(){ 
        ls.registry.get('component.tinimce.plugins').push('lsmedia');
        ls.registry.get('component.tinimce.toolbar').push('lsmedia');
    }, 100);
});
