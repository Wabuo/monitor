(function(A){A.effects.drop=function(B){return this.queue(function(){var E=A(this),D=["position","top","left","opacity"];var I=A.effects.setMode(E,B.options.mode||"hide");var H=B.options.direction||"left";A.effects.save(E,D);E.show();A.effects.createWrapper(E);var F=(H=="up"||H=="down")?"top":"left";var C=(H=="up"||H=="left")?"pos":"neg";var J=B.options.distance||(F=="top"?E.outerHeight({margin:true})/2:E.outerWidth({margin:true})/2);if(I=="show"){E.css("opacity",0).css(F,C=="pos"?-J:J)}var G={opacity:I=="show"?1:0};G[F]=(I=="show"?(C=="pos"?"+=":"-="):(C=="pos"?"-=":"+="))+J;E.animate(G,{queue:false,duration:B.duration,easing:B.options.easing,complete:function(){if(I=="hide"){E.hide()}A.effects.restore(E,D);A.effects.removeWrapper(E);if(B.callback){B.callback.apply(this,arguments)}E.dequeue()}})})}})(jQuery)