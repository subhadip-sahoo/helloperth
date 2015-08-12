jQuery(document).ready(function () {

	$('.banner-slider').flexslider({
	    animation: "fade",
	    controlNav: false
	});

	$('.news-event-slider').flexslider({
	    animation: "fade",
	    controlNav: false
	});

	 $('.bxslider').bxSlider();
	 
    jQuery('header nav').meanmenu({
    	meanMenuContainer: '.header-nav-area',
    	meanScreenWidth: "1024"
    });
	
	jQuery(".container").fitVids();

  jQuery('.contact-form-grp').find('.site-heading').addClass('contact-title');
  
	jQuery('.perth-carousel').owlCarousel({
	    loop:true,
	    margin:10,
	    responsiveClass:true,
      dots:true,
      nav:true,
	    responsive:{
	        0:{
	            items:2,
	            margin: 1
	        },
	        768:{
	            items:2,
	        },
	        1025:{
	            items:4,
	            // loop:false,
	            margin: 35
	        }
	    }
	});

	jQuery('.inner-banner-carousel').owlCarousel({
	    center: true,
	    items:2,
	    loop:true,
	    margin:1,
	    nav: true,
	    dots:false,
	    responsive:{
	    	0:{
	            items:1,
	            margin: 1
	        },
	        480:{
	            items:2,
	            margin: 1
	        },
	        768:{
	            items:3,
	            margin: 1
	        },
	        1025:{
	            items:4,
	            margin: 1
	        },
	        1400:{
	            items:6,
	            margin: 1
	        }
	    }
	});

    $('.inner-blocks-cts-area').magnificPopup({
        delegate: '.perth-slider-box a',
        type: 'inline',
        fixedContentPos: true,
        fixedBgPos: true,

        overflowY: 'auto',

        closeBtnInside: true,
        preloader: false,

        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in',
        gallery: {
            enabled: true
        }
    });
    
    $(document).delegate('.asterik-toggle', 'click', function(){
        if($(this).data('status') == '0'){
            $(this).siblings('p').empty().text($(this).data('show'));
            $(this).data('status', '1');
        }
        else if($(this).data('status') == '1'){
            $(this).siblings('p').empty().text($(this).data('hide'));
            $(this).data('status', '0');
        }
    });
    
    $('.directories-checkbox input[type=checkbox]').addClass('validate[required] tax-default');
    
    $('#quick_contatc').validationEngine();
    $('#registration').validationEngine({'custom_error_messages' : {
            '#con_password': {
                'equals': {
                    'message': 'Password & Confirm password does not match.'
                }
            }
        }
    });
    $('#advertise-with-us').validationEngine({'custom_error_messages' : {
            '#con_password': {
                'equals': {
                    'message': 'Password & Confirm password does not match.'
                }
            }
        }
    });
    $('#add_directory').validationEngine({'custom_error_messages' : {
            '.tax-default' : {
                'required' : {
                    'message' : 'Please select at least one business diretcory category.'
                }
            }
        }
    });
    
    $(document).delegate('.accord-more','click', function(){
        var postid = $(this).data('postid');
        var $this = $(this);
        $.ajax({
            url: userSettings.url + 'wp-admin/admin-ajax.php',
            type: 'POST',
            data: {action: 'get_page_content', postid: postid},
            success: function(response){
                $this.closest('p').parent('.accordian-content').html(response);
            }
        });
    });
    
    $(document).delegate('.accord-more','click', function(){
        var postid = $(this).data('postid');
        var $this = $(this);
        $.ajax({
            url: userSettings.url + 'wp-admin/admin-ajax.php',
            type: 'POST',
            data: {action: 'get_page_content', postid: postid},
            success: function(response){
                $this.closest('.accordian-content-news').html(response);
            }
        });
    });
});
(function ($) {
  'use strict';

  // DROPDOWN CLASS DEFINITION
  // =========================

  var backdrop = '.dropdown-backdrop'
  var toggle   = '[data-toggle="dropdown"]'
  var Dropdown = function (element) {
    $(element).on('click.bs.dropdown', this.toggle)
  }

  Dropdown.VERSION = '3.3.4'

  Dropdown.prototype.toggle = function (e) {
    var $this = $(this)

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    clearMenus()

    if (!isActive) {
      if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
        // if mobile we use a backdrop because click events don't delegate
        $('<div class="dropdown-backdrop"/>').insertAfter($(this)).on('click', clearMenus)
      }

      var relatedTarget = { relatedTarget: this }
      $parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this
        .trigger('focus')
        .attr('aria-expanded', 'true')

      $parent
        .toggleClass('open')
        .trigger('shown.bs.dropdown', relatedTarget)
    }

    return false
  }

  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

    var $this = $(this)

    e.preventDefault()
    e.stopPropagation()

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('open')

    if ((!isActive && e.which != 27) || (isActive && e.which == 27)) {
      if (e.which == 27) $parent.find(toggle).trigger('focus')
      return $this.trigger('click')
    }

    var desc = ' li:not(.disabled):visible a'
    var $items = $parent.find('[role="menu"]' + desc + ', [role="listbox"]' + desc)

    if (!$items.length) return

    var index = $items.index(e.target)

    if (e.which == 38 && index > 0)                 index--                        // up
    if (e.which == 40 && index < $items.length - 1) index++                        // down
    if (!~index)                                      index = 0

    $items.eq(index).trigger('focus')
  }

  function clearMenus(e) {
    if (e && e.which === 3) return
    $(backdrop).remove()
    $(toggle).each(function () {
      var $this         = $(this)
      var $parent       = getParent($this)
      var relatedTarget = { relatedTarget: this }

      if (!$parent.hasClass('open')) return

      $parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this.attr('aria-expanded', 'false')
      $parent.removeClass('open').trigger('hidden.bs.dropdown', relatedTarget)
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = selector && $(selector)

    return $parent && $parent.length ? $parent : $this.parent()
  }


  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.dropdown')

      if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.dropdown

  $.fn.dropdown             = Plugin
  $.fn.dropdown.Constructor = Dropdown


  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }

  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document)
    .on('click.bs.dropdown.data-api', clearMenus)
    .on('click.bs.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    .on('keydown.bs.dropdown.data-api', toggle, Dropdown.prototype.keydown)
    .on('keydown.bs.dropdown.data-api', '[role="menu"]', Dropdown.prototype.keydown)
    .on('keydown.bs.dropdown.data-api', '[role="listbox"]', Dropdown.prototype.keydown)

})(jQuery);

