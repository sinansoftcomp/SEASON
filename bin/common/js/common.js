$(function(){
	mResizeCheck = $(window).width();
	npos = $(window).scrollTop();
	SW	=	$(window).width();
	SH	=	$(window).height();		
	GNB.init();
	SIDE.init();


	$('.toggle_tab').find('.btn_toggle').click(function(){
		//console.log('click');
		if($(this).parents('.toggle_tab').hasClass('open')){
			$(this).parents('.toggle_tab').removeClass('open');
			$(this).parents('.toogle_data').stop(true).animate({'width':0},400);
		}else{
			$(this).parents('.toggle_tab').addClass('open')
			$(this).parents('.toogle_data').stop(true).animate({'width':'100%'},400);
		}
	});

});//end ready

$(window).scroll(function() {			
	npos = $(window).scrollTop();
	SW	=	$(window).width();
	SH	=	$(window).height();
	
});//end scroll

$(window).resize(function(){
	if (mResizeCheck != $(window).width()) {
		npos = $(window).scrollTop();
		SW	=	$(window).width();
		SH	=	$(window).height();
		SIDE.resize();
		mResizeCheck = $(window).width();
	}

});//end resize

var SIDE = {
	init:function(){
		SIDE.resize();
		$('#side_bar').mouseenter(function(){
			if($(this).hasClass('fix')){
				$(this).find('.btn_toggle').css({'opacity':0});
				$('#side_bar').stop(true).animate({'right':0},300,function(){
					$(this).find('.btn_toggle').hide();	
				});
			}
		});
		$('#side_bar').mouseleave(function(){
			if($(this).hasClass('fix')){
				$(this).find('.btn_toggle').css({'opacity':1});
				$(this).stop(true).animate({'right':-352},300,function(){
					$(this).find('.btn_toggle').show();
				});
			}
		});
	},
	resize:function(){
		if(SW <= 1100){
			$('#side_bar').addClass('fix');
			$('#contents').addClass('fix');
		}else{
			$('#side_bar').removeClass('fix');
			$('#side_bar').attr('style','');
			$('#side_bar').find('.btn_toggle').attr('style','');
			$('#contents').removeClass('fix');
		}
	}
}


var GNB = {
	target:null,
	init:function(){
		//console.log('init');
		GNB.target = $('#nav');
		GNB.target.find('.lnb_list > li > a').bind('click',function(event){		
			if(!$(this).parent().hasClass('open')){
				GNB.clickHand($(this).parent().index());
			}			
		});
	},
	clickHand:function(_i){
		GNB.target.find('.lnb_list > li').each(function(){
			if($(this).index() == _i){
				$(this).find('.tnbMenu').stop(true).slideDown(300);
				$(this).addClass('open');
			}else{
				$(this).find('.tnbMenu').stop(true).slideUp(300);
				$(this).removeClass('open');
			}
		});
	}
}