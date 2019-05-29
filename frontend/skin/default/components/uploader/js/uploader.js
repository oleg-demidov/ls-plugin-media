/**
 * Media
 *
 * @module ls/uploader
 *
 * @license   GNU General Public License, version 2
 * @copyright 2013 OOO "ЛС-СОФТ" {@link http://livestreetcms.com}
 * @author    Denis Shakhov <denis.shakhov@gmail.com>
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
                upload_input: '[data-file-input]',
                
                file_upl:"[data-file-tmp]"
            },

            
            // Настройки загрузчика
            fileupload : {
                
            },
            
            i18n: {
            },

            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
            onFileUpload:null,
            onFileError:null

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
                fieldName:  this.elements.upload_input.attr('name'),
                url:        this.element.data('url'),
                extraData:  function(){
                    return this.option('params')
                }.bind(this),
                dataType:   "json"
            });
            console.log(this.option( 'fileupload' ))
            
            this.elements.upload_zone.dmUploader( this.option( 'fileupload' ) );
            
            this.elements.upload_input.on({
                onNewFile: this.onNewFile.bind( this ),
                onUploadProgress: this.onUploadProgress.bind( this ),
            })

        },

        /**
         * 
         */
        onNewFile: function( id, file ) {
            
            console.log('onUploadAdd', data);
            
            data.then(function(  event, data) {
                console.log('process', data);
            });
        
//            let file = data.files[0]; 
//            let fileTpl = $(this.elements.file_upl.clone());
//            fileTpl.removeClass('d-none').attr('id', file.name.replace(/[^a-zA-Z0-9 ]/g, ""));
//            this.elements.upload_zone.append(fileTpl);
//            fileTpl.find('.name-file').html(file.name);
//            fileTpl.find('.close').on('click', data.abort);
        },


        /**
         * 
         */
        onUploadProgress: function(  id, percent) {
            console.log('onUploadProgress', data.progress());
        },

        

        /**
         * 
         */
        onUploadDone: function( fileObj, response ) {
            let file = this.elements.upload_zone.find('#'+fileObj.name.replace(/[^a-zA-Z0-9 ]/g, ""));
            setTimeout(function(){file.hide(500);}, 3000);
            file.find('.close').on('click', function(){
                file.hide();
            });
            this._trigger('onFileUpload', null, { file: file, response: response });
        },

        /**
         * 
         */
        onUploadError: function( fileObj, response ) {
            ls.msg.error( response.sMsgTitle, response.sMsg);
            
            let file = this.elements.upload_zone.find('#'+fileObj.name.replace(/[^a-zA-Z0-9 ]/g, ""));
            setTimeout(function(){file.hide(500);}, 3000);
            file.find('.close').on('click', function(){
                file.hide();
            });
            
            this._trigger('onFileError', null, { file: fileObj, response: response });
        },

        onFileAdd: function(event, data){
            let fileObj = data.files[0];
            let file = this.elements.upload_zone.find('#'+fileObj.name.replace(/[^a-zA-Z0-9 ]/g, ""));
            if(file.length){
                ls.msg.error( this._i18n('errorDublicate'));
                return false;
            }
        }
    });
})(jQuery);