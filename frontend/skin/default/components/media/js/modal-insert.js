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

    $.widget( "livestreet.mediaModalInsert", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                load: aRouter.media + "form-insert"
            },
            
            // Селекторы
            selectors: {
                body:   ".modal-body"
                
            },
            // Классы
            classes: {
                btnInsert: '[data-btn-insert]',
                form: 'form'
            },

            i18n: {
                tpl: null
            },
            isUploadable:false,
            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
            onInsert: null

        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
            
        },
        
        show: function(id){ 
            this.element.modal('show');
            
            this._load('load', {id:id}, 'onLoad');
        },
        
        onLoad: function(response){
            this.elements.body.html(response.html);
            this.option('i18n.tpl', response.template);
            this.element.find(this.option('classes.btnInsert')).on('click', function(){
                let data = this.getDataForm(this.element.find(this.option('classes.form')));
                this._trigger('onInsert', null, this._i18n('tpl',data ));
                this.element.modal('hide');
            }.bind(this));
        },
        
        getDataForm: function(form){
            let data = $(form).serializeArray();
            let dataNormal = {};
            data.forEach( function(item){
                dataNormal[item.name] = item.value;
            });
            return dataNormal;
        }
       
        
    });
})(jQuery);