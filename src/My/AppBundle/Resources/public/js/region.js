window.regionCategoriesCheck = function(){
    if($('.category-price:checked').size() > 0){
        $('#empty-categories-alert').hide();
    } else {
        $('#empty-categories-alert').show();
    }
}

window.zeroSecondPriceCheck = function(){
    var isZero = false;
    $('.second-price').each(function(){
        if($(this).val() == 0){
            isZero = true;
        }
    })
    if(isZero){
        $('#zero-second-price-alert').show();
    } else {
        $('#zero-second-price-alert').hide();
    }
}

$(document).ready(function(){
    window.regionCategoriesCheck();
    window.zeroSecondPriceCheck();
    $('.category-price').on('change', function(){
        window.regionCategoriesCheck();
    })
    $('.second-price').on('change', function(){
        window.zeroSecondPriceCheck()
    })
})