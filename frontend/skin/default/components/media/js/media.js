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

    $.widget( "livestreet.mediaMedia", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                
            },
            
            id: 0,
            // Селекторы
            selectors: {
                progress: ".progress",
                close:".media-close"
            },
            // Классы
            classes: {
            },

            i18n: {
            },
            isUploadable:false,
            // Доп-ые параметры передаваемые в аякс запросах
            params: {}

        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
            
            this.element.removeClass('d-none');
            
            this._on( this.elements.close, {click:"cancelUpload"});
            
            if(this.option('isUploadable')){
                this.elements.close.removeClass('d-none');
                this.elements.progress.removeClass('d-none');
                this.element.on('onUploadProgress', this.onUploadProgress.bind(this));
                this.element.on('onUploadSuccess', this.onUploadSuccess.bind(this))
            }
        },
        
        
        onUploadProgress: function(event, precent){
            this.elements.progress.find('.progress-bar')
                    .text(precent)
                    .css('width', precent + "%");
            if(precent == 100){
                this.elements.progress.addClass('d-none');
            }
            
        },
        
        cancelUpload: function(){
            this._trigger('onCancelUpload', null, this.option('id') );
            this.element.remove();
        },
        
        onUploadSuccess: function(event, data){
            this.element.css('background-image', 'url('+ data.path +')');
            this.elements.close.remove();
            
        }      

        
    });
})(jQuery);