jQuery('#cWindowContent #handicap-pagination a').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-scores&ajax[page]='+jQuery(this).attr('href').match(/\d+/)+'")', Handicap.displayName+' scores', 600, 450);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#cWindowContent table a').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=edit-score&ajax[round_id]='+jQuery(this).attr('href').match(/\d+/)+'")', 'Edit score', 600, 450);
  jQuery('#cWindowContent').focus();
  return false;
});

Handicap.submit = function () {
  var serialized = jQuery('#hc-delete-rounds').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-scores&'+serialized+'")',Handicap.displayName+' scores',600,450);
  return false;
};

jQuery('#cWindowContent a[href="#delete"]').click(function(){
  Handicap.submit();
});