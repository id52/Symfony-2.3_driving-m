jQuery(document).ready(function() {
	$(function() {
      if (window.PIE) {
          $('.ie_css3').each(function() {
              PIE.attach(this);
          });
      }
    });
    /**Поддержка placeholder**/
    jQuery('input[placeholder], textarea[placeholder]').placeholder();
    /**link false**/
    $('a[href$="#"]').on('click', function(event) {
		event.preventDefault();
	});
    /**Для выпадения всех блоков с класом .drop-down**/
    var parentDrop = $('.js-parrent-drop');
    var childLink = $('.js-link-drop');
    $(childLink).click(function() {
        var that = $(this).parent();
        if($(that).hasClass('active')) {
          $(that).removeClass('active');
          $(that).find('.js-drop-down').hide();
        }
        else {
          $(parentDrop).removeClass('active');
          $('.js-drop-down').hide();
          $(that).addClass('active');
          $(that).find('.js-drop-down').stop().fadeIn(200);
        }
        return false;
    });
    $('.js-drop-down .close').click(function() {
        $(parentDrop).removeClass('active');
        $('.js-drop-down').hide();
        return false;
    });
    var parentSlide = $('.js-parent-slide');
    var childSlide = $('.js-child-slide');
    $(childSlide).click(function() {
        var that = $(this).closest('.js-parent-slide');
        if($(this).hasClass('active')) {
            $(that).removeClass('active');
            $(this).removeClass('active');
            $(that).find('.js-body-slide:first').slideUp(300);
        }
        else {
            $(that).addClass('active');
            $(this).addClass('active');
            $(that).find('.js-body-slide:first').stop().slideDown(300);
        }
        return false;
    });
    /**Закрывает все открытые .drop-down при клике на пустую область**/
    $("html").click(function(e) {
        if($(e.target).closest(parentDrop).length==0) {
            $(".js-drop-down").hide();
            $(this).find(parentDrop).removeClass('active');
        }
    });
    /**Чекбоксы**/
    $('.checkb').prettyCheckboxes({
        checkboxWidth: 14,
        checkboxHeight: 14,
        className: 'prettyCheckbox'
    });
    /****/
    $('#metromap area').click(function() {
        var that = $(this);
        if($(that).hasClass('active')) {
            $(that).removeClass('active');
            $('.popup-maps').hide();
        }
        else {
            $('#metromap area').removeClass('active');
            $(that).addClass('active');
            $('.popup-maps').fadeIn(200);
        }
        return false;
    });
    $('.close-popup').click(function() {
        $('#metromap area').removeClass('active');
        $('.popup-maps').hide();
        return false;
    });

    $('.forgot-pass-link').click(function(){
        var block = $('.remind-password-block');
        if (block.is(':visible')) {
            block.slideUp(200);
            $(this).css('textDecoration', 'underline');
        } else {
            block.slideDown(200);
            $(this).css('textDecoration', 'none');
        }
        return false;
    });

    function initRadioLabels()
    {
        var labels = $('.radio-field');

        function updateRadioLabels(input)
        {
            labels.each(function() {
                var label = $(this);
                var input = label.find('input');
                if (input.is(':checked')) {
                    label.addClass('-checked');
                } else {
                    label.removeClass('-checked');
                }
            });
        }
        $('.radio-field').find('input').change(function(){
            updateRadioLabels($(this));
        });
        updateRadioLabels();
    }

    initRadioLabels();

    function showSmsConfirmForm()
    {
        var form = $('.register-form').find('.sms-confirm-form');
        form.slideDown();
        form.click(function(e){
            e.stopPropagation();
        });
        $(document).bind('click.sms-confirm', function() {
            form.slideUp();
            $(document).unbind('click.sms-confirm');
        });
        return false;
    }

    $('.register-form').find('.confirm-phone-link').click(showSmsConfirmForm);

    function smsConfirmed()
    {
        var form = $('.register-form');
        form.find('.sms-confirm-form').slideUp();
        form.find('.confirm-phone-link').hide();
        form.find('.sms-confirmed-message').show();
    }
    $('.sms-confirm-wrapper').find('button').click(function() {
        smsConfirmed();
        return false;
    });

    var initAboutClip = function() {
        var clip = $('video', '.as-clip');
        var play = $('.as-play');

        play.click(function() {
            clip[0].play();
            clip.attr('controls', true);
            play.fadeOut();
            return false;
        });
    };
    initAboutClip();
});
