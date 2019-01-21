/*
   Repeatable text field editor support
   Source: https://code.tutsplus.com/articles/reusable-custom-meta-boxes-part-3-extra-fields--wp-23821
*/
jQuery('.repeatable-add').click(function() {
    field = jQuery(this).closest('div').find('.custom_repeatable li:last').clone(true);
    fieldLocation = jQuery(this).closest('div').find('.custom_repeatable li:last');
    jQuery('input', field).val('').attr('name', function(index, name) {
        return name.replace(/(\d+)/, function(fullMatch, n) {
            return Number(n) + 1;
        });
    })
    field.insertAfter(fieldLocation, jQuery(this).closest('div'))
    return false;
});

jQuery('.repeatable-remove').click(function(){
    if($('.repeatable-remove').length > 1) {
        jQuery(this).parent().remove();
        return false;
    }
});

jQuery('.custom_repeatable').sortable({
    opacity: 0.6,
    revert: true,
    cursor: 'move',
    handle: '.sort'
});