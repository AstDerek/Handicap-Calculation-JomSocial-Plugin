jQuery('#hc-new-course').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=add-new-course")', 'Add a new course', 450, 300);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#cWindowContent #handicap-pagination a').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-courses&ajax[page]='+jQuery(this).attr('href').match(/\d+/)+'")', Handicap.displayName+' courses', 450, 300);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#cWindowContent table a').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=edit-course&ajax[course_id]='+jQuery(this).attr('href').match(/\d+/)+'")', 'Edit course', 450, 300);
  jQuery('#cWindowContent').focus();
  return false;
});

Handicap.submit = function () {
  var serialized = jQuery('#hc-delete-courses').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-courses&'+serialized+'")',Handicap.displayName+' courses', 450, 300);
  return false;
};

jQuery('#cWindowContent a[href="#delete"]').click(function(){
  Handicap.submit();
});