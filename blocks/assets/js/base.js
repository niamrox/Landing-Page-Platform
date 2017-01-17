$(function() {

	/*
	 * Viewport checker
	 */

	$('.vp').viewportChecker();

	/*
	 * Responsive text with fitText.js
	 */
	$('.text-responsive').fitText();

	/*
	 * Set text color based on background color
	 */
	$('.color-overlay').contrastColor();
});

/*
 * http://codeitdown.com/jquery-color-contrast/
 */
$.fn.contrastColor = function()
{
    return this.each(function()
    {
        var bg = $(this).css('background-color');
        //use first opaque parent bg if element is transparent
        if (bg == 'transparent' || bg == 'rgba(0, 0, 0, 0)')
        {
            $(this).parents().each(function()
            {
                bg = $(this).css('background-color')
                if (bg != 'transparent' && bg != 'rgba(0, 0, 0, 0)') return false;
            });
            //exit if all parents are transparent
            if (bg == 'transparent' || bg == 'rgba(0, 0, 0, 0)') return false;
        }
        //get r,g,b and decide
        var rgb = bg.replace(/^(rgb|rgba)\(/, '').replace(/\)$/, '').replace(/\s/g, '').split(',');
        var yiq = ((rgb[0] * 299) + (rgb[1] * 587) + (rgb[2] * 114)) / 1000;
        if (yiq >= 128) 
		{
			$(this).removeClass('color-text-base-child');
			$(this).addClass('text-dark');
		}
        else 
		{
			$(this).removeClass('text-dark');
			$(this).addClass('color-text-base-child');
		}
    });
};