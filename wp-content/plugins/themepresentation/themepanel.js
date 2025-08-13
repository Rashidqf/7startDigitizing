jQuery(document).ready(function($) {
	
//var currentDomain = 'finestwp.com/biztech' ;	
var currentDomain = siteData.siteDirectory;	
	
// Function to apply a specific color
function applyColor(mainColor) {
    // Construct the complete URL including the path and query parameters
    // 
	 var cssUrl = currentDomain + '/assets/css/color.php?main_color=' + mainColor;
	
    // Remove existing jQuery stylesheets
    $('link[rel*="jquery"]').remove();

    // Append the new stylesheet link to the head
    $('head').append('<link rel="stylesheet jquery" href="' + cssUrl + '" type="text/css" />');

    return false;
}

// Apply different colors when a color option is clicked

$('#default').click(function(){
    applyColor('DE2C2C');
});

$('#color2').click(function(){
    applyColor('ff3269');
});

$('#color3').click(function(){
    applyColor('04ea58');
});

$('#color4').click(function(){
    applyColor('610287');
});

$('#color5').click(function(){
    applyColor('3064ee');
});

$('#color6').click(function(){
    applyColor('7ac7c4');
});

$('#color7').click(function(){
    applyColor('fa2a2a');
});

$('#color8').click(function(){
    applyColor('47fff6');
});

$('#color9').click(function(){
    applyColor('b8276d');
});

$('#color10').click(function(){
    applyColor('FF69B1');
});

$('#color11').click(function(){
    applyColor('73B819');
});

$('#color12').click(function(){
    applyColor('19B8AF');
});

		
	
  if ($.cookie('boxed') == "yes") {

            $("main").addClass("boxed");

        }



  if ($.cookie('boxed') == "no") {

    $("main").removeClass("boxed");

  }

});

jQuery(document).ready(function($) {

	$("#boxed").click(function(e) { 

	e.preventDefault();

	$('main').addClass("boxed");

    $.cookie('boxed','yes', {expires: 7, path: '/'});

	return false;

	});

	$("#full").click(function(e) { 

	e.preventDefault();

	$('main').removeClass("boxed");

    $.cookie('boxed','no', {expires: 7, path: '/'});

	return false;

	});

});

//RTL CLASS 

jQuery(document).ready(function($) {

	$("#rtl").click(function(e) { 

	e.preventDefault();

	$('main').addClass("rtl");

    $.cookie('rtl','yes', {expires: 7, path: '/'});

	return false;

	});

	$("#normal").click(function(e) { 

	e.preventDefault();

	$('main').removeClass("rtl");

    $.cookie('rtl','no', {expires: 7, path: '/'});

	return false;

	});

});


jQuery(document).ready(function($) {

	$(".switcher .fa-cog").click(function(e) { 

	e.preventDefault();

	$(".switcher").toggleClass("active");

	});

});



/*	
	
// Function to get the active WordPress theme name from stylesheet links
function getActiveThemeName() {
    // Get all stylesheet links on the page
    var stylesheetLinks = document.querySelectorAll('link[rel="stylesheet"]');
    
    for (var i = 0; i < stylesheetLinks.length; i++) {
        var href = stylesheetLinks[i].getAttribute('href');
        if (href && href.indexOf('/wp-content/themes/') !== -1) {
            // Extract the theme name from the href
            var parts = href.split('/wp-content/themes/');
            if (parts.length === 2) {
                var themeName = parts[1].split('/')[0];
                return themeName;
            }
        }
    }
    
    // Return null if no theme name was found
    return null;
}
	
// Get the active WordPress theme name
var activeThemeName = getActiveThemeName();

var baseUrl =  currentDomain + '/wp-content/themes/' + activeThemeName + '/assets/'  ;

*/	
	
//var currentDomain = 'finestwp.com/biztech' ;
//
	
	
//var baseUrl =  currentDomain + '/assets/css/'  ;

	
/*	
// Check if the current page is using HTTPS or HTTP
var protocol = window.location.protocol;


// Set the linkcss variable based on the protocol
if (protocol === 'https:') {
    var linkcss = 'https:' + baseUrl;
} else {
    var linkcss = 'http:' + baseUrl;
}
*/
