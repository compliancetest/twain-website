/**
* jQuery Custom Popup Plugin
* 
*/

(function($){
    $.fn.cplightbox = function(options){
        return this.each(function(){
            var opts = $.extend({}, $.fn.cplightbox.defaults, options),
            $overlay = null,
            $self = $(this);
            opts.self = $self;
            opts.href = opts.href == '' ? $self.attr('href') : opts.href;
            
            opts.template = $('<div class="popup-box" id="product-popup-box" style="display: none">' + 
                            '<div class="popup-box-header radius6 noradiusbottom"></div>' + 
                            '<div class="popup-box-content"></div>' +    
                            '<a class="close_btn"></a>' + 
                            '</div>');
                            
            if($self.attr('cp-type'))
                opts.type = $self.attr('cp-type');
            if($self.attr('cp-removeBoxAfterClose'))
                opts.removeBoxAfterClose = $self.attr('cp-removeBoxAfterClose');
            if($self.attr('cp-closeWhenClickOveraly'))
                opts.removeBoxAfterClose = $self.attr('cp-closeWhenClickOveraly');
            if($self.attr('cp-showTemplate'))
                opts.showTemplate = $self.attr('cp-showTemplate');
            if($self.attr('cp-headerTitle'))
                opts.headerTitle = $self.attr('cp-headerTitle');        
            if($self.attr('cp-width'))
                opts.width = $self.attr('cp-width');        
            
            if(opts.width)    
                opts.template.width(opts.width);
            if(opts.headerTitle)    
                opts.template.find('.popup-box-header').html(opts.headerTitle);            
            
            $self.click(function(){                
                if($('.mask-wrapper').length < 1)
                {
                    $('body').append("<div class='mask-wrapper'></div>");
                }
                $overlay = $(".mask-wrapper");
                //Make Sure the current element is hiden            
                
                $overlay.attr('class', 'mask-wrapper');
                if(opts.additionalClass != '')
                    $overlay.addClass(opts.additionalClass);
                
                if($overlay.css('display') == 'none')
                    $overlay.show();
                setOverlaySize();
                switch(opts.type)
                {
                    case 'ajax':
                        //Getting HTML By Ajax
                        if($overlay.find('.loading').length < 1)
                            $overlay.append('<div class="loading"></div>');
                        $overlay.find('.loading').show();
                        $.ajax({
                            url: opts.href,
                            type: 'get',
                            dataType: 'html',
                            success: function(rsp){
                                $overlay.find('.loading').remove();                                                      
                                
                                if(opts.showTemplate)
                                {                                    
                                    opts.template.find('.popup-box-content').append(rsp);
                                    $overlay.append(opts.template);
                                }else{
                                    $overlay.append(rsp);    
                                }                                
                                opts.box = $overlay.find('.popup-box:last');
                                setSelfPosition();
                                initPopupEvents();
                                if($overlay.find('.popup-box:visible').length > 0)
                                {
                                    $overlay.find('.popup-box:visible').fadeOut('fast', function(){     
                                        opts.onClose();               
                                        opts.box.fadeIn('fast', function(){
                                            setOverlaySize();
                                            opts.onLoad();                                                    
                                        });
                                    })
                                }else{
                                    opts.box.fadeIn('fast', function(){
                                        setOverlaySize();
                                        opts.onLoad();        
                                    });
                                }
                                
                            }
                        })
                        break;
                    case 'inline': //Show Inline Object
                    default:                                    
                        opts.box = $(opts.href);    
                        opts.box.hide();                        
                        $overlay.append(opts.box);                        
                        setSelfPosition();
                        initPopupEvents();
                        
                        if($overlay.find('.popup-box:visible').length > 0)
                        {
                            $overlay.find('.popup-box:visible').fadeOut('fast', function(){                    
                                opts.onClose();
                                opts.box.fadeIn('fast', function(){
                                    setOverlaySize();
                                    opts.onLoad();        
                                });
                            })
                        }else{
                            opts.box.fadeIn('fast', function(){
                                setOverlaySize();
                                opts.onLoad();        
                            });
                        }
                        
                        
                        break;
                    
                }
                
                return false;
                
            })
            
            
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
                var selfHeight = opts.box.outerHeight();
                var wHeight = $(window).height();
                if(selfHeight > wHeight)
                {
                    opts.box.css('top', 50);
                }else if(selfHeight > 0){
                    opts.box.css('top', (wHeight - selfHeight) / 2); //Keep Vertical Align Middle
                }
            }
            
            function initPopupEvents()
            {
                opts.box.find('.close_btn, .close-popup-btn').click(function(){
                    opts.box.fadeOut('fast', function(){
                        opts.onClose();
                        $overlay.hide();
                        if(opts.removeBoxAfterClose)
                            opts.box.remove();
                        else
                            $('body').append(opts.box);
                    })
                })
                
                $overlay.unbind('click');
                if(opts.closeWhenClickOveraly)
                {
                    opts.box.click(function(e){
                        e.stopPropagation();
                    })
                    $overlay.click(function(){
                        opts.box.fadeOut('fast', function(){
                            opts.onClose();
                            $overlay.hide();
                            if(opts.removeBoxAfterClose)
                                opts.box.remove();
                            else
                                $('body').append(opts.box);
                        })
                    })
                }
                
                
                //Add Events
                $(window).resize(setOverlaySize);
                $(window).resize(setSelfPosition);
                $(window).scroll(setSelfPosition);
            }
            
        })
    }
    $.fn.cplightbox.defaults = {
        isAjax: false,
        type: 'inline',
        href: '',
        box: null,
        removeBoxAfterClose: false,
        closeWhenClickOveraly: true,
        showTemplate: false,
        headerTitle: '',
        onLoad: function() {},
        onClose: function() {},
        additionalClass: ''
    }
})(jQuery)