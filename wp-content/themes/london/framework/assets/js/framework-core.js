/*
*
*	Admin $ Functions
*	------------------------------------------------
*
*/



(function($){
    $('document').ready(function() {
        
        
        /* SIDEBAR =====================================================*/
        var $sidebar_config = $('#kt_sidebar'),
            $sidebar_left = $('#kt_left_sidebar').closest('.rwmb-field'),
            $sidebar_right = $('#kt_right_sidebar').closest('.rwmb-field');
            
            
        function kt_sidebar(){
            $sidebar_value = $sidebar_config.val();
            if ($sidebar_value == "left") {
                $sidebar_left.show();
                $sidebar_right.hide();
            }else if($sidebar_value == "right"){
                $sidebar_left.hide();
                $sidebar_right.show();
            }else{
                $sidebar_left.hide();
                $sidebar_right.hide();
            }
        }
        kt_sidebar();
    	$sidebar_config.change(function() {
            kt_sidebar();
    	});
        
        /* Slideshow source =====================================================*/
        var $slideshow_config = $('#kt_slideshow_source'),
            $rev_slider = $('#kt_rev_slider').closest('.rwmb-field'),
            $layerslider = $('#kt_layerslider').closest('.rwmb-field');
            
        function kt_slideshow(){
            $slideshow_value = $slideshow_config.val();
            if ($slideshow_value == "revslider") {
                $rev_slider.show();
                $layerslider.hide();
            }else if($slideshow_value == "layerslider"){
                $rev_slider.hide();
                $layerslider.show();
            }else{
                $rev_slider.hide();
                $layerslider.hide();
            }
        }
        kt_slideshow();
    	$slideshow_config.change(function() {
            kt_slideshow();
    	});
        
        
        
    });
})(jQuery);