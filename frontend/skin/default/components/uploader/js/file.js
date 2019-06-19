/**
 * Media
 *
 * @module ls/uploader
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Oleg Demidov
 */

(function($) {
    "use strict";

    $.widget( "livestreet.mediaFile", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
                        
            id: 0,
            // Селекторы
            selectors: {
                progress:   "[data-progress]",
                close:      ".close",
                name:       "[data-file-name]"
            },
            // Классы
            classes: {
                
            },

            i18n: {
            },
            
            data:null

        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
                        
            this.elements.name.text(this.option('data').name);
        },
        
        setProgress: function( precent){
            this.elements.progress.css('width', precent + "%");
        },
        
        cancelUpload: function(){
            this._trigger('onCancelUpload', null, this.option('id') );
            this.element.remove();
        },
        
        successUpload: function( data ){
            this.elements.progress.addClass('d-none');
            this.element.hide();
        },
        
        errorUpload: function(){
            this.element.hide();
        }
        
    });
})(jQuery);