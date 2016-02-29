$(document).ready( function() {
  var pagesMax_m = 60;
  var pagesMin_m = 1;
  var startPage_m =30;
  var url = "六合资本";
  
  $('.slideTwo .pageSlider').slider({

    value: startPage_m, max: pagesMax_m, min: pagesMin_m,
    animate: true,
    
    create: function( event, ui ) {
      
      $('.slideTwo .pageSlider .ui-slider-handle').attr({
        "aria-valuenow": startPage_m,
        "aria-valuetext": "Page " + startPage_m,
        "role": "slider",
        "aria-valuemin": pagesMin_m,
        "aria-valuemax": pagesMax_m,
        "aria-describedby": "pageSliderDescription" 
      });
	  var pageVal_m=Math.ceil(startPage_m/5);
	  if(pageVal_m<10){
		 $('.slideTwo .pageInput').text('0'+pageVal_m);   
	  }
	  else{
		$('.slideTwo .pageInput').text(pageVal_m);  
	  }
        $('.slideTwo .inputval').val(pageVal_m);
        $(".slideTwo .pageLong").css("width",(startPage_m/12)*100);
    }
  
  }).on( 'slide', function(event,ui) {
      
      // let user skip 10 pages with keyboard ;)
      if( event.metaKey || event.ctrlKey ) {
        
        if( ui.value > $(this).slider('value') ) {
          
          if( ui.value+9 < pagesMax_m ) { ui.value+=9; } 
          else { ui.value=pagesMax_m }
          $(this).slider('value',ui.value);
        
        } else {
          
          if( ui.value-9 > pagesMin_m ) { ui.value-=9; } 
          else { ui.value=pagesMin_m }
          $(this).slider('value',ui.value);
          
        }
        
        event.preventDefault();
        
      }
      var pageOne_m=Math.ceil(ui.value/5);
	  $(".slideTwo .pageLong").css("width",(ui.value/12)*100);
      $('.slideTwo .pageNumber span').text( ui.value );
	  if(pageOne_m<10){
      	$('.slideTwo .pageInput').text('0'+pageOne_m);
	  }
	  else{
	    $('.slideTwo .pageInput').text(pageOne_m);   
	  }
      $('.slideTwo .inputval').val(pageOne_m);
      
  }).on('slidechange', function(event, ui) {
	  var pageTwo_m=Math.ceil(ui.value/5);
      $(".slideTwo .pageLong").css("width",(ui.value/12)*100);
      $('.slideTwo .pageNumber')
        .attr('role','alert')
        .find('span')
        .text( ui.value );
      
      if(pageTwo_m<10){
      	$('.slideTwo .pageInput').text('0'+pageTwo_m);
	  }
	  else{
	    $('.slideTwo .pageInput').text(pageTwo_m);   
	  }
      $('.slideTwo .inputval').val(pageTwo_m);
      $('.slideTwo .pageSlider .ui-slider-handle').attr({
        "aria-valuenow": ui.value,
        "aria-valuetext": "Page " + ui.value 
      });
    
  });

$('.slideTwo .pageSlider .ui-slider-handle').on( 'keyup' , function(e) {
  
  if( e.which == 13 ) {
    var curPage = $('.slideTwo .pageSlider').slider('value');
    alert( 'we would now send you to: ' + url.replace( /{{.}}/ , curPage ));
  }
  
});


$('.slideTwo .pageInput').on( 'change' , function(e) {
  $('.slideTwo .pageSlider').slider( 'value', $(this).val() );
});


  
  
  
  var tmr;
  $('.pageSkip').on('click', function(e) {
    
    e.preventDefault();
    
    var $this = $(this);
    
    if( $this.hasClass('pageNext') ) {
      var curPage = $('.slideTwo .pageSlider').slider('value')+1;
    } else if( $this.hasClass('pagePrev') ) {
      var curPage = $('.slideTwo .pageSlider').slider('value')-1;
    }
    
    $('.slideTwo .pageSlider').slider('value',curPage);
    
    clearTimeout(tmr);
    tmr = setTimeout( function() {
      alert( 'we would now send you to: ' + url.replace( /{{.}}/ , curPage ));
    },1000);
    
  });
  
  
  
  

function sliderPips_m( min, max ) {
  
  var pips_m = max-min;
  var $pagination_m = $('.slideTwo .pageSlider');
  var num_m=["01","02","03","04","05","06","07","08","09","10","11","12"];
  for( var m=0; m<num_m.length; m++ ) {

    var r = $('<span class="pagePip">'+num_m[m]+'</span>').css({ 
      left: '' + (100/num_m.length)*m + '%' 
    });
    
    $pagination_m.append( r );

  }
  
  var minPip_m = $('<span class="pageMinPip">'+min+'</span>');
  var maxPip_m = $('<span class="pageMaxPip">'+max+'</span>');
  $pagination_m.prepend( minPip_m, maxPip_m );
  
};sliderPips_m( pagesMin_m, pagesMax_m );
 

/*function sliderLabel() {
  $('.slideTwo .ui-slider-handle').append(
    '<span class="pageNumber">Page <span>' + 
    $('.slideTwo .pageSlider').slider('value') + 
    '</span></span>');
};sliderLabel();*/

  $('.slideTwo .pageButton').on('click', function(e) {

    e.preventDefault();
    var curPage = $('.slideTwo .pageSlider').slider('value');
    alert( 'we would now send you to: ' + 
          url.replace( /{{.}}/ , curPage ));
  
  });
  
});



  