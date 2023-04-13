(function($) { "use strict";
	
	/* Content */
	
	var $header = (jQuery("div.hidden-header").length?jQuery("div.hidden-header").outerHeight():0);
	var $footer = (jQuery("footer.footer").length?jQuery("footer.footer").outerHeight():0);
	var $wpadminbar = (jQuery(".admin-bar #wpadminbar").length?jQuery(".admin-bar #wpadminbar").outerHeight():0);
	var header_footer = ($header+$footer+$wpadminbar);
	jQuery(".page-main").css({"min-height":"calc( 100vh - "+header_footer+"px )"});
	
	/* Vars */
	
	var $window = jQuery(window);
	var is_RTL  = jQuery('body').hasClass('rtl')?true:false;
	
	/* Menu */
	
	jQuery("nav.nav ul li ul").parent("li").addClass("parent-list");
	jQuery(".parent-list").find("a:first").append(" <span class='menu-nav-arrow'><i class='icon-right-open-mini'></i></span>");
	
	jQuery("nav.nav ul a").removeAttr("title");
	jQuery("nav.nav ul ul").css({display: "none"});
	jQuery("nav.nav ul li").each(function() {
		var sub_menu = jQuery(this).find("ul:first");
		jQuery(this).hover(function() {
			sub_menu.stop().css({overflow:"hidden", height:"auto", display:"none", paddingTop:0}).slideDown(200, function() {
				jQuery(this).css({overflow:"visible", height:"auto"});
			});	
		},function() {
			sub_menu.stop().slideUp(50, function() {
				jQuery(this).css({overflow:"hidden", display:"none"});
			});
		});	
	});
	
	/* Header fixed */
	
	var fixed_enabled = jQuery("#wrap").hasClass("fixed-enabled");
	if (fixed_enabled && jQuery(".header").length) {
		var hidden_header = jQuery(".hidden-header").offset().top;
		if (hidden_header < 40) {
			var aboveHeight = -20;
		}else {
			var aboveHeight = hidden_header;
		}
		if (jQuery(".mobile_apps_bar_active").length && $window.width() < 992) {
			aboveHeight = aboveHeight+60;
		}
		var cachedWidth = $window.width();
		$window.on("resize",function() {
			var newWidth = $window.width();
			if (newWidth !== cachedWidth) {
				cachedWidth = newWidth;
				if (newWidth > 991) {
					jQuery(".fixed-enabled-removed").addClass("fixed-enabled w-fixed-hidden-header").removeClass("fixed-enabled-removed");
				}else {
					jQuery(".fixed-enabled").addClass("fixed-enabled-removed").removeClass("fixed-enabled w-fixed-hidden-header");
				}
			}
		});
		$window.scroll(function() {
			if ($window.scrollTop() > aboveHeight && $window.scrollTop() > 0) {
				jQuery(".header").css({"top":"0"}).addClass("fixed-nav");
				jQuery("#wrap").addClass("fixed-wrap");
			}else {
				jQuery(".header").css({"top":"auto"}).removeClass("fixed-nav");
				jQuery("#wrap").removeClass("fixed-wrap");
			}
		});
	}else {
		jQuery(".header").removeClass("fixed-nav");
		jQuery("#wrap").removeClass("fixed-wrap");
	}
	
	/* Header mobile */
	
	jQuery("nav.nav > ul > li").clone().appendTo('.navigation_mobile > ul');
	
	/* User login */
	
	if (jQuery(".user-click").length) {
		jQuery(".user-click:not(.user-click-not)").on("click",function () {
			jQuery(".user-notifications.user-notifications-seen").removeClass("user-notifications-seen").find(" > div").slideUp(200);
			jQuery(".user-messages > div").slideUp(200);
			jQuery(this).parent().toggleClass("user-click-open").find(" > ul").slideToggle(200);
		});
	}
	
	/* Tipsy */
	
	jQuery(".tooltip-n").tipsy({fade:true,gravity:"s"});
	jQuery(".tooltip-s").tipsy({fade:true,gravity:"n"});
	jQuery(".tooltip-nw").tipsy({fade:true,gravity:"nw"});
	jQuery(".tooltip-ne").tipsy({fade:true,gravity:"ne"});
	jQuery(".tooltip-w").tipsy({fade:true,gravity:"w"});
	jQuery(".tooltip-e").tipsy({fade:true,gravity:"e"});
	jQuery(".tooltip-sw").tipsy({fade:true,gravity:"sw"});
	jQuery(".tooltip-se").tipsy({fade:true,gravity:"se"});
	
	/* Nav menu */
	
	if (jQuery("nav.nav_menu").length) {
		jQuery(".nav_menu > ul > li > a,.nav_menu > div > ul > li > a,.nav_menu > div > div > ul > li > a").on("click",function () {
			var li_menu = jQuery(this).parent();
			if (li_menu.hasClass("make-it-clickable")) {
				return true;
			}else {
				if (li_menu.find(" > ul").length) {
					if (li_menu.hasClass("nav_menu_open")) {
						li_menu.find(" > ul").slideUp(200,function () {
							li_menu.removeClass("nav_menu_open");
						});
					}else {
						li_menu.find(" > ul").slideDown(200,function () {
							li_menu.addClass("nav_menu_open");
						});
					}
					StickySidebarContent();
					return false;
				}
			}
		});
	}
	
	/* Mobile aside */

	jQuery('.open-mobileMenu').on('click',function () {
		if (jQuery('.menu-closed').length == 0) {
			jQuery('.hidden-header,.header-transparent').addClass('header-menu-opened').delay(100).queue(function(){
				jQuery(".fixed-enabled").addClass("fixed-enabled-removed").removeClass("fixed-enabled w-fixed-hidden-header");
				jQuery('.navbar-collapse').addClass('menu-opened').dequeue();
				jQuery('body').addClass("body-menu-opened").prepend("<div class='overlay-body'></div>");
			});
		}
	})
	jQuery('#mainNavigation').find('li.menu-item-has-children').each(function () {
		if (!jQuery(this).find(".mobile-arrows").length) {
			jQuery(this).append('<span class="mobile-arrows"><i class="icon-arrow-down-b"></i></span>');
		}
	});
	if (jQuery('#mainNavigation ul.menu > li').length) {
		jQuery('#mainNavigation li.menu-item-has-children > .mobile-arrows').on("click",function(){
			jQuery(this).parent().find('ul:first').slideToggle(200);
			jQuery(this).parent().find('> .mobile-arrows').toggleClass('mobile-arrows-open');
			return false;
		});
	}
	jQuery(document).on('click','.close-mobileMenu,.overlay-body',function () {
		jQuery(".fixed-enabled-removed").addClass("fixed-enabled w-fixed-hidden-header").removeClass("fixed-enabled-removed");
		jQuery('body').removeClass("body-menu-opened");
		jQuery('.overlay-body').remove();
		jQuery('.navbar-collapse').removeClass('menu-opened');
		jQuery('.navbar-collapse').addClass('menu-closed');
		setTimeout(function () { 
			jQuery('.hidden-header,.header-transparent').removeClass('header-menu-opened').dequeue();
			jQuery('.navbar-collapse').removeClass('menu-closed');
		},1000);
	})
	
	/* Set Background-img to section */

	jQuery('.bg-img').each(function () {
		var imgSrc = jQuery(this).children('img').attr('src');
		jQuery(this).parent().css({
			'background-image': 'url(' + imgSrc + ')',
			'background-size': 'cover',
			'background-position': 'center',
		});
		jQuery(this).parent().addClass('bg-img');
		jQuery(this).remove();
	});

	jQuery(".js-activate-slider").each(function () {
		var $this = jQuery(this),
		data_js = $this.data("js"),
		vids = $this.find(" > div > div > div");
		for(var i = 0; i < vids.length; i+=data_js) {
		    vids.slice(i,i+data_js).wrapAll('<div class="slider-custom-js slider-item"></div>');
		}
	});
	
	/* Filter By Gender */

	var $FilterByGender = jQuery(".filter-enabled #FilterByGender"),
	$him = jQuery('.filter-enabled .widget-article-wide.him'),
	$her = jQuery('.filter-enabled .widget-article-wide.her');

	jQuery(window).on('load', function () {
		if (!$FilterByGender.is(':checked')) {
			$her.css('display', 'none');
		}
	})

	$FilterByGender.on('change', function () {
		if (jQuery(this).is(':checked')) {
			$him.css('display', 'none');
			$her.css('display', 'block');
		}else {
			$her.css('display', 'none');
			$him.css('display', 'block');
		}
	});
	
	/* Post share */
	
	if (jQuery(".article-post-only .post-share").length) {
		var cssArea = (is_RTL == true?"left":"right");
		jQuery(".article-post-only .post-share").each(function () {
			var share_width = jQuery(this).find(" > ul").css({"position":"static"}).outerWidth()+20;
			jQuery(this).find(" > ul").css({"position":"absolute"}).css(cssArea,"-"+share_width+"px");
		});
	}
	
	/* Go up */
	
	$window.scroll(function () {
		var cssArea = (is_RTL == true?"left":"right");
		if (jQuery(this).scrollTop() > 700) {
			jQuery(".go-up").css(cssArea,"20px");
			jQuery(".ask-button").css(cssArea,(jQuery(".go-up").length?"80px":"20px"));
			jQuery(".ask-button").addClass('actived');
		}else {
			jQuery(".go-up").css(cssArea,"-60px");
			jQuery(".ask-button").css(cssArea,"20px");
			jQuery(".ask-button").removeClass('actived');
		}
	});
	
	jQuery(".go-up").on("click",function(){
		jQuery("html,body").animate({scrollTop:0},500);
		return false;
	});

	var $scrollTopBtn = jQuery('#scrollTopBtn');
	jQuery(window).on('scroll', function () {
		if (jQuery(this).scrollTop() > 700) {
			$scrollTopBtn.addClass('actived');
		}else {
			$scrollTopBtn.removeClass('actived');
		}
	});

	$scrollTopBtn.on('click', function () {
		jQuery('html, body').animate({
			scrollTop: 0
		}, 500);
	});
	
	/* Tabs */
	
	if (jQuery(".widget ul.tabs").length) {
		jQuery(".widget ul.tabs").tabs(".widget .tab-inner-wrap",{effect:"slide",fadeInSpeed:100});
	}
	
	if (jQuery("ul.tabs-box").length) {
		jQuery("ul.tabs-box").tabs(".tab-inner-wrap-box",{effect:"slide",fadeInSpeed:100});
	}
	
	/* Owl */
	
	if (jQuery(".slider-owl").length) {
		jQuery(".slider-owl").each(function () {
			var $slider = jQuery(this);
			var $slider_item = $slider.find('.slider-item').length;
			$slider.find('.slider-item').css({"height":"auto"});
			if ($slider.find('img').length) {
				var $slider = jQuery(this).imagesLoaded(function() {
					$slider.owlCarousel({
						autoplay: 5000,
						margin: 10,
						responsive: {
							0: {
								items: 1
							}
						},
						autoplayHoverPause: true,
						navText : ["", ""],
						nav: ($slider_item > 1)?true:false,
						rtl: is_RTL,
						loop: ($slider_item > 1)?true:false,
						autoHeight: true
					});
				});
			}else {
				$slider.owlCarousel({
					autoplay: 5000,
					margin: 10,
					responsive: {
						0: {
							items: 1
						}
					},
					autoplayHoverPause: true,
					navText : ["", ""],
					nav: ($slider_item > 1)?true:false,
					rtl: is_RTL == true,
					loop: ($slider_item > 1)?true:false,
					autoHeight: true
				});
			}
		});
	}
	
	/* Flex menu */
	
	if (jQuery("ul.menu.flex").length) {
		jQuery('ul.menu.flex').flexMenu({
			threshold   : 0,
			cutoff      : 0,
			linkText    : '<i class="icon-dot-3"></i>',
			linkTextAll : '<i class="icon-dot-3"></i>',
			linkTitle   : '',
			linkTitleAll: '',
			showOnHover : ($window.width() > 991?true:false),
		});
		
		jQuery("ul.menu.flex .active-tab,ul.menu.flex .active").closest(".menu-tabs").addClass("active-menu");
	}
	
	if (jQuery("nav.nav ul").length) {
		jQuery('nav.nav ul').flexMenu({
			threshold   : 0,
			cutoff      : 0,
			linkText    : '<i class="icon-dot-3"></i>',
			linkTextAll : '<i class="icon-dot-3"></i>',
			linkTitle   : '',
			linkTitleAll: '',
			showOnHover : ($window.width() > 991?true:false),
		});
		
		jQuery("nav.nav ul .active-tab").parent().parent().addClass("active-menu");
	}
	
	/* Select */
	
	if (jQuery(".widget select,.post-content-text select").length) {
		jQuery(".widget select,.post-content-text select").addClass("form-control").wrap('<div class="styled-select"></div>');
	}
	
	/* Lightbox */
	
	if (jQuery(".active-lightbox").length) {
		var lightboxArgs = {
			animation_speed: "fast",
			overlay_gallery: true,
			autoplay_slideshow: false,
			slideshow: 5000,
			theme: "facebook", 
			opacity: 0.8,
			show_title: false,
			social_tools: "",
			deeplinking: false,
			allow_resize: false,
			counter_separator_label: "/",
			default_width: 500,
			default_height: 344,
			horizontal_padding: 20
		};
		
		jQuery("a[href$=jpg], a[href$=JPG], a[href$=jpeg], a[href$=JPEG], a[href$=png], a[href$=gif], a[href$=bmp]:has(img)").prettyPhoto(lightboxArgs);
		jQuery("a[class^='prettyPhoto'], a[rel^='prettyPhoto']").prettyPhoto(lightboxArgs);
	}
	
	/* 2 columns questions */
	
	if (jQuery(".article-question.post-with-columns").length) {
		if (jQuery(".article-question.post-with-columns.question-masonry").length) {
			jQuery(".article-question.post-with-columns.question-masonry").closest(".question-articles").isotope({
				filter: "*",
				animationOptions: {
					duration: 750,
					itemSelector: '.question-masonry',
					easing: "linear",
					queue: false,
				}
			});
		}else {
			jQuery(".article-question.post-with-columns").matchHeight();
			jQuery(".article-question.post-with-columns > .single-inner-content").matchHeight();
		}
	}
	
	/* 2 columns posts */
	
	if (jQuery(".section-post-with-columns .post-articles").length) {
		if (jQuery(".section-post-with-columns .post-articles .post-masonry").length) {
			jQuery(".section-post-with-columns .post-articles .post-masonry").closest(".post-articles").isotope({
				filter: "*",
				animationOptions: {
					duration: 750,
					itemSelector: '.post-masonry',
					easing: "linear",
					queue: false,
				}
			});
		}
	}
	
	/* Load */
	
	$window.on('load',function() {
		
		/* Loader */
		
		jQuery(".loader").fadeOut(500);
		
		/* Users */
		
		if (jQuery(".user-section-small_grid,.user-section-simple,.user-section-small,.user-section-grid").length) {
			if (jQuery(".users-masonry").length) {
				jQuery(".user-masonry").closest(".users-masonry").isotope({
					filter: "*",
					animationOptions: {
						duration: 750,
						itemSelector: '.user-masonry',
						easing: "linear",
						queue: false,
					}
				});
			}else {
				jQuery('.user-section-small_grid,.user-section-simple,.user-section-small,.user-section-grid').each(function() {
					jQuery(this).find('> div > div').matchHeight();
				});
			}
		}
		
		if (jQuery(".user-section-columns,.user-section-small_grid").length) {
			if (jQuery(".users-masonry").length) {
				jQuery(".user-masonry").closest(".users-masonry").isotope({
					filter: "*",
					animationOptions: {
						duration: 750,
						itemSelector: '.user-masonry',
						easing: "linear",
						queue: false,
					}
				});
			}else {
				jQuery('.user-section-columns').each(function() {
					jQuery(this).find('.post-inner').matchHeight();
				});
			}
		}
		
		/* Badges & Tags & Categories */
		
		if (jQuery(".badge-section,.tag-sections,.points-section ul .point-section").length) {
			jQuery(".badge-section > *,.tag-sections,.points-section ul .point-section").matchHeight();
		}
		
		/* 2 columns questions */
		
		if (jQuery(".article-question.post-with-columns").length) {
			if (jQuery(".article-question.post-with-columns.question-masonry").length) {
				jQuery(".article-question.post-with-columns.question-masonry").closest(".question-articles").imagesLoaded(function() {
					jQuery(".article-question.post-with-columns.question-masonry").closest(".question-articles").isotope({
						filter: "*",
						animationOptions: {
							duration: 750,
							itemSelector: '.question-masonry',
							easing: "linear",
							queue: false,
						}
					});
				});
			}else {
				jQuery(".article-question.post-with-columns").matchHeight();
				jQuery(".article-question.post-with-columns > .single-inner-content").matchHeight();
			}
		}
		
		/* 2 columns posts */

		if (jQuery(".section-post-with-columns .post-articles").length) {
			if (jQuery(".section-post-with-columns .post-articles .post-masonry").length) {
				jQuery(".section-post-with-columns .post-articles .post-masonry").closest(".post-articles").imagesLoaded(function() {
					jQuery(".section-post-with-columns .post-articles .post-masonry").closest(".post-articles").isotope({
						filter: "*",
						animationOptions: {
							duration: 750,
							itemSelector: '.post-masonry',
							easing: "linear",
							queue: false,
						}
					});
				});
			}
		}
		
		/* Sticky Question */
		
		var sticky_sidebar = jQuery(".single-question .question-sticky");
		if (sticky_sidebar.length && $window.width() > 480) {
			jQuery(".single-question .question-vote-sticky").css({"width":sticky_sidebar.outerWidth()});
			jQuery('.single-question .question-vote-sticky').theiaStickySidebar({updateSidebarHeight: false, additionalMarginTop: (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0)+40,minWidth : sticky_sidebar.outerWidth()});
		}
		
		/* Questions */
		
		if (jQuery(".question-header-mobile").length) {
			$window.on("resize", function () {
				if (jQuery(this).width() < 480) {
					if (jQuery(".question-header-mobile").length) {
						jQuery(".article-question").each(function () {
							var question_mobile_h = jQuery(this).find(".question-header-mobile").outerHeight()-20;
							var author_image_h = jQuery(this).find(".author-image").outerHeight();
							jQuery(this).find(".author-image").css({"margin-top":(question_mobile_h-author_image_h)/2});
						});
					}
				}else {
					jQuery(".article-question .author-image,.question-image-vote,.question-image-vote .theiaStickySidebar").removeAttr("style");
					jQuery(".article-question .author-image").css({"width":"46px"});
					
					if (sticky_sidebar.length) {
						jQuery(".single-question .question-image-vote").css({"width":sticky_sidebar.outerWidth()});
						jQuery('.single-question .question-image-vote').theiaStickySidebar({updateSidebarHeight: false, additionalMarginTop: (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0)+40,minWidth : sticky_sidebar.outerWidth()});
					}
				}
			});
			
			if ($window.width() < 480) {
				if (jQuery(".question-header-mobile").length) {
					jQuery(".article-question").each(function () {
						var question_mobile_h = jQuery(this).find(".question-header-mobile").outerHeight()-20;
						var author_image_h = jQuery(this).find(".author-image").outerHeight();
						jQuery(this).find(".author-image").css({"margin-top":(question_mobile_h-author_image_h)/2});
					});
				}
			}
		}
		
		if (jQuery("section .question-articles > .article-question").length > 3 && jQuery("section .question-articles > .article-question .author-image-pop-2").length) {
			var last_question_h = jQuery("section .question-articles > .article-question:last-child").height();
			var last_popup_h = jQuery("section .question-articles > .article-question:last-child .author-image-pop-2").height();
			if (last_question_h < last_popup_h) {
				jQuery("section .question-articles > .article-question:last-child .author-image-pop-2").addClass("author-image-pop-top");
			}
			if (jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist").length) {
				var last_question_answer_h = jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist .comment").height();
				var last_answer_popup_h = jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist .comment .author-image-pop-2").height();
				if (last_question_answer_h < last_answer_popup_h) {
					jQuery("section .question-articles > .article-question:last-child .question-bottom > .commentlist .comment .author-image-pop-2").addClass("author-image-pop-top");
				}
			}
		}
		
		if ($window.width() > 991 && jQuery(".page-content.commentslist > ol > .comment").length > 3 && jQuery(".page-content.commentslist > ol > .comment .author-image-pop-2").length) {
			var last_answer_h = jQuery(".page-content.commentslist > ol > .comment:last-child").height();
			var last_popup_h = jQuery(".page-content.commentslist > ol > .comment:last-child .author-image-pop-2").height();
			if (last_answer_h < last_popup_h) {
				jQuery(".page-content.commentslist > ol > .comment:last-child .author-image-pop-2").addClass("author-image-pop-top");
			}
		}
		
	});
	
})(jQuery);

/* Sticky content */

jQuery.noConflict()(function himer_sidebar() {
	StickySidebarContent();
});

StickySidebarContent = () => {
	var main_wrap_h    = jQuery(".warp-main-content").outerHeight();
	var main_sidebar_h = jQuery(".sidebar-width").outerHeight();
	if (jQuery(".nav_menu_sidebar").length) {
		var nav_menu_h = jQuery(".nav_menu_sidebar").outerHeight();
	}else {
		var nav_menu_h = jQuery(".nav_menu").outerHeight();
	}
	if (jQuery('.menu_left').length && nav_menu_h > main_wrap_h) {
		//
	}else if ((main_wrap_h > nav_menu_h && jQuery(".fixed_nav_menu").length) || (main_wrap_h > main_sidebar_h && jQuery(".fixed-sidebar").length)) {
		var hidden_header = (jQuery("#wrap.fixed-enabled").length?jQuery(".hidden-header").outerHeight():0);
		var wpadminbar = (jQuery(".admin-bar #wpadminbar").length?jQuery(".admin-bar #wpadminbar").outerHeight():0);
		var marginTopHeight = hidden_header + wpadminbar + 10;
		var updateSidebarHeight = (jQuery(".widget-footer").length?false:true);
		var stickyClasses = (jQuery(".fixed_nav_menu").length?'.warp-main-content,.warp-sidebar,.warp-left-menu':'.warp-main-content,.warp-sidebar');
		jQuery(stickyClasses).theiaStickySidebar({updateSidebarHeight: updateSidebarHeight, additionalMarginTop: marginTopHeight});
	}
}

jQuery(window).on("sticky_recalc", StickySidebarContent);