// JavaScript Document
(function($){
	$(function(){
		
		/*
		 *	mOover Toolbar
		*/
		//Color Picker
		//Hex Converter
		Number.prototype.toHex = function () {var o = this.toString(16);return o[1] ? o : "0" + o;}
		function toHex(string) {
			return string.replace(/rgb\((\d+)\,\s(\d+)\,\s(\d+)\)/g, function (a, b, c, d) {return "#" + parseInt(b).toHex() + parseInt(c).toHex() + parseInt(d).toHex();})
		}
		if ($.fn.ColorPicker) {
			$('input[name="mv-color"]').ColorPicker({
				color : $('input[name="mv-color"]').val(),
				onShow : function(){
					$(this).ColorPickerSetColor($('input[name="mv-color"]').val())
				},
				onChange : function(hsb, hex, rgb) {
					$('input[name="mv-color"]').val('#'+hex)
				}
			})
			$('input[name="mv-bg"]').ColorPicker({
				color : $('input[name="mv-bg"]').val(),
				onChange : function(hsb, hex, rgb) {
					$('input[name="mv-bg"]').val('#'+hex)
				}
			})	
		}
		//--
		if ( $('.moover-slide-editor').length>0 ) {
			$('.moover-slide-editor').appendTo('body')	
		}
		var mWP = {}
		var dragOptions = { 
			addClasses: false, 
			drag: function(event, ui) { $('body').addClass('moover-drag') } ,
			stop: function(event, ui) { 
				$('body').removeClass('moover-drag')
				$(this).trigger('mouseover')
				//if (mWP.isResponsive) {
					$(this).css({
						left : Math.round($(this).position().left/$('.moover-slider').width()*100)+'%'
					}) 
				//}
			},
			cursor:'default'
		}
		/* Preview is Playing */
		function isPlaying() {
			return $('body').hasClass('moover-preview')	
		}
		/* Sortable Slides */
		if ( $('#moover-sortable').length>0) {
			$('#moover-sortable').sortable({
				items: "li:not(.sort-disabled)"
			})
		}
		$('.manage-copy').live('click',function(){
			$(this).parents('li').clone().insertAfter( $(this).parents('li') )
		})
		$('.manage-remove').live('click',function(){
			$(this).parents('li').remove()
		})
		$('.manage-add').click(function(){
			$($('.moover-slide-template').html()).insertBefore( $('#moover-sortable li:last-child') ) 
		})
		
		function loadMooverSlide( index ) {
			var html = $('#moover-sortable li').eq(index).find('.moover-html').html()
			$('.moover-slider').html(html)
			$('.moover-slider .moover-text p').draggable(dragOptions)
			mWP.activeIndex = index
			var js = $('#moover-sortable li').eq(index).find('.moover-js').html()
			mWP.e = (  eval ( '('+ js+')'  ) );
			//Set Active FX
			$('.active-fx').removeClass('active-fx')
			var index = $('.mv-effects-left a[data-fx="'+mWP.e.effect+'"]').addClass('active-fx').index()
			$('.mv-effects-right > div:eq('+index+')').addClass('active-fx')
			var e = mWP.e
			//Init Slide Effect
			if (e.effect=='slide') {
				if (e.crossSideLines!==true)
					$('.mv-effects-right #crossSideLines').removeAttr('checked')
				else {
					$('.mv-effects-right #crossSideLines').attr('checked','checked')
				}
				$('.mv-effects-right #slide-moveWidth').val(e.moveWidth)*1
				$('.mv-effects-right #slide-moveTime').val(e.moveTime)*1
				$('.mv-effects-right #lineFrom').val(e.lineFrom)
				$('.mv-effects-right #textLineDelay').val(e.textLineDelay)
				$('.mv-effects-right #transformPresetSlide option:selected').removeAttr('selected')
				$('.mv-effects-right #transformPresetSlide option[value="'+e.transformPreset+'"]').attr('selected','selected')
				$('.mv-effects-right #transformPresetSlide').val(e.transformPreset)
			}
			if (e.effect=='pusher') {
				$('#pushDelay').val(e.pushDelay),
				$('#pushTime').val(e.pushTime),
				$('#afterPushHoldTime').val(e.afterPushHoldTime)
			}
			if (e.effect=='typewriter') {
				$('#timingPreset').val(e.timingPreset),
				$('#transformPresetTW').val(e.transformPreset),
				$('#textHoldTime').val(e.textHoldTime)
			}
			
			//Init Pusher Effect
		}
		//Switch Effect
		$('.mv-effects-left > a').click(function(e){
			e.preventDefault()
			if (isPlaying()) return;
			$('.active-fx').removeClass('active-fx')
			var index = $(this).addClass('active-fx').index()
			$('.mv-effects-right > div:eq('+index+')').addClass('active-fx')
			
			var effect = $(this).attr('data-fx')
			applyEffect(effect)
		})
		//Save Effect Settings
		$('.mv-effects-right button').click(function(e){
			e.preventDefault()
			if (isPlaying()) return;
			applyEffect( $(this).parent().parent().attr('data-fx') )
			$(this).next().fadeIn(100).delay(200).fadeOut(100)	
			$('.mv-effects').fadeOut(400)
		})
	
		//Apply Effect
		function applyEffect(effect) {
			if (effect=='slide') {	
				mWP.e = {
					effect : 'slide',
					textLineDelay : $('#textLineDelay').val(),
					moveWidth : $('#slide-moveWidth').val()*1,
					moveTime : $('#slide-moveTime').val()*1,
					crossSideLines : $('#crossSideLines:checked').length>0 ? true : false,
					lineFrom : $('#lineFrom').val(),
					transformPreset :  $('#transformPresetSlide').val()
				}
			}
			if (effect=='pusher') {
				mWP.e = {
					effect : 'pusher',
					pushDelay : $('#pushDelay').val(),
					pushTime:$('#pushTime').val(),
					afterPushHoldTime:$('#afterPushHoldTime').val()
				}
			}
			if (effect=='typewriter') {
				mWP.e = {
					effect : 'typewriter',
					waitForImage : true,
					timingPreset : $('#timingPreset').val(),
					transformPreset :  $('#transformPresetTW').val(),
					textHoldTime : $('#textHoldTime').val()
				}
			}
		}
		
		//Load Toolbar
		$(".manage-edit").live('click',function(){
			var slideIndex = $(this).parents('li').index()
			$('body').addClass('moover-editor-on')
			$('.moover-layer').removeClass('moover-hidden-layer');	
			$('.moover-toolbar').delay(700).animate({top:0},200,function(){
				$('.moover-slider').show()	
				var width = $('#moover-d-width').val();
				mWP.isResponsive = false;
				if (width.indexOf('%')>=0) {
					mWP.isResponsive = true;	
				}
				$('.moover-slider').css({
					width:$('#moover-d-width').val(),
					height:$('#moover-d-height').val(),
					left:0	,
					marginLeft:0
				})
				mWP.offset = $('.moover-slider').offset().left
				$('.moover-slider').css({
					marginLeft:!mWP.isResponsive ? -$('#moover-d-width').val()/2 - mWP.offset/2 : 0,
					left:!mWP.isResponsive?'50%':0,
					top: $(window).scrollTop()+100
				})
				loadMooverSlide( slideIndex )
			})
		})
		$('.mv-tb-inverse').click(function(){
			$('.moover-layer').toggleClass('inversed')	
		})
		//Open FX Tab
		$('.mv-tb-fx').hover(
			function(){
				$('.mv-effects').show()
			},
			function(){}
		)
		//Add Image
		$('.mv-tb-bg > a').click(function(e){
			e.preventDefault()
			if (isPlaying()) return;
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			mWP.insetImageType = 'bg'
		})
		$('.mv-tb-image').click(function(e){
			e.preventDefault()
			if (isPlaying()) return;
			tb_show('', 'media-upload.php?type=image&TB_iframe=true');
			mWP.insetImageType = 'object'
		})
		window.send_to_editor = function(html) {
			imgurl = $('img',html).attr('src');
			mWP.bgURL = imgurl
			tb_remove();
			if (mWP.insetImageType=='bg') {
				$('.moover-slider .moover-slide>img').remove()
				$('.moover-slider .moover-slide').prepend('<img src="'+mWP.bgURL+'">')
				var dummyImage = new Image();
				dummyImage.onload = function(){
					$('.moover-slider .moover-slide>img').css({
						width:	dummyImage.width
					})	
				}
				dummyImage.src = mWP.bgURL
				mWP.insetImageType = false
			}
			if (mWP.insetImageType=='object') {
				$('.moover-slider .moover-text').append('<p style="font-size:25px; line-height:35px; padding:0; background:none"><span><img src="'+mWP.bgURL+'"></span></p>')
				$('.moover-slider .moover-text p:last-child').draggable(dragOptions)
				var dummyImage = new Image();
				dummyImage.onload = function(){
					$('.moover-slider .moover-text p:last-child span img').css({
						width:	dummyImage.width,
						height: dummyImage.height
					})	
				}
				dummyImage.src = mWP.bgURL
				mWP.insetImageType = false
			}
		}
		//Remove BG
		$('.mv-removeBG').click(function(e){
			e.preventDefault();
			if (isPlaying()) return;
			$('.moover-slider .moover-slide>img').remove()
		})
		//Add Text
		$('.mv-tb-text').click(function(e){
			e.preventDefault();
			if (isPlaying()) return;
			$('.moover-slider .moover-text').append('<p style="font-size:25px; color:#666; line-height:35px; padding:0; background-color:none">Text Line</p>')
			$('.moover-slider .moover-text p:last-child').draggable(dragOptions)
		})
		$('.moover-slider .moover-text p').live('mouseover',function(e){
			if (isPlaying()) return;
			clearTimeout(mWP.timeout)
			$('.mv-tb-controls').show()	
			.css({
				left: $(this).offset().left - mWP.offset,
				top: $(this).offset().top-38	
			})	
			mWP.hovered = $(this)
		})
		$('.moover-slider .moover-text p').live('mouseleave',function(e){
			mWP.timeout = setTimeout(function(){
				$('.mv-tb-controls').hide()
			},1000)
		})
		$('.mv-tb-controls').hover(function(){ clearTimeout(mWP.timeout) }, function(){ $(this).hide() })
		$('.mv-tb-controls .mv-tb-remove').click(function(){
			if (isPlaying()) return;
			mWP.hovered.remove()
			$('.mv-tb-controls').fadeOut(200)
			mWP.hovered = undefined;	
		})
		//Click Copy-Line/Image
		$('.mv-tb-copy').click(function(e){
			if (isPlaying()) return;
			mWP.hovered.clone().appendTo('.moover-slider .moover-text');
			$('.moover-slider .moover-text p:last-child').css({
				left:mWP.hovered.position().left+10,
				top:mWP.hovered.position().top+30
			}).draggable(dragOptions)
		})
		//Click Edit-Line/Image
		$('.mv-tb-controls .mv-tb-edit').click(function(){
			if (isPlaying()) return;
			$('.mv-tb-editor').show()
			.css({
				left:parseInt($('.mv-tb-controls').css('left'),10)+20,
				top:parseInt($('.mv-tb-controls').css('top'),10)+25
			})
			$('.mv-tb-editor textarea').val( mWP.hovered.html() );
			$('.mv-tb-editor input[name="mv-fs"]').val( mWP.hovered.css('font-size') );	
			$('.mv-tb-editor input[name="mv-color"]').val( toHex( mWP.hovered.css('color') ) );
			$('.mv-tb-editor input[name="mv-bg"]').val( toHex( mWP.hovered[0].style.backgroundColor) );
			$('.mv-tb-editor input[name="mv-fw"]').val( mWP.hovered.css('font-weight') );
			$('.mv-tb-editor input[name="mv-p"]').val( parseInt(mWP.hovered.css('padding-top'),10) + 'px ' + parseInt(mWP.hovered.css('padding-right'),10) + 'px ' + parseInt(mWP.hovered.css('padding-bottom'),10) + 'px ' + parseInt(mWP.hovered.css('padding-left'),10) + 'px' );
		})
		$('.mv-tb-editor .button-primary').click(function(e){
			e.preventDefault()
			$('.mv-tb-editor').hide()
			var fs = $('.mv-tb-editor input[name="mv-fs"]').val();	
			var color = $('.mv-tb-editor input[name="mv-color"]').val();
			var bg = $('.mv-tb-editor input[name="mv-bg"]').val();
			var lh = parseInt(fs,10)+10+'px';
			var fw = $('.mv-tb-editor input[name="mv-fw"]').val();
			var p = $('.mv-tb-editor input[name="mv-p"]').val();
			mWP.hovered.css({
				fontSize:fs,
				color:color,
				backgroundColor:bg,
				lineHeight:lh,
				fontWeight:fw,
				padding:p
			})
			.html( $('.mv-tb-editor textarea').val() )
		})
		$('.mv-tb-editor .button-secondary').click(function(e){
			e.preventDefault();
			if (isPlaying()) return;
			$('.mv-tb-editor').hide()
		})
		
		//Save Changes
		$('.mv-tb-save').click(function(e){
			e.preventDefault();
			if (isPlaying()) return;
			var slideHTML = $('.moover-slider').html()
			$('.moover-slides li:eq('+mWP.activeIndex+')').find('textarea[name="moover-html[]"]').val( slideHTML )
			$('.moover-slides li:eq('+mWP.activeIndex+')').find('.moover-html').html( slideHTML )
			$('.moover-layer').addClass('moover-hidden-layer')
			$('.moover-toolbar').animate({top:-50},200)
			$('.moover-slider,.mv-tb-controls, .mv-tb-editor').hide()
			$('.mv-effects').hide()
			var bgImg = $('.moover-slider .moover-slide > img')
			if ( bgImg.length > 0 ) {
				$('.moover-slides li:eq('+mWP.activeIndex+')').css({backgroundImage:'url('+bgImg.attr('src')+')'}) 	
			}
			var slideJS = '{';
			var numOfProps = 0,flag = 0;
			for (var key in mWP.e) {
				numOfProps++
			}
			for (var key in mWP.e) {
				flag++;
				var varType = typeof(mWP.e[key])
				if(  varType == 'boolean' || varType == 'number' ) slideJS+=key+':'+mWP.e[key]
				else slideJS+=key+':"'+mWP.e[key]+'"';
				if ( flag!=numOfProps ) slideJS+=','
			}
			slideJS+='}';
			$('.moover-slides li:eq('+mWP.activeIndex+')').find('textarea[name="moover-js[]"]').val( slideJS )
			$('.moover-slides li:eq('+mWP.activeIndex+')').find('.moover-js').html( slideJS )
		})
		//Discard Changes
		$('.mv-tb-discard').click(function(e){
			e.preventDefault();
			if (isPlaying()) return;
			$('.moover-layer').addClass('moover-hidden-layer')
			$('.moover-toolbar').animate({top:-50},200)
			$('.moover-slider,.mv-tb-controls, .mv-tb-editor').hide()
			$('.mv-effects').hide()
		})
		//Preview Slide
		$('.mv-tb-play').live('click', function(e){
			e.preventDefault();
			mWP.beforePreviewHTML = $('.moover-slider').html()
			$('.moover-slider').html('')
			for (var i=0;i<2;i++) {
				$('.moover-slider').append(mWP.beforePreviewHTML).find('.moover-slide').hide()
			}
			var params = {
				moveTime:$('#moveTime').val(),
				moveWidth:$('#moveWidth').val()*1,
				scaleImage:$('#scaleImage').val()==='false'?false:$('#scaleImage').val()*1,
				moveImage:$('#moveImage').val()==='false'?false:true,
				slideTime:$('#slideTime').val(),
				disableCSS3:$('#disableCSS3').val()==='false'?false:true,
				moveOutImage: true
			}
			$.extend(params, mWP.e)
			$('.moover-slider').moover(params)
			$(this).toggleClass('mv-tb-play').toggleClass('mv-tb-stop').children('div').html('Stop Preview')
			$('body').addClass('moover-preview')
		})
		$('.mv-tb-stop').live('click', function(e){
			e.preventDefault();
			var style = $('.moover-slider').attr('style')
			$('.moover-slider').remove()
			$('<div class="moover-slider moover" style="'+style+'">'+mWP.beforePreviewHTML+'</div>').insertAfter($('.moover-toolbar'))
			$('body').removeClass('moover-preview')
			$(this).toggleClass('mv-tb-play').toggleClass('mv-tb-stop').children('div').html('Preview Slide')
			$('.moover-slider .moover-text p').draggable(dragOptions)
		})
		/*
		 *	mOover Toolbar Ends
		*/
		
		//Twitter
		$("#moover-tweets").idTwitter({
			username : "idangerous",
			numberOfTweets : 6,
			loadingText : '<li class="tweets_load">Loading tweets...</li>',
			tweetFormat : '<li class="single-tweet">'
						+ '<p class="tweet-text">'
						+ '%tweetText'
						+ '</p>'
						+ '<p class="tweet-date">on %tweetDate</p>'
						+ '</li>'	
		})
		//Check for number of images
		/*$("#moover-addnew-form").submit(function(e){
			var slidesNum = 0;
			$(".cs-upload_image").each(function(){
				var imgPath = $(this).val()	
				if(imgPath!="") slidesNum++;
			})
			if (slidesNum<2) {
				e.preventDefault()
				alert("You need to choose/upload at least 2 images");	
			}
		})*/
		//Status message
		$(".moover-status-close").click(function(){
			$(this).parent(".moover-status").slideUp(600)
		})
		
		//Togglers
		$(".moover-expand-tg").click(function(e){
			e.preventDefault();
			var tContent = $(this).parents(".postbox").find(".moover-tg-content")
			if( tContent.css('display')=="none" ) {
				tContent.slideDown(600);
				$(this).text("Close")	
			}
			else {
				tContent.slideUp(600);
				$(this).text("Open")
			}
		})
		
		
		//mOover Bulk Actions
		$("input#submit-moover-bulk-actions").click(function(e){
			e.preventDefault();
			if ( $("select[name='moover-action']").val()=="delete" ) {
				if (confirm('Are you sure you want to remove all the selected Sliders?')) {
					jQuery('#moover-ba-form').submit()
				}
			}
			else {
				$('#moover-ba-form').submit()
			}
		})
		$("#moover-ba-form input[name='moover-id[]'][value='all']").change(function(e){
			if($(this).is(":checked")) {
				$(this).parents("table").find("input[type='checkbox']").attr({'checked':'checked'})	
			}
			else {
				$(this).parents("table").find("input[type='checkbox']").removeAttr('checked')	
			}
		})
		//Single Select
		$("#moover-ba-form input[type='checkbox'][value!='all']").click(function(){
			$(this).parents('table').find("input[type='checkbox'][value='all']").removeAttr("checked")
		})
		//Delete Moover
		$(".moover_remove").click(function(e){
			e.preventDefault();
			if (confirm('Are you sure you want to remove this mOover?')) {
				document.location = $(this).attr("href")
			}
		})
		
	})
	
})(jQuery);
/*
iDangero.us jQuery Twitter Feed
------------------------
Version - 1.1
*/
(function($) {
	$.fn.idTwitter = function(a,callback) {
		var tweetContainer = this;
		if (tweetContainer.length==0) return;
		this.html(a.loadingText)
		$.getJSON("http://api.twitter.com/1/statuses/user_timeline.json?screen_name="+a.username+"&count="+a.numberOfTweets+"&include_entities=true&include_rts=true&callback=?",
		function(tweetData){
			tweetContainer.html("")
			$.each(tweetData, function(i,tweet) {
				var tweetDate = tweet.created_at.substr(0,20);
				var tweetText = tweet.text;
				for (var i =0; i<tweet.entities.user_mentions.length; i++) {
					var mentioned = tweet.entities.user_mentions[i].screen_name;
					tweetText = tweetText.replace('@'+mentioned,'<a href="http://twitter.com/'+mentioned+'">@'+mentioned+'</a>')	
				}
				for (var i =0; i<tweet.entities.urls.length; i++) {
					var uRL = tweet.entities.urls[i].url;
					tweetText = tweetText.replace(uRL,'<a href="'+uRL+'">'+uRL+'</a>')	
				}
				var readyTweet = a.tweetFormat.replace(/%username/g,a.username)
					readyTweet = readyTweet.replace(/%tweetDate/g,tweetDate)
					readyTweet = readyTweet.replace(/%tweetText/g,tweetText)
				tweetContainer.append(readyTweet);
			})
			if(callback) callback()
		})
	}
})(jQuery);