/* ========================================================================
 * Bootstrap: alert.js v3.3.5
 * http://getbootstrap.com/javascript/#alerts
 * ========================================================================
 * Copyright 2011-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


(function ($) {
  'use strict';

  // ALERT CLASS DEFINITION
  // ======================

  var dismiss = '[data-dismiss="alert"]'
  var Alert   = function (el) {
    $(el).on('click', dismiss, this.close)
  }

  Alert.VERSION = '3.3.5'

  Alert.TRANSITION_DURATION = 150

  Alert.prototype.close = function (e) {
    var $this    = $(this)
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = $(selector)

    if (e) e.preventDefault()

    if (!$parent.length) {
      $parent = $this.closest('.alert')
    }

    $parent.trigger(e = $.Event('close.bs.alert'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      // detach from parent, fire event then clean up data
      $parent.detach().trigger('closed.bs.alert').remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent
        .one('bsTransitionEnd', removeElement)
        .emulateTransitionEnd(Alert.TRANSITION_DURATION) :
      removeElement()
  }


  // ALERT PLUGIN DEFINITION
  // =======================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.alert')

      if (!data) $this.data('bs.alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.alert

  $.fn.alert             = Plugin
  $.fn.alert.Constructor = Alert


  // ALERT NO CONFLICT
  // =================

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


  // ALERT DATA-API
  // ==============

  $(document).on('click.bs.alert.data-api', dismiss, Alert.prototype.close)

})(jQuery);

$(window).load(function() {
  // The slider being synced must be initialized first
  $('#single-carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    itemWidth: 138,
    itemMargin: 14,
    asNavFor: '#single-slider'
  });
 
  $('#single-slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: false,
    slideshow: false,
    sync: "#single-carousel"
  });
});

(function($){
    jQuery(window).load(function(){
        jQuery(".mCustomScrollbar-view-directory").mCustomScrollbar();
    });
})(jQuery);