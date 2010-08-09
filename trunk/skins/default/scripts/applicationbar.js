var HOVER_SCALE = 1.260;
var PROXIMITY = 120.0;
$(function()
{
    if (jQuery.browser.msie) {
        return; // application bar animation works in IE, but doesn't animate smoothly
    }
    /**
    * Calculate the height of an icon based upon its position from the mouse
    * @param icon  object
    * @param mousePosX hor. mouse position relative to left window border
    * @return new width/height
    */
    function calculateSize(icon, mousePosX)
    {
        //get the distance in x from the mouse to the icon
        var initWidth = parseInt($(icon).data("initWidth"));
        var initHeight = parseInt($(icon).data("initHeight"));
        var newWidth = parseInt($(icon).data("newWidth"));
        var newHeight = parseInt($(icon).data("newHeight"));
        var xProximity = Math.abs(mousePosX - $(icon).offset().left - (newWidth / 2.0));
        //if we need to vary the height because the mouse is within range
        if (xProximity < 16) {
            return [Math.round(HOVER_SCALE * initWidth), Math.round(HOVER_SCALE * initHeight)];
        } else if (xProximity < PROXIMITY) {
            //get the percentage height the icon needs to be
            var newRatio = ((PROXIMITY-xProximity)/PROXIMITY);
            var additionalWidth = newRatio * (newWidth-initWidth);
            var additionalHeight = newRatio * (newHeight-initHeight);
            //add on the additional percentage to the icon
            return [(initWidth + additionalWidth), (initHeight + additionalHeight)];
        } else {
            //otherwise, return the original icon size
            return [initWidth, initHeight];
        }
    }
    $(".header").each(function(i)
    {
        //private variables
        var appBar = $(this);
        var icons = appBar.find(".applicationBar img");
        //store the to and from sizes
        $.each(icons, function()
        {
            var initHeight = parseInt($(this).height());
            var initWidth = parseInt($(this).width());
            $(this).data("initWidth", initWidth);
            $(this).data("initHeight", initHeight);
            $(this).data("newWidth", initWidth * HOVER_SCALE);
            $(this).data("newHeight", initHeight * HOVER_SCALE);
        });
        //event handlers
        appBar.bind("mouseleave",function(event)
        {
            $.each(icons, function()
            {
                $(this).animate(
                    {
                        "height": $(this).data("initHeight") + "px",
                        "width": $(this).data("initWidth") + "px"
                    },
                    "fast"
                );
            });
        });
        appBar.bind("mousemove", function(event)
        {
            $.each(icons, function()
            {
                var newSize = calculateSize($(this), event.pageX);
                $(this).stop();
                $(this).width(newSize[0]);
                $(this).height(newSize[1]);
                var marginScale = newSize[1] / $(this).data("initHeight");
                marginScale = (marginScale - 1) / (HOVER_SCALE - 1);
                $(this).css('margin-top', (10 - (10 * marginScale)) + 'px');
            });
        });
    });
});