(function($) {    
  // 插件的定义    
  $.fn.hilight = function(textValue) { 
  //alert(text);
  var pagesMax = 30;
  var pagesMin = 1;
  var startPage = 6;
  var url = "六合资本";
 
  $('.slideOne .pageSlider').slider({

    value: startPage, max: pagesMax, min: pagesMin,
    animate: true,
    create: function( event, ui) {
      
      $('.slideOne .pageSlider .ui-slider-handle').attr({
        "aria-valuenow": startPage,
        "aria-valuetext": "Page " + startPage,
        "role": "slider",
        "aria-valuemin": pagesMin,
        "aria-valuemax": pagesMax,
        "aria-describedby": "pageSliderDescription" 
      }
	  );
	 
	  var pageVal=Math.ceil(startPage/6);
      $('.slideOne .pageInput').text(pageVal*textValue);
        $('.slideOne .inputval').val(pageVal);
	  $(".slideOne .pageLong").css("width",(startPage/6)*100);
	  /*for(var n=0; n<(pageVal/text);n++){
		$(".slideOne .pagePip").eq(n).addClass("hover");	  
	  }*/
	  
    }
  
  }).on( 'slide', function(event,ui) {
      
      // let user skip 10 pages with keyboard ;)
      if( event.metaKey || event.ctrlKey ) {
        
        if( ui.value > $(this).slider('value') ) {
          
          if( ui.value+9 < pagesMax ) { ui.value+=9; } 
          else { ui.value=pagesMax }
          $(this).slider('value',ui.value);
        
        } else {
          
          if( ui.value-9 > pagesMin ) { ui.value-=9; } 
          else { ui.value=pagesMin }
          $(this).slider('value',ui.value);          
        }
        
        event.preventDefault();
        
      }
      var pageOne=Math.ceil(ui.value/6);
	  /*for(var n=0; n< (pageOne/text);n++){
		$(".slideOne .pagePip").eq(n).addClass("hover");	  
	  }*/
	  
	  $(".slideOne .pageLong").css("width",(ui.value/6)*100);
      //$('.slideOne .pageNumber span').text( ui.value );
      $('.slideOne .pageInput').text(pageOne*textValue);
      $('.slideOne .inputval').val(pageOne);
      
  }).on('slidechange', function(event, ui) {
	 // var test=[];
	 var textValue=$(".textw").val(); 
	 var pageTwo=Math.ceil(ui.value/6);
	  /*for(var n=0; n< (pageTwo/text);n++){
		$(".slideOne .pagePip").eq(n).addClass("hover");	  
	  }*/
	  //alert(pageTwo);
	  //alert(startPage);
	  //test.push(textValue);
	  //var textV=test[test.length-1];
	  /*for(var g=0;g<textValue.length;g++){
		test.push(textValue[g]);	  
	  }
	  return test;*/
	 // var textV=test[test.length-1];
	  //alert(textValue);
      $(".slideOne .pageLong").css("width",(ui.value/6)*100);
	  
      $('.slideOne .pageInput').text(pageTwo*textValue);
      $('.slideOne .inputval').val(pageTwo);
      $('.slideOne .pageSlider .ui-slider-handle').attr({
        "aria-valuenow": ui.value,
        "aria-valuetext": "Page " + ui.value 
      });
     
  });

$('.slideOne .pageSlider .ui-slider-handle').on( 'keyup' , function(e) {
  
  if( e.which == 13 ) {
    var curPage = $('.slideOne .pageSlider').slider('value');
    alert( 'we would now send you to: ' + url.replace( /{{.}}/ , curPage ));
  }
  
});


/*$('.slideOne .pageInput').on('change' , function(e) {
  var textValue=$(this).val();
  $('.slideOne .pageSlider').slider( 'value',textValue*startPage  );
});
*/

  
  
  
 /* var tmr;
  $('.pageSkip').on('click', function(e) {
    
    e.preventDefault();
    
    var $this = $(this);
    
    if( $this.hasClass('pageNext') ) {
      var curPage = $('.slideOne .pageSlider').slider('value')+1;
    } else if( $this.hasClass('pagePrev') ) {
      var curPage = $('.slideOne .pageSlider').slider('value')-1;
    }
    
    $('.slideOne .pageSlider').slider('value',curPage);
    
    clearTimeout(tmr);
    tmr = setTimeout( function() {
      alert( 'we would now send you to: ' + url.replace( /{{.}}/ , curPage ));
    },1000);
    
  });*/
  
function sliderPips(min, max) {
  
  var pips = max-min;
  var $pagination = $('.slideOne .pageSlider');
  var num=["1倍","2倍","3倍","4倍","5倍"];
  
   for( i=0; i<num.length; i++ ) {

    var s = $('<span class="pagePip">'+num[i]+'</span>').css({ 
      left: '' + (100/num.length)*i + '%' 
    });
	
    $pagination.append(s);
	
  }
  
  var minPip = $('<span class="pageMinPip">'+min+'</span>');
  var maxPip = $('<span class="pageMaxPip">'+max+'</span>');
  $pagination.prepend( minPip, maxPip );
  
};sliderPips(pagesMin, pagesMax);

/*function sliderLabel() {
  $('.pagination .ui-slider-handle').append(
    '<span class="pageNumber">Page <span>' + 
    $('.pagination .pageSlider').slider('value') + 
    '</span></span>');
};sliderLabel();*/

  /*$('.slideOne .pageButton').on('click', function(e) {

    e.preventDefault();
    var curPage = $('.slideOne .pageSlider').slider('value');
    alert( 'we would now send you to: ' + 
          url.replace( /{{.}}/ , curPage ));
  
  });*/
 }
})(jQuery); 


  