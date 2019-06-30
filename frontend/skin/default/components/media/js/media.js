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
                remove: aRouter['media'] + 'remove/'
            },
            
            id: 0,
            // Селекторы
            selectors: {
                remove:     "[data-remove]",
                progress:   ".progress",
                close:      ".media-close",
                mediaModal: "@[data-media-modal]",
                name:       "[data-media-name]" ,
                input:      "[data-input]"
            },
            // Классы
            classes: {
                choose:"choose",
            },

            i18n: {
                remove: "@plugin.media.media.remove"
            },
            isUploadable:false,
            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
            onChoose: null,
            
            onUpdated: null

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
            
            this.elements.remove.lsConfirm({
                message: this._i18n('remove', {name:this.elements.name.text()}),
                onconfirm: this.remove.bind(this),
            });
            
            this._on( this.elements.close, {click:"cancelUpload"});
            
            if(this.option('isUploadable')){
                this.element.data('id', this.option('id'));
                this.elements.close.removeClass('d-none');
                this.elements.progress.removeClass('d-none');
            }else{
                this.option('id', this.element.data('id'));
            }
            
            this._on(this.element, {click:"toggleSelect"});
        },
              
        getWebPath: function(){
            return this.element.data('webPath');
        },
        
        select: function(){
            this.element.addClass(this.option('classes.choose'));
        },
        
        deselect: function(){
            this.element.removeClass(this.option('classes.choose'));
        },
        
        isSelect: function(){
            return this.element.hasClass(this.option('classes.choose'))
        },
        
        toggleSelect: function(){
            this.element.toggleClass(this.option('classes.choose'));
        },
        
        remove: function(){
            this._load('remove', {id:this.option('id')}, function(response){
                this.element.remove();
            }.bind(this))
        },
        
        setNameInput: function(name){
            this.elements.input.attr('name', name + '[]');
        }
        
    });
})(jQuery);