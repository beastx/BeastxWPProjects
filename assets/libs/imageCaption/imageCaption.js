jQuery(document).ready(function() {
    jQuery("img.hascaption").each(function() {
        var caption = document.createElement('p');
        jQuery(caption).addClass("caption");
        caption.innerHTML = jQuery(this).attr("title");
        jQuery(this).wrap('<div class="figure"></div>')
        .after(caption)
        .removeAttr('title');
    });
    jQuery(".figure").width('480px');

    jQuery(".figure").mouseenter(function(){
        jQuery(this).find('.caption').slideToggle();
    }).mouseleave(function(){
        jQuery(this).find('.caption').slideToggle();
    });
});