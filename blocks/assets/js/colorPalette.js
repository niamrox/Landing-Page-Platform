/*
 * Script for switching between Color Palettes in the templates / web blocks
 * 
 * PREREQUISITES:
 * The page that uses this script needs the have a stylesheet defined exactly like this:
 * <style type="text/css" title="color-palett" id="colorPalette"></style>
 *
 * 
 * USAGE:
 * Each color pallette should be define as a object and passed on to the colorPalette.change function
 * Example color object:
 
 var MyColorPalette = {
    color_base: "rgb(103,58,183)",
    color_base_dark: "rgb(81,45,168)",
    color_base_light: "rgb(209,196,233)",

    color_accent: "rgb(0,150,136)",
    color_base_child: "rgb(255,255,255)",

    color_text_primary: "rgb(33,33,33)",
    color_text_secondary: "rgb(114,114,114)",
    color_divider: "rgb(182,182,182)",

    color_overlay: "rgba(103,58,183,0.65)",
    color_overlay_dark: "rgba(81,45,168,0.65)",
    color_overlay_light: "rgba(209,196,233,0.65)"

};

* Switch to this color pallette by passing the object like:
* colorPalette.change(MyColorPalette);
* 
* To revert back to the original color pallet pass a null or the colorDefaultObj like 
* colorPalette.change(null) or colorPalette.change(colorDefaultObj)
* 
* SAVING:
* To save the altered color palette it is only necessary to save the stylesheet. The script doesn't
* need to be included in the final output. So only save the following element and it's contents:
* <style type="text/css" title="color-palett" id="colorPalett"> ... altered styles ... </style>
* 
 */

/* Pre defined color palette objects */
var colorDefaultObj = null;
var colorObjects = {
    colorPurpleObj: {
        color_base: "rgb(103,58,183)",
        color_base_dark: "rgb(81,45,168)",
        color_base_light: "rgb(209,196,233)",

        color_accent: "rgb(0,150,136)",
        color_base_child: "rgb(255,255,255)",

        color_text_primary: "rgb(33,33,33)",
        color_text_secondary: "rgb(114,114,114)",
        color_divider: "rgb(182,182,182)",

        color_overlay: "rgba(103,58,183,0.65)",
        color_overlay_dark: "rgba(81,45,168,0.65)",
        color_overlay_light: "rgba(209,196,233,0.65)"

    },
    colorGreenyObj: {
        color_base: "rgb(139,195,74)",
        color_base_dark: "rgb(104,159,56)",
        color_base_light: "rgb(220,237,200)",

        color_accent: "rgb(205,220,57)",
        color_base_child: "rgb(255,255,255)",

        color_text_primary: "rgb(33,33,33)",
        color_text_secondary: "rgb(114,114,114)",
        color_divider: "rgb(182,182,182)",

        color_overlay: "rgba(139,195,74,0.65)",
        color_overlay_dark: "rgba(104,159,56,0.65)",
        color_overlay_light: "rgba(220,237,200,0.65)"

    },
    colorRedObj: {
        color_base: "rgb(244,67,54)",
        color_base_dark: "rgb(211,47,47)",
        color_base_light: "rgb(255,205,210)",

        color_accent: "rgb(255,87,34)",
        color_base_child: "rgb(255,255,255)",

        color_text_primary: "rgb(33,33,33)",
        color_text_secondary: "rgb(114,114,114)",
        color_divider: "rgb(182,182,182)",

        color_overlay: "rgba(244,67,54,0.65)",
        color_overlay_dark: "rgba(211,47,47,0.65)",
        color_overlay_light: "rgba(255,205,210,0.65)"

    }
}

var colorPalette = {

    /* Change color palette of page */
    change: function (colorPaletteObj) {
        var styleSheet = document.getElementById('colorPalette');
        if (styleSheet) {
            if (colorPaletteObj) {
                var newColorPalett = this._sourceStyle().replace(/\bcolor_base\b|\bcolor_base_dark\b|\bcolor_base_light\b|\bcolor_accent\b|\bcolor_base_child\b|\bcolor_text_primary\b|\bcolor_text_secondary\b|\bcolor_divider\b|\bcolor_overlay\b|\bcolor_overlay_dark\b|\bcolor_overlay_light\b/gi, function (matched) {
                    return colorPaletteObj[matched];
                });

                styleSheet.innerHTML = newColorPalett;
            } else {
                if (styleSheet) styleSheet.innerHTML = "";
            }
        } else {
            if (typeof console == "object") console.log("Color Palette: Stylesheet not found");
        }
    },

    /* Stylesheet source string */
    _sourceStyle: function () { return " .color-base{background-color:color_base}.color-base-dark{background-color:color_base_dark}.color-base-light{background-color:color_base_light}.color-accent{background-color:color_accent}.color-base-child{background-color:color_base_child}.color-text-base{color:color_base}.color-text-base-dark{color:color_base_dark}.color-text-base-light{color:color_base_light}.color-text-accent{color:color_accent}.color-text-base-child{color:color_base_child;a{color:color_base_child;}small {color:color_base_light;}}.color-text-primary{color:color_text_primary}.color-text-secondary{color:color_text_secondary}.color-divider{color:color_divider}.color-overlay{background-color:color_overlay}.color-overlay-dark{background-color:color_overlay_dark}.color-overlay-light{background-color:color_overlay_light}"; },

    /* find color palette object and fill with available colors */
    showPalette: function () {
        var placeHolder = document.getElementById('colorPaletteBtns');
        if (placeHolder != null) {
            var colorBtns = '';
            for (var color in colorObjects){
                var colorObject = colorObjects[color];
                colorBtns += '<a onmouseover="colorPalette.change(colorObjects.' + color + ')" style="background-color: ' + colorObject.color_base + ';">' + color + '</a>';
            }
            placeHolder.innerHTML = colorBtns;
        }
        //Add stylesheet for palette buttons
        var btnStyles = document.createElement('style');
        btnStyles.type = 'text/css';
        btnStyles.innerHTML = '#colorPaletteBtns a{display:inline-block;height:30px;width:120px;margin-right:10px;text-align:center;color:#fff}'
        document.head.appendChild(btnStyles);
    }
}
/* find colorPaletteBtns */
colorPalette.showPalette();
