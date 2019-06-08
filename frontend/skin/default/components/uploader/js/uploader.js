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
                upload_input: '[data-file-input]',
                
                fileTpl: "[data-file-tpl]"
            },

            // Настройки загрузчика
            fileupload : {
                
            },
            
            i18n: {
            },

            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
            onUploadAdd:null,
            
            uploadableFiles:[]

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
                
                dataType:   "json",
                
//                onInit: function(){console.log('init')},
                
                onNewFile: this.onNewFile.bind( this ),
                
                onUploadProgress: this.onUploadProgress.bind( this ),
                
                onUploadSuccess: this.onUploadSuccess.bind( this ),
                
                onDocumentDragEnter: function(){
                    this.option('originalColorBorder', this.elements.upload_zone.css('border-color'));
                    this.elements.upload_zone.css('border-color', 'green');
                }.bind(this),
                
                onDocumentDragLeave: function(){
                    this.elements.upload_zone.css('border-color', this.option('originalColorBorder'));
                }.bind(this),
            });
            
            this.elements.upload_zone.dmUploader( this.option( 'fileupload' ) );
           
        },

        /**
         * 
         */
        onNewFile: function( id, fileData ) {
            let file = this.elements.fileTpl.clone();
            
            this.elements.upload_zone.append(file);
            
            file.mediaFile({
                data:fileData,
                onCancelUpload: function(){
                    this.elements.upload_zone.dmUploader( 'cancel' );
                }.bind(this)
            });
            
            file.removeClass('d-none');
            
            this.option('uploadableFiles')[id] = file;
            
            this._trigger('onNewFile', null, file );
        },

        /**
         * 
         */
        onUploadProgress: function(  id, percent) {
            if(this.option('uploadableFiles')[id] !== undefined){
                this.option('uploadableFiles')[id].mediaFile('setProgress', percent);
            }
        },

        /**
         * 
         */
        onUploadSuccess: function( id, data ) {
            if(this.option('uploadableFiles')[id] !== undefined){
                this.option('uploadableFiles')[id].mediaFile('successUpload');
                this._trigger('onUploadSuccess', null, {
                    response: data,
                    file: this.option('uploadableFiles')[id].mediaFile('option', 'data') });
            }
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
