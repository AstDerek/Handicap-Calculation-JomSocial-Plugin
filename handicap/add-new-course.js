jQuery('input[name="ajax[location_alt]"]').change(function(){
  if (jQuery('input[name="ajax[location_alt]"]:checked').length) {
    jQuery('select[name="ajax[location_id]"]').removeAttr('disabled').show();
    jQuery('input[name="ajax[location]"]').attr('disabled','disabled').hide();
  }
  else {
    jQuery('input[name="ajax[location]"]').removeAttr('disabled').show();
    jQuery('select[name="ajax[location_id]"]').attr('disabled','disabled').hide();
  }
});

jQuery('input[name="ajax[country_alt]"]').change(function(){
  if (jQuery('input[name="ajax[country_alt]"]:checked').length) {
    jQuery('select[name="ajax[country_id]"]').removeAttr('disabled').show();
    jQuery('input[name="ajax[country]"]').attr('disabled','disabled').hide();
  }
  else {
    jQuery('input[name="ajax[country]"]').removeAttr('disabled').show();
    jQuery('select[name="ajax[country_id]"]').attr('disabled','disabled').hide();
  }
});

if (typeof(Handicap) !== 'object') {
  Handicap = {};
}

Handicap.newCourseSubmit = function(){
  if (jQuery('#hc-add-new-course input[value=]').length) {
    jQuery('#cWindowContent .message').html('Please fill in all the fields');
    return false;
  }
  
  var serialized = jQuery('#hc-add-new-course').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=add-new-course&'+serialized+'")', 'Add a new course', 450, 300);
  jQuery('#cWindowContent').focus();
  return false;
};