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

    $.widget( "livestreet.mediaLibrary", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                // Подгрузка файлов
                load: aRouter['media'] + 'load/', 
            },
            
            // Селекторы
            selectors: {
                medias:  '[data-library-medias]',
                uploader: '[data-uploader]',
                mediaTpl:  '.media-tpl',
            },
            // Классы
            classes: {
                media:'[data-library-media]'                
            },

            i18n: {
            },

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
            
            this.elements.uploader.mediaUploader({
                onUploadAdd: this.uploadMedia.bind(this)
            });
            
            this._load('load', {}, 'append');
        },
        
        uploadMedia: function(event, data){
            let media = this.elements.mediaTpl.clone();
            media.mediaMedia({
                upload:data
            });
            this.elements.medias.prepend(media);
        },
        
        append: function(response){
            this.elements.medias.append($(response.html));
            
            let medias= this.elements.medias.find(this.option('classes.media'));
            
//            files.mediaFile();
//            
//            files.on('click', function(e){
//                this.selectItem( $(e.currentTarget) );
//            }.bind(this));
            
        },
        
        selectItem: function(file){
            this.elements.fileInfoEmpty.addClass('d-none');
            this.elements.info.removeClass('d-none');
            
            this.elements.items.removeClass('border-1  p-1').addClass('p-2');
            file.addClass('border-1 p-1').removeClass('p-2');
            
            $.each(this.option('infoList'), function(name, selector){
                this.elements.info.find(selector).html(file.data(name))
            }.bind(this));
            
            //let sel = this.addSizesSelect(file.data('mediaSizes'));
            
            this.option('selectedItem', file);
            
        },
        
        getSelectSize: function(){
            if(this.option('select') !== null){
                return this.option('select').val();
            }
        },
        
        addSizesSelect: function(sizes){
            let sel = $(document.createElement('select')).attr('name', 'sizes');
            
            $.each(sizes, function(i, size){
                let opt = $(document.createElement('option'));
                let sSize = size.w + "x" + (size.h !== null?size.h:"") + (size.crop?"crop":"");
                opt.val(sSize).text(sSize);
                sel.append(opt);
            })
            
            this.elements.info.find(this.option('infoList.sizes')).html(sel);
            
            this.option('select', sel);
            
            return sel;
        },
        
        getSelectItem: function(){
            if(!this.option('selectedItem')){
                return null;
            }
            return this.option('selectedItem').clone();
        },
        
        reset: function(){
            this.elements.fileInfoEmpty.removeClass('d-none');
            this.elements.info.addClass('d-none');
        },
        
        onClickRemove: function(){
            this._load('remove', {id:$(this.option('infoList.id')).text()}, "loadFiles");
        }

        
    });
})(jQuery);