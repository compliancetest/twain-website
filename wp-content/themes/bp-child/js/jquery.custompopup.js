/**
* jQuery Custom Popup Plugin
* 
*/

(function($){
    $.fn.cplightbox = function(options){
        return this.each(function(){
            opts = $.extend({}, $.fn.cplightbox.defaults, options),
            $overlay = null,
            $self = $(this);
            
            if($('.mask-wrapper').length < 1)
            {
                $('body').append("<div class='mask-wrapper'></div>");
            }
            $overlay = $(".mask-wrapper");
            //Make Sure the current element is hiden            
            
            $overlay.attr('class', 'mask-wrapper');
            if(opts.additionalClass != '')
                $overlay.addClass(opts.additionalClass);
            
            function setOverlaySize()
            {
                $overlay.css({'height': '100%', 'width': '100%'});
                var wWidth = $(document).width();
                var wHeight = $(document).height();
                $overlay.height(wHeight);
                $overlay.width(wWidth);                
            }
            
            function setSelfPosition()
            {
                var selfHeight = $self.outerHeight();
                var wHeight = $(window).height();
                if(selfHeight > wHeight)
                {
                    $self.css('top', 50);
                }else if(selfHeight > 0){
                    $self.css('top', (wHeight - selfHeight) / 2); //Keep Vertical Align Middle
                }
            }
            
            function initCloseButton()
            {
                $self.find('.close_btn').click(function(){
                    $self.fadeOut('fast', function(){
                        opts.onClose();
                        $overlay.hide();
                    })
                })
            }
            
            function showPopupBox(){
                if($overlay.css('display') == 'none')
                    $overlay.show();
                
                //Show Boxes
                if(opts.isAjax == false)
                {
                    $self.hide();
                    $overlay.append($self);
                    
                    $self.fadeIn('fast', function(){
                        opts.onLoad();        
                    });
                    
                }else{
                    
                }    
            }
            
            setOverlaySize();
            setSelfPosition();
            initCloseButton();
            
            if($overlay.find('.popup-box:visible').length > 0)
            {
                $overlay.find('.popup-box:visible').fadeOut('fast', function(){                    
                    showPopupBox();
                })
            }else{
                showPopupBox();
            }
            
            $overlay.unbind('click');
            if(opts.closeWhenClickOveraly)
            {
                $self.click(function(e){
                    e.stopPropagation();
                })
                $overlay.click(function(){
                    $self.fadeOut('fast', function(){
                        opts.onClose();
                        $overlay.hide();
                    })
                })
            }
            
            
            //Add Events
            $(window).resize(setOverlaySize);
            $(window).resize(setSelfPosition);
            $(window).scroll(setSelfPosition);
        })
    }
    $.fn.cplightbox.defaults = {
        isAjax: false,
        closeWhenClickOveraly: true,
        onLoad: function() {},
        onClose: function() {},
        additionalClass: ''
    }
})(jQuery)