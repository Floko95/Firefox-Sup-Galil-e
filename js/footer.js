$(window).bind("load", function() { 
       
    var footerHeight = 0,
        bodyHeight = 0,
        footerTop = 0,
        $footer = $("#page-footer");
        $body = $("body");
        
    positionFooter();
    
    function positionFooter() {
    
             footerHeight = $footer.height();
             bodyHeight = $body.height();
             footerTop = ($(window).height()-footerHeight-100);
    
            if ( ($(document.body).height()+footerHeight) < $(window).height()) {
                $footer.css({
                     position: "absolute",
                     top: footerTop
                })
            } else {
                $footer.css({
                     position: "static"
                })
            }
            
    }

    $(window)
            .scroll(positionFooter)
            .resize(positionFooter)
            
});