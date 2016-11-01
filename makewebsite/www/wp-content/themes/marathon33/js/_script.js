'use strict';
var diffHeightColumns = $(".equal-height .col-2");
var difColumnsHeightCol5 = $(".equal-height .col-5");
var tabletWindowWidth = 768;
var mobileWindowWidth = 500;
var windowWidth = $(window).width();
var popupButton = $("#popup");
var popupContainer = $(".popup-window");
var closePopup = $('.popup-window .close');
var body = $("body");


// Set equal height for columns
var setEqualHeight = function (columns) {
    var tallestColumn = 0;
    //console.log(windowWidth);
    columns.each(function () {
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

$(window).load(function () {
    $('#countdown').countdown( {date: '12 august 2016 00:00:01'} );


    setEqualHeight(diffHeightColumns);
    if (windowWidth < tabletWindowWidth) {
        setEqualHeight(difColumnsHeightCol5);
    }

    popupButton.click(function (event) {
        event.preventDefault();
        popupContainer.addClass('is-visible');
        body.css("overflow", "hidden");
    });

    closePopup.click(function(event) {
        event.preventDefault();
        popupContainer.removeClass("is-visible");
        body.css("overflow", "visible");
    });

    $(document).keyup(function(event){
        if(event.which=='27'){
            popupContainer.removeClass('is-visible');
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

});

$(window).resize(function () {
    setEqualHeight(diffHeightColumns);
    if (windowWidth < tabletWindowWidth) {
        setEqualHeight(difColumnsHeightCol5);
    }


});