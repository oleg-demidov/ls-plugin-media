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

    $.widget( "livestreet.mediaUploader", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                // Загрузка файла
            },

            // Селекторы
            selectors: {
                // Drag & drop зона
                upload_zone:  '[data-upload-area]',
                // Инпут
                upload_input: '[data-file-input]'
            },

            
            // Настройки загрузчика
            fileupload : {
                sequentialUploads: false,
                singleFileUploads: true,
                limitConcurrentUploads: 3
            },
            
            i18n: {
            },

            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
            onUploadAdd:null

        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
            
            $.extend( this.option( 'fileupload' ), {
                fileInput:  this.elements.upload_input,
                url:        this.element.data('url'),
                formData:   this.option('params'),
                paramName:  this.elements.upload_input.attr('name')
            });
            
            this.elements.upload_input.fileupload( this.option( 'fileupload' ) );
            
            this.elements.upload_input.on({
                /**
                 * Фикс fileupload не обрабатывает change
                 */
                change:function(e){
                    $(e.currentTarget).fileupload('add', {files: $(e.currentTarget).prop('files')});
                },
                fileuploadadd: this.onUploadAdd.bind( this )
            })

        },

        /**
         * 
         */
        onUploadAdd: function( event, data ) {
            this._trigger('onUploadAdd', event, data);
            
        },

    });
})(jQuery);