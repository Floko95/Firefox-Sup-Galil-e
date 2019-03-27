$(window).bind("load", function() { 
       
    var footerHeight = 0,
        footerTop = 0,
        $footer = $("#page-footer");
        
    positionFooter();
    
    function positionFooter() { 
            console.log("repositionnement du footer");
             footerHeight = $footer.height();
             footerTop = ($(window).height()-footerHeight-100);
    
            if ( ($(document.body).height()+footerHeight) < $(window).height()) {
                console.log("absolue : "+footerTop);
                $footer.css({
                    
                     position: "absolute",
                     top: footerTop
                })
            } else {
                console.log("static : "+footerTop);
                $footer.css({
                    
                     position: "static"
                })
            }
            
    }

    $(window)
            .scroll(positionFooter)
            .resize(positionFooter)
            
});