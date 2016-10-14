'use strict';
var diffHeightColumns = $(".equal-height .col-2");
var difColumnsHeightCol5 = $(".equal-height .col-5");
var tabletWindowWidth = 768;
var mobileWindowWidth = 500;
var windowWidth = $(window).width();
var popup = $(".btn-popup");
// var registerPopup = $(".btn-register");
var popupContainer = $(".popup-window");
var closePopup = $('.popup-window .close');
var body = $("body");


// Set equal height for columns
var setEqualHeight = function(columns) {
    var tallestColumn = 0;
    //console.log(windowWidth);
    columns.each(function() {
        $(this).css('height', 'auto');

        var currentHeight = $(this).height();
        if (currentHeight > tallestColumn) {
            tallestColumn = currentHeight;
        }
    });
    columns.height(tallestColumn);
};

$(function() {
    $(".knob").knob({
        change: function(value) {
            //console.log("change : " + value);
        },
        release: function(value) {
            //console.log(this.$.attr('value'));
            console.log("release : " + value);
        },
        cancel: function() {
            console.log("cancel : ", this);
        },
        draw: function() {
            // "tron" case
            if (this.$.data('skin') == 'tron') {
                var a = this.angle(this.cv) // Angle
                    ,
                    sa = this.startAngle // Previous start angle
                    ,
                    sat = this.startAngle // Start angle
                    ,
                    ea // Previous end angle
                    , eat = sat + a // End angle
                    ,
                    r = 1;
                this.g.lineWidth = this.lineWidth;
                this.o.cursor && (sat = eat - 0.3) && (eat = eat + 0.3);
                if (this.o.displayPrevious) {
                    ea = this.startAngle + this.angle(this.v);
                    this.o.cursor && (sa = ea - 0.3) && (ea = ea + 0.3);
                    this.g.beginPath();
                    this.g.strokeStyle = this.pColor;
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false);
                    this.g.stroke();
                }
                this.g.beginPath();
                this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false);
                this.g.stroke();
                this.g.lineWidth = 2;
                this.g.beginPath();
                this.g.strokeStyle = this.o.fgColor;
                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
                this.g.stroke();
                return false;
            }
        }
    });
});

$(window).load(function() {
    var diffHeightColumns = $(".equal-height .col-2");
    var difColumnsHeightCol5 = $(".equal-height .col-5");
    var tabletWindowWidth = 768;
    var mobileWindowWidth = 500;
    var windowWidth = $(window).width();
    var popup = $(".btn-popup");
    // var registerPopup = $(".btn-register");
    var popupContainer = $(".popup-window");
    var closePopup = $('.popup-window .close');
    var body = $("body");


    // Set equal height for columns
    var setEqualHeight = function(columns) {
        var tallestColumn = 0;
        //console.log(windowWidth);
        columns.each(function() {
            $(this).css('height', 'auto');

            var currentHeight = $(this).height();
            if (currentHeight > tallestColumn) {
                tallestColumn = currentHeight;
            }
        });
        columns.height(tallestColumn);
    };

    
    $('#countdown').countdown({
        date: '12 september 2016 11:59:59'
    });

    var urlAjax = "http://" + window.location.host + window.location.pathname + "ajax.php";

    setEqualHeight(diffHeightColumns);
    if (windowWidth < tabletWindowWidth) {
        setEqualHeight(difColumnsHeightCol5);
    }

    $(".btn-popup").click(function(event) {
        var dataPopup = $(this).data('popup');
        event.preventDefault();
        $(".popup-window#" + dataPopup + "-popup").addClass('is-visible');
        $("body").css("overflow", "hidden");
    });

    $(".popup-window .close").click(function(event) {
        event.preventDefault();
        $(".popup-window").removeClass('is-visible');
        $(".popup-window .message").css("display", "none");
        setTimeout(function(){
            $("#subscribe-form").css("display", "block");
        }, 2000);
        $("body").css("overflow", "visible");
    });

    $(document).keyup(function(event) {
        if (event.which == '27') {
            $(".popup-window").removeClass('is-visible');
            $(".popup-window .message").css("display", "none");
            setTimeout(function(){
              $("#subscribe-form").css("display", "block");
            }, 2000);
        }
    });


    // Mobile menu
    // -----------------------
    if (!$('.mobile-menu').children('ul').length) {
        $('.mobile-menu').append($('.nav').html());
    }

    $('.responsive-nav-button').click(function() {
        $(this).toggleClass('active').siblings().slideToggle();
    });

    var $obj_data = {};
    var $form1 = $('#question-form');
    $('.question-small').click(function() {
        $form1.validate({
            onkeyup: false,
            submitHandler: function() {
                var name = $('.question-name').val(),
                    email = $('.question-email').val(),
                    question = $('.question-textarea').val();

                var data = {
                    'app': 'site33days',
                    'name': name,
                    'question': question,
                    'email': email
                }
                $.ajax({
                    type: "POST",
                    url: urlAjax,
                    data: data,
                    success: function(data) {
                        $obj_data = $.parseJSON(data);
                        $("#question-form").css("display", "none");
                        $("#success-answer").css("display", "block");
                    },
                    error: function() {
                        $("#question-form").css("display", "none");
                        $("#error-answer").css("display", "block");
                    }
                });
                return false;
            },
            errorClass: "notvalid",
            messages: {
                name: {
                    required: "Укажите свое Имя.",
                    minlength: "Имя должно быть не менее 2-х символов"
                },
                email: {
                    required: "Укажите свой Email.",
                    email: "Пожалуйста введите Email в правильной форме"
                },
                question: {
                    required: "Укажите свой Вопрос.",
                    minlength: "Вопрос должно быть не менее 5-и символов"
                }
            },
            debug: true,
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                email: {
                    required: true,
                    email: true
                },
                question: {
                    required: true,
                    minlength: 5
                }
            }
        });
    });

    $('.send-request').on('click', function() {
        var $form = $(this).closest('form');
        $form.validate({
            onkeyup: false,
            submitHandler: function() {
                $.ajax({
                    type: "POST",
                    url: urlAjax,
                    data: {
                        'app': 'site33days',
                        'email_subs': $form.find('.email-input').val()
                    },
                    success: function(data) {
                        var $obj_data = $.parseJSON(data);
                        if ($obj_data.data.exist == true) {
                            $("#subscribe-form").css("display", "none");
                            $("#email-exist").css("display", "block");
                        } else {
                            $("#subscribe-form").css("display", "none");
                            $("#response").css("display", "block");
                        }
                    },
                    error: function() {
                        $("#subscribe-form").css("display", "none");
                        $("#email-error").css("display", "block");
                    }
                });
                return false;
            },
            errorClass: "notvalid",
            messages: {
                email_subs: {
                    required: "Укажите свой Email.",
                    email: "Пожалуйста введите Email в правильной форме"
                }
            },
            debug: true,
            rules: {
                email_subs: {
                    required: true,
                    email: true
                }
            }
        });
    });
    $.urlParam = function(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        } else {
            return results[1] || 0;
        }
    }
    $(".id").val($.urlParam('id'));
    $(".code").val($.urlParam('code'));

    $(function() {
        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top - 70
                    }, 1000);
                    return false;
                }
            }
        });
    });

});

$(window).resize(function() {
    setEqualHeight(diffHeightColumns);
    if (windowWidth < tabletWindowWidth) {
        setEqualHeight(difColumnsHeightCol5);
    }
});