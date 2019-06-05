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
                mediaContainer:     '[data-library-medias]',
                uploader:           '[data-uploader]',
                toggleView: '[data-toggle-view]',
                loadBtn:    "[data-load-btn]",
                viewField:  "[data-toggle-view] input",
                sortFields: ".sort-field"
            },
            // Классы
            classes: {
                media:'[data-media-item]' ,  
                
                view:{
                    tile:"media-tile",
                    column:"media-column"
                },
                
            },
            
            page:1,

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
                mediaContainer: this.elements.mediaContainer
            });
            
            this.more();
            
            this.elements.loadBtn.on('click', this.more.bind(this));
            
            this.elements.toggleView.find('input').on('change', function(event){
                this.toggleView( $(event.target).val() );
            }.bind(this));
            
            this.elements.sortFields.on('change', function(event){
                this.option('params')[event.currentTarget.name] = $(event.currentTarget).val();
                this.option('page', 1);
                this.elements.mediaContainer.empty();
                this.more();
            }.bind(this))
        },
        
        more: function(){
            this._load('load', {page:this.option('page')}, 'onLoad');
            this.elements.loadBtn.bsButton('loading');
        },
        
        onLoad: function(response){
            this.elements.loadBtn.bsButton('loaded');
            
            let medias = $(response.html);
            
            this.elements.mediaContainer.append(medias);  
            
            medias.mediaMedia();      
            
            medias.on('click', function(event){
                $(event.currentTarget).mediaMedia('select');
                this.elements.mediaContainer
                    .find(this.option('classes.media'))
                    .not(event.currentTarget)
                    .mediaMedia('deselect');
            }.bind(this))
            
            this.elements.loadBtn.bsButton('setCount', response.moreCount);
            if(response.moreCount <= 0){
                this.elements.loadBtn.addClass('d-none');
            }
            
            this.option('page', this.option('page') + 1);
        },
        
        toggleView: function(view){
            view = view || "tile";
            this.elements.mediaContainer
                .removeClass(Object.values(this.option('classes.view')).join(' '))
                .addClass(this.option('classes.view')[view]);
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