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
                sequentialUploads: false,
                singleFileUploads: true,
                limitConcurrentUploads: 3
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
                url:        this.element.data('url'),
                formData:   this.option('params')
            });
            console.log(this.option( 'fileupload' ))
            
            this.elements.upload_input.fileupload( this.option( 'fileupload' ) );
            
            this.elements.upload_input.on({
                /**
                 * Фикс fileupload не обрабатывает change
                 */
                change:function(e){
                    $(e.currentTarget).fileupload('add', {files: $(e.currentTarget).prop('files')});
                },
                fileuploadadd: this.onUploadAdd.bind( this ),
                fileuploaddone: function( event, data ) {
                    this[ data.result.bStateError ? 'onUploadError' : 'onUploadDone' ]( data.files[0], data.result );
                }.bind( this ),
                fileuploadprogress: function( event, data ) {
                    this.onUploadProgress( data.files[0], parseInt( data.loaded / data.total * 100, 10 ) );
                }.bind( this ),
                fileuploaddrop:this.onFileAdd.bind( this ),
                fileuploadchange:this.onFileAdd.bind( this )
            })

        },

        /**
         * 
         */
        onUploadAdd: function( event, data ) {console.log('onUploadAdd')
            let file = data.files[0]; 
            let fileTpl = $(this.elements.file_upl.clone());
            fileTpl.removeClass('d-none').attr('id', file.name.replace(/[^a-zA-Z0-9 ]/g, ""));
            this.elements.upload_zone.append(fileTpl);
            fileTpl.find('.name-file').html(file.name);
            fileTpl.find('.close').on('click', data.abort);
        },


        /**
         * 
         */
        onUploadProgress: function( fileObj, percent ) {
            let file = this.elements.upload_zone.find('#'+fileObj.name.replace(/[^a-zA-Z0-9 ]/g, ""));
            file.find('.progress-bar')
                .html(percent+"%")
                .attr('style', "width: "+percent+"%")
                .attr('aria-valuenow', percent);
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