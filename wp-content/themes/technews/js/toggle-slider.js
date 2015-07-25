/*JS for nav toggle and flexslider*/
		jQuery("#show-nav").click(function(){
			jQuery(".main-nav").toggle("slow");
			jQuery("#close-nav").show("slow");
			});
		jQuery("#close-nav").click(function(){
			jQuery(".main-nav").toggle("slow");
			jQuery("#close-nav").hide("slow");
			});
// Can also be used with $(document).ready()
		jQuery(window).load(function() {
			jQuery('.flexslider').flexslider({
			animation: "slide"
			});
		});
