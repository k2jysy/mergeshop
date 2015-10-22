/**
 * $Desc
 *
 * @version    $Id$
 * @package    wpbase
 * @author     Opal  Team <opalwordpressl@gmail.com >
 * @copyright  Copyright (C) 2014 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */


var WPO_Plugin = window.WPO_Plugin || {};

!function ($) {
	"use strict";
	$.extend(WPO_Plugin, {
		mm_Duration: $('#wpo-mainnav').data('duration') || 0,
		mm_Animation:$('#wpo-mainnav').data('animation') || '',
        oc_Variables:{
            wrapper : $('body'),
            inner : $('.wpo-wrapper'),
            toggles : $('.off-canvas-toggle'),
            offcanvas : $('.wpo-off-canvas'),
            close : $('.wpo-off-canvas .close'),
            btn:null,
            nav:null,
            direction : 'left',
            fixed : null
        },
		init: function(){
			// Tooltip Bootstrap
			$("[data-toggle='tooltip']").tooltip();

			// Blog Tabs Element Builder
			WPO_Plugin.BlogTab();

			//Megamenu
			//WPO_Plugin.MegamenuHover();
			WPO_Plugin.MegamenuDuration();
            WPO_Plugin.ocInit();
		},
        // Offcanvas
        // 
        ocInit : function(){
            // no wrapper, just exit
            if (!WPO_Plugin.oc_Variables.wrapper.length) return ;

            // add effect class for nav
            WPO_Plugin.oc_Variables.toggles.each (function () {
                var $this = $(this);
                WPO_Plugin.oc_Variables.nav = $($this.data('nav'));
                var effect = $this.data('effect');
                WPO_Plugin.oc_Variables.direction = ($('html').attr('dir') == 'rtl' && $this.data('pos')!='right') || ($('html').attr('dir') != 'rtl' && $this.data('pos')=='right')  ? 'right':'left';
                WPO_Plugin.oc_Variables.nav.addClass (effect).addClass ('off-canvas-'+WPO_Plugin.oc_Variables.direction);

                // move to outside wrapper-content
                var inside_effect = ['off-canvas-effect-3','off-canvas-effect-16','off-canvas-effect-7','off-canvas-effect-8','off-canvas-effect-14'];
                if ($.inArray(effect, inside_effect) == -1) {
                    WPO_Plugin.oc_Variables.inner.before(WPO_Plugin.oc_Variables.nav);
                } else {
                    WPO_Plugin.oc_Variables.inner.prepend(WPO_Plugin.oc_Variables.nav);
                }
            });

            WPO_Plugin.oc_Variables.toggles.click (function(e){
                // detect direction

                WPO_Plugin.stopBubble (e);
                if (WPO_Plugin.oc_Variables.wrapper.hasClass ('off-canvas-open')) {
                    WPO_Plugin.oc_Variables.oc_hide (e);
                    return;
                }

                WPO_Plugin.oc_Variables.btn = $(this);
                WPO_Plugin.oc_Variables.nav = $(WPO_Plugin.oc_Variables.btn.data('nav'));
                WPO_Plugin.oc_Variables.fixed = WPO_Plugin.oc_Variables.inner.find('*').filter (function() {return $(this).css("position") === 'fixed';});

                WPO_Plugin.oc_Variables.nav.addClass ('off-canvas-current');

                WPO_Plugin.oc_Variables.direction = ($('html').attr('dir') == 'rtl' && WPO_Plugin.oc_Variables.btn.data('pos')!='right') || ($('html').attr('dir') != 'rtl' && WPO_Plugin.oc_Variables.btn.data('pos')=='right')  ? 'right':'left';

                // add direction class to body
                // $('html').removeClass ('off-canvas-left off-canvas-right').addClass ('off-canvas-' + direction);

                WPO_Plugin.oc_Variables.offcanvas.height($(window).height());


                // disable scroll on page
                var scrollTop = ($('html').scrollTop()) ? $('html').scrollTop() : $('body').scrollTop(); // Works for Chrome, Firefox, IE...
                $('html').addClass('noscroll').css('top',-scrollTop).data('top', scrollTop);
                $('.wpo-off-canvas').css('top',scrollTop);

                // make the fixed element become absolute
                WPO_Plugin.oc_Variables.fixed.each (function () {
                    var $this = $(this),
                        $parent = $this.parent(),
                        mtop = 0;
                    // find none static parent
                    while (!$parent.is(WPO_Plugin.oc_Variables.inner) && $parent.css("position") === 'static') $parent = $parent.parent();
                    mtop = -$parent.offset().top;
                    $this.css ({'position': 'absolute', 'margin-top': mtop});
                });

                WPO_Plugin.oc_Variables.wrapper.scrollTop (scrollTop);
                // update effect class
                WPO_Plugin.oc_Variables.wrapper[0].className = WPO_Plugin.oc_Variables.wrapper[0].className.replace (/\s*off\-canvas\-effect\-\d+\s*/g, ' ').trim() +
                    ' ' + WPO_Plugin.oc_Variables.btn.data('effect') + ' ' + 'off-canvas-' + WPO_Plugin.oc_Variables.direction;

                setTimeout(WPO_Plugin.oc_show, 50);

                return;
            });
        },
        //WPO_Plugin.oc_Variables.
        oc_show : function () {
            WPO_Plugin.oc_Variables.wrapper.addClass ('off-canvas-open');
            WPO_Plugin.oc_Variables.wrapper.on ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.close.on ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.offcanvas.on ('click', WPO_Plugin.stopBubble);

            // fix for old ie
            if ($.browser.msie && $.browser.version < 10) {
                var p1 = {}, p2 = {};
                p1['padding-'+direction] = $('.wpo-off-canvas').width();
                p2[direction] = 0;
                WPO_Plugin.oc_Variables.inner.animate (p1);
                WPO_Plugin.oc_Variables.nav.animate (p2);
            }
        },
        oc_hide : function () {
            WPO_Plugin.oc_Variables.wrapper.removeClass ('off-canvas-open');
            WPO_Plugin.oc_Variables.wrapper.off ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.close.off ('click', WPO_Plugin.oc_hide);
            WPO_Plugin.oc_Variables.offcanvas.off ('click', WPO_Plugin.stopBubble);
            setTimeout (function (){
                WPO_Plugin.oc_Variables.wrapper.removeClass (WPO_Plugin.oc_Variables.btn.data('effect')).removeClass ('off-canvas-'+WPO_Plugin.oc_Variables.direction);
                WPO_Plugin.oc_Variables.wrapper.scrollTop (0);
                // enable scroll
                $('html').removeClass ('noscroll').css('top', '');
                $('html,body').scrollTop ($('html').data('top'));
                WPO_Plugin.oc_Variables.nav.removeClass ('off-canvas-current');
                // restore fixed elements
                WPO_Plugin.oc_Variables.fixed.css ({'position': '', 'margin-top': ''});
            }, 550);

            // fix for old ie
            if ($.browser.msie && $.browser.version < 10) {
                var p1 = {}, p2 = {};
                p1['padding-'+direction] = 0;
                p2[direction] = -$('.wpo-off-canvas').width();
                WPO_Plugin.oc_Variables.inner.animate (p1);
                WPO_Plugin.oc_Variables.nav.animate (p2);
            }
        },

        stopBubble : function (e) {
            if($(e.target).hasClass('dropdown-toggle')){
                
            }else{
                e.stopPropagation();
            }
            
        },
		BlogTab : function(){
			$('[data-toggle="blog-tab"]').each(function(index, el) {
				var contain = $(this);
				var recent = contain.find('.recent-item');
				var child = contain.find('.category-child a');
				recent.hide();
				contain.find('.recent-item:first-child').show();
				child.hover(function(){
					var id = $(this).attr('data-item');
					recent.hide();
					contain.find(id).show();
				});
			});
		},
		MegamenuDuration: function(){
			if(WPO_Plugin.mm_Duration){
				$('<style type="text/css">' +
					'.wpo-megamenu.animate .animating > .dropdown-menu,' +
					'.wpo-megamenu.animate.slide .animating > .dropdown-menu > div {' +
						'transition-duration: ' + WPO_Plugin.mm_Duration + 'ms;' +
						'-webkit-transition-duration: ' + WPO_Plugin.mm_Duration + 'ms;' +
                        '-moz-transition-duration: ' + WPO_Plugin.mm_Duration + 'ms;' +
					'}' +
				'</style>').appendTo ('head');
			}
		}
	});

	$(document).ready(function(){
		WPO_Plugin.init();
	});

    $(window).resize(function(){
        WPO_Plugin.oc_Variables.offcanvas.height($(window).height());
    });

}(jQuery);
