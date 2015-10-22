/**
 * Functions
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Zoom Magnifier
 * @version 1.1.2
 */
jQuery(document).ready(function(b){var a=b(".images"),d=b(".yith_magnifier_zoom"),f=b(".yith_magnifier_zoom img"),e=a.find(".yith_magnifier_zoom").attr("href"),g=a.find(".yith_magnifier_zoom img").attr("src");a.yith_magnifier(yith_magnifier_options);b(document).on("found_variation","form.variations_form",function(b,c){var h=c.image_src?c.image_src:g;d.attr("href",c.image_magnifier?c.image_magnifier:e);f.attr("src",h);a.data("yith_magnifier")&&a.yith_magnifier("destroy");a.yith_magnifier(yith_magnifier_options)}).on("reset_image",
    function(b){d.attr("href",e);a.data("yith_magnifier")&&a.yith_magnifier("destroy");a.yith_magnifier(yith_magnifier_options)});b("form.variations_form .variations select").trigger("change")});