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
                count:      "[data-media-count] [btn-text]"
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
                .mediaLibrary('option', 'multiple', true);
        },
        
        onChoose: function($media){
            let ids = $.map( this.elements.body.children(), function(id, el) {
                return $(id).data('id');
            });

            $media = $media.filter(function(id, element){
                return ($.inArray($(element).data('id'), ids) == -1);
            }.bind(this));
            
            this.elements.body.append($media);
            this.updateCount();
        },
        
        updateCount: function(){
            let count = this.elements.body.children().length;
            this.elements.countField.attr('value', count).change();console.log(this.elements.countField.val())
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