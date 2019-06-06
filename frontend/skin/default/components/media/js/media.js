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

    $.widget( "livestreet.mediaMedia", $.livestreet.lsComponent, {
        /**
         * Дефолтные опции
         */
        options: {
            // Ссылки
            urls: {
                loadInfo: aRouter.media + "info"
            },
            
            id: 0,
            // Селекторы
            selectors: {
                progress:   ".progress",
                close:      ".media-close",
                mediaModal: "@[data-media-modal]",
                info:       ".media-info"
            },
            // Классы
            classes: {
                choose:"choose",
                viewTile:".media-tile"
            },

            i18n: {
            },
            isUploadable:false,
            // Доп-ые параметры передаваемые в аякс запросах
            params: {},
            
            onChoose: null

        },

        /**
         * Конструктор
         *
         * @constructor
         * @private
         */
        _create: function () {
            this._super();
            
            this.element.removeClass('d-none');
            
            this._on( this.elements.close, {click:"cancelUpload"});
            
            if(this.option('isUploadable')){
                this.elements.close.removeClass('d-none');
                this.elements.progress.removeClass('d-none');
                this.element.on('onUploadProgress', this.onUploadProgress.bind(this));
                this.element.on('onUploadSuccess', this.onUploadSuccess.bind(this))
            }else{
                this._on(this.element, {click:"onClick"})
            }
        },
        
        select: function(){
            this.element.addClass(this.option('classes.choose'));
            this.showInfo();
        },
        
        deselect: function(){
            this.element.removeClass(this.option('classes.choose'));
            if(this.element.closest(this.option('classes.viewTile')).length){
                this.hideInfo();
            }
        },
        
        showInfo: function(){
            this.elements.info.show();
        },
        
        hideInfo: function(){
            this.elements.info.hide();
        },
        
        onUploadProgress: function(event, precent){
            this.elements.progress.find('.progress-bar')
                    .text(precent)
                    .css('width', precent + "%");
            if(precent == 100){
                this.elements.progress.addClass('d-none');
            }
            
        },
        
        cancelUpload: function(){
            this._trigger('onCancelUpload', null, this.option('id') );
            this.element.remove();
        },
        
        onUploadSuccess: function(event, data){
            this.element.css('background-image', 'url('+ data.path +')');
            this.elements.close.remove();
            
        },
        
        onClick:function(e){
            //this.elements.mediaModal.modal('show');
        }

        
    });
})(jQuery);