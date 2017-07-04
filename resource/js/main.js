var $index = $('#text_cent');
var $rule = $('#text_cent2');
var pageHeight = $index.height();
$rule.css('-webkit-transform', 'translate3d(0,' + pageHeight + 'px,0)');

var sliderOpts = {
    curPage: 1,
    indexPos: 0,
    rulePos: 0,
    timer: null,
    duration: 300,
    range: 70,
    startY: 0,
    distance: 0,
    noSlider: false
};

var handleStart = function (event) {
    sliderOpts.distance = 0;
    var touch = event.touches[0];
    if (($(window).height() - touch.clientY < 95) || !rule || sliderOpts.curPage == 2) {
        sliderOpts.noSlider = true;
        return;
    }
    event.preventDefault();
    sliderOpts.noSlider = false;
    if (sliderOpts.timer) return;
    sliderOpts.startY = touch.clientY;
    sliderOpts.indexPos = $("#text_cent").offset().top;
    sliderOpts.rulePos = $("#text_cent2").offset().top;
};

var handleMove = function (event) {
    if (sliderOpts.noSlider) return;
    event.preventDefault();
    if (sliderOpts.timer) return;
    var touch = event.touches[0];
    sliderOpts.distance = touch.clientY - sliderOpts.startY;
    if (Math.abs(sliderOpts.distance) < 5) return;
    $("#text_cent").css('-webkit-transform', 'translate3d(0,' + (sliderOpts.indexPos + sliderOpts.distance) + 'px,0)');
    $("#text_cent2").css('-webkit-transform', 'translate3d(0,' + (sliderOpts.rulePos + sliderOpts.distance) + 'px,0)');
};

var handleEnd = function (event) {
    if (sliderOpts.noSlider) return;
    event.preventDefault();
    if (sliderOpts.timer) return;
    $('#text_cent,#text_cent2').css('-webkit-transition', '-webkit-transform ' + sliderOpts.duration + 'ms linear');
    var $index = $('#text_cent');
    var $rule = $('text_cent2');
    var pageHeight = $index.height();
    if ((sliderOpts.distance < -sliderOpts.range && sliderOpts.curPage == 1) || (sliderOpts.distance > sliderOpts.range && sliderOpts.curPage == 2)) {
        var next = sliderOpts.distance < -sliderOpts.range;
        $(next ? '#text_cent' : '#text_cent2').css('-webkit-transform', 'translate3d(0,' + (next ? (-pageHeight) : pageHeight) + 'px,0)');
        $(next ? '#text_cent2' : '#text_cent').css('-webkit-transform', 'translate3d(0,0px,0)');

        sliderOpts.timer = setTimeout(function() {
            $('#text_cent,#text_cent2').css({
                '-webkit-transition': null
            });
            clearTimeout(sliderOpts.timer);
            sliderOpts.timer = null;
        }, sliderOpts.duration + 5);
    } else {
        if (sliderOpts.curPage == 1) {
            $index.css('-webkit-transform', 'translate3d(0,0px,0)');
            $rule.css('-webkit-transform', 'translate3d(0,' + pageHeight + 'px,0)');
        } else {
            $rule.css('-webkit-transform', 'translate3d(0,0px,0)');
            $index.css('-webkit-transform', 'translate3d(0,' + (-pageHeight) + 'px,0)');
        }
        sliderOpts.timer = setTimeout(function() {
            $('#text_cent,#text_cent2').css({
                '-webkit-transition': null
            });
            clearTimeout(sliderOpts.timer);
            sliderOpts.timer = null;
        }, sliderOpts.duration + 5);
    }
};

var handleCancel = function (event) {
    if (sliderOpts.noSlider) return;
    event.preventDefault();
    if (sliderOpts.timer) return;
    $('#text_cent,#text_cent2').css('-webkit-transition', '-webkit-transform ' + sliderOpts.duration + 'ms linear');
    var $index = $('#text_cent');
    var $rule = $('text_cent2');
    var pageHeight = $index.height();
    if ((sliderOpts.distance < -sliderOpts.range && sliderOpts.curPage == 1) || (sliderOpts.distance > sliderOpts.range && sliderOpts.curPage == 2)) {
        var next = sliderOpts.distance < -sliderOpts.range;
        $(next ? '#text_cent' : '#text_cent2').css('-webkit-transform', 'translate3d(0,' + (next ? (-pageHeight) : pageHeight) + 'px,0)');
        $(next ? '#text_cent2' : '#text_cent').css('-webkit-transform', 'translate3d(0,0px,0)');

        sliderOpts.timer = setTimeout(function() {
            $('#text_cent,#text_cent2').css({
                '-webkit-transition': null
            });
            clearTimeout(sliderOpts.timer);
            sliderOpts.timer = null;
        }, sliderOpts.duration + 5);
    } else {
        if (sliderOpts.curPage == 1) {
            $index.css('-webkit-transform', 'translate3d(0,0px,0)');
            $rule.css('-webkit-transform', 'translate3d(0,' + pageHeight + 'px,0)');
        } else {
            $rule.css('-webkit-transform', 'translate3d(0,0px,0)');
            $index.css('-webkit-transform', 'translate3d(0,' + (-pageHeight) + 'px,0)');
        }
        sliderOpts.timer = setTimeout(function() {
            $('#text_cent,#text_cent2').css({
                '-webkit-transition': null
            });
            clearTimeout(sliderOpts.timer);
            sliderOpts.timer = null;
        }, sliderOpts.duration + 5);
    }
};

document.addEventListener("touchstart", handleStart, false);
document.addEventListener("touchend", handleEnd, false);
document.addEventListener("touchcancel", handleCancel, false);
document.addEventListener("touchmove", handleMove, false);