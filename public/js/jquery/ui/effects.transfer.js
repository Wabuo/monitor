(function(A){A.effects.transfer=function(B){return this.queue(function(){var E=A(this);var G=A.effects.setMode(E,B.options.mode||"effect");var F=A(B.options.to);var C=E.offset();var D=A('<div class="ui-effects-transfer"></div>').appendTo(document.body);if(B.options.className){D.addClass(B.options.className)}D.addClass(B.options.className);D.css({top:C.top,left:C.left,height:E.outerHeight()-parseInt(D.css("borderTopWidth"))-parseInt(D.css("borderBottomWidth")),width:E.outerWidth()-parseInt(D.css("borderLeftWidth"))-parseInt(D.css("borderRightWidth")),position:"absolute"});C=F.offset();animation={top:C.top,left:C.left,height:F.outerHeight()-parseInt(D.css("borderTopWidth"))-parseInt(D.css("borderBottomWidth")),width:F.outerWidth()-parseInt(D.css("borderLeftWidth"))-parseInt(D.css("borderRightWidth"))};D.animate(animation,B.duration,B.options.easing,function(){D.remove();if(B.callback){B.callback.apply(E[0],arguments)}E.dequeue()})})}})(jQuery)