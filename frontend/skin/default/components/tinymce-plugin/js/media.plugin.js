/**
 * LiveStreet
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Oleg Demidov
 */
ls.hook.add('ls_template_init_start', function(){ 
    tinymce.PluginManager.add('lsmedia', function(editor, url) {

        // Вставка медиа-объектов
        editor.addButton('lsmedia', {
            icon: 'image',
            tooltip: ls.lang.get('plugin.media.library.button.tooltip'),
            onclick: function() {
                $('[data-library]').mediaLibrary('chooseMedia', function(media){
                    console.log(media.mediaMedia('option', 'id'));
                    $('#mediaInsert').modal('show');
                }).mediaLibrary('option', 'multiple', false);
            }
        });
    });
});

ls.hook.add('ls_template_init_start', function(){ 
    ls.registry.get('component.tinimce.plugins').push('lsmedia');
    ls.registry.get('component.tinimce.toolbar').push('lsmedia');
}, 100);
