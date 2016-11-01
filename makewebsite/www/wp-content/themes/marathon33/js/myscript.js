/* knob */
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
    // Example of infinite knob, iPod click wheel
    var v, up = 0,
        down = 0,
        i = 0,
        $idir = $("div.idir"),
        $ival = $("div.ival"),
        incr = function() {
            i++;
            $idir.show().html("+").fadeOut();
            $ival.html(i);
        },
        decr = function() {
            i--;
            $idir.show().html("-").fadeOut();
            $ival.html(i);
        };
    $("input.infinite").knob({
        min: 0,
        max: 20,
        stopper: false,
        change: function() {
            if (v > this.cv) {
                if (up) {
                    decr();
                    up = 0;
                } else {
                    up = 1;
                    down = 0;
                }
            } else {
                if (v < this.cv) {
                    if (down) {
                        incr();
                        down = 0;
                    } else {
                        down = 1;
                        up = 0;
                    }
                }
            }
            v = this.cv;
        }
    });
});

var timer = function (){
    var  list = $(".status.timer");
    $.each($(list).find("span"), function (index, value) {
        var minut = parseInt($(value).data("minut"));
        var hour = parseInt($(value).data("hour"));
        var diffMinut =  minut - 1;
        if(diffMinut < 0){
            minut = 59;
            var diffHour = hour - 1;
            if(diffHour < 1){
                hour = 0;
            }else{
                hour--;
            }
        }else{
            minut--;
        }          
       
        $(value).data("hour", hour);    
        $(value).data("minut", minut);
        $(value).find("i").text(hour);
        $(value).find("b").text(minut);
    });
    
    setTimeout(function(){
        timer();
    }, 60000); 
};

$(window).load(function() {
    // $('#countdown').countdown( {date: '10 june 2016 23:59:59'} );
    timer();     

     // $('#countdown').countdown( {date: '10 june 2016 23:59:59'} );

    $('input[type="text"],textarea').focus(function(){
        $(this).data('placeholder',$(this).attr('placeholder'))
        $(this).attr('placeholder','');
    });
    $('input[type="text"],textarea').blur(function(){
       $(this).attr('placeholder',$(this).data('placeholder'));
    });

    
});