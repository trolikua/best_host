jQuery(document).ready(function(){
    let mainContainer = jQuery('.rating-container');
    let ratingWidth = mainContainer.width();
    if (ratingWidth < 768) {
        mainContainer.addClass('sm-rating-container');
        jQuery('.rating-wrapper').addClass('sm-rating-wrapper');
    }
    //console.log(ratingWidth);
});