/**
 * LiveStreet
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Oleg Demidov
 */
jQuery(document).ready(function($){
    ls.hook.add('ls_template_init_start', function(){ 
        ls.registry.get('component.tinimce.plugins').push('lsmedia');
        ls.registry.get('component.tinimce.toolbar').push('lsmedia');
    }, 100);
});

tinymce.PluginManager.add('lsmedia', function(editor, url) {
    // Заголовки
    

    // Ссылка на пользователя
//    editor.addButton('lsmedia', {
//        icon: 'emoticons',
//        tooltip: 'User',
//        onclick: function() {
//            // Open window
//            editor.windowManager.open({
//                title: 'Add user',
//                body: [
//                    { type: 'textbox', name: 'login', label: 'Login' }
//                ],
//                onsubmit: function(e) {
//                    editor.insertContent('<ls user="' + e.data.login + '">');
//                }
//            });
//        }
//    });

    // Вставка медиа-объектов
    editor.addButton('lsmedia', {
        icon: 'image',
        tooltip: 'Insert media',
        onclick: function() {
            $('#medialibrary').modal('show')
        }
    });

//    editor.on('postProcess', function(e) {
//        if (e.set) {
//            e.content = _code2html(e.content);
//        }
//
//        if (e.get) {
//            e.content = _html2code(e.content);
//        }
//    });
//
//    editor.on('beforeSetContent', function(e) {
//        e.content = _code2html(e.content);
//    });
//
//
//    function _code2html(s) {
//        s = tinymce.trim(s);
//
//        function rep(re, str) {
//            s = s.replace(re, str);
//        }
//
//        rep(/<ls.*?user=\"(.*?)\".*?\/>/gi, "<span class='ls-user'>$1</span>");
//        rep(/<gallery.*?items=\"(.*?)\".*?nav=\"(.*?)\".*?\/>/gi, "[gallery items=\"$1\" nav=\"$2\"]");
//
//        return s;
//    };
//
//    function _html2code(s) {
//        s = tinymce.trim(s);
//
//        function rep(re, str) {
//            s = s.replace(re, str);
//        }
//
//        rep(/<span.*?class=\"ls-user\">(.*?)<\/span>/gi, "<ls user=\"$1\" />");
//        rep(/\[gallery.*?items=\"(.*?)\".*?nav=\"(.*?)\".*?\]/gi, "<gallery items=\"$1\" nav=\"$2\" />");
//
//        return s;
//    };
});