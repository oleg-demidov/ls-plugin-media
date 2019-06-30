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

    $.widget( "livestreet.mediaField", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
            },
            
            // Селекторы
            selectors: {
                body:       "[data-field-body]",
                addBtn:     "[data-add-btn]",
                removeBtn:  "[data-remove-btn]",
                countField: "[data-media-count-field]",
                count:      "[data-media-count] [btn-text]",
                cropper:    '@[data-cropper]'  
            },
            // Классы
            classes: {
                choose:"choose",
            },

            i18n: {
                remove: "@plugin.media.media.remove"
            },
            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
                        
            this.elements.body.children().mediaMedia();
            this._on(this.elements.addBtn, {click: "onClickAdd"});
            this._on(this.elements.removeBtn, {click: "onClickRemove"});
            this._on(this.elements.body, {click: "checkSelect"});
        },
        
        getSelectMedia: function(){
            return this.elements.body.children()
                .filter(function(i, el){
                    return $(el).mediaMedia('isSelect');
                });
        },
        
        onClickAdd: function(){
            $('[data-library]')
                .mediaLibrary('chooseMedia', this.onChoose.bind(this))
                .mediaLibrary('option', 'multiple', this.element.data('multiple'));
        },
        
        onChoose: function($media){
            if(!this.element.data('multiple')){
                this.elements.body.empty();
                if($media.length > 1){
                    $media = $($media.get(0))
                }
            }else{
                let ids = $.map( this.elements.body.children(), function(id, el) {
                    return $(id).data('id');
                });

                $media = $media.filter(function(id, element){
                    return ($.inArray($(element).data('id'), ids) == -1);
                }.bind(this));
            }
            
            $media.mediaMedia('setNameInput', this.element.data('fieldName'));
            console.log(this.element.data('fieldName'), $media);
            
            if(this.element.data('crop')){ 
                this.elements.cropper.mediaCropper('crop', $media,  function(e, data){
                    this.elements.body.html(data.html);
                    this.elements.body.children().mediaMedia()
                        .mediaMedia('setNameInput', this.element.data('fieldName'));
                    this.updateCount();
                }.bind(this));
            }else{
                this.elements.body.prepend($media);
                this.updateCount();
            }
            
        },
        
        updateCount: function(){
            let count = this.elements.body.children().length;
            this.elements.countField.attr('value', count).change();
            this.elements.count.text(count);
        },
        
        onClickRemove:function(){
            this.getSelectMedia().remove();
            this.updateCount();
            this.elements.removeBtn.addClass('d-none');
        },
        
        checkSelect: function(event){
           
            if(this.getSelectMedia().length){
                this.elements.removeBtn.removeClass('d-none');
            }else{
                this.elements.removeBtn.addClass('d-none');
            }
            
        }

        
    });
})(jQuery);