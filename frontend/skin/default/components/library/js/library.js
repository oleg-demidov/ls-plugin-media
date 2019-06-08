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
                onUploadSuccess: function(event, data){
                    let medias = $(data.response.html);
                    medias.addClass('border-success');
                    this.addMedia(medias, true);
                }.bind(this)
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
                        
            this.addMedia(medias);
            
            this.elements.loadBtn.bsButton('setCount', response.moreCount);
            if(response.moreCount <= 0){
                this.elements.loadBtn.addClass('d-none');
            }
            
            this.option('page', this.option('page') + 1);
        },
        
        addMedia: function(medias, prepend = false){
                        
            if(prepend){
                this.elements.mediaContainer.prepend(medias);
            }else{
                this.elements.mediaContainer.append(medias);
            }
            
            medias.mediaMedia();
            
            this.attachEventsMedia(medias)
        },
        
        attachEventsMedia: function(medias){
            medias.on('click', function(event){
                $(event.currentTarget).mediaMedia('select');
                this.elements.mediaContainer
                    .find(this.option('classes.media'))
                    .not(event.currentTarget)
                    .mediaMedia('deselect');
            }.bind(this))
        },
        
        toggleView: function(view){
            view = view || "tile";
            this.elements.mediaContainer
                .removeClass(Object.values(this.option('classes.view')).join(' '))
                .addClass(this.option('classes.view')[view]);
            
        }

        
    });
})(jQuery);