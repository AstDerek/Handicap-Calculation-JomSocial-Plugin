jQuery('#hc-new-round').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=add-new-round")', 'Add a new round', 600, 450);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#hc-edit-score').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=edit-score")', 'Edit your score', 450, 300);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#hc-delete-round').click(function(){
  var serialized = jQuery('#hc-delete-form').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-stats&'+serialized+'")', 'My stats', 600, 500);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#hc-select-all').click(function(){
  jQuery('#cWindowContent input[type=checkbox]').attr('checked','checked');
  return false;
});

jQuery('#hc-select-none').click(function(){
  jQuery('#cWindowContent input[type=checkbox]').removeAttr('checked');
  return false;
});

jQuery('#score-graph').ready(function(){
  if (!jQuery('#score-graph').length) {
    return;
  }
  
  new Dygraph(jQuery('#score-graph').get(0),
    Handicap.base+'?option=community&no_html=1&task=azrul_ajax&func=plugins,handicap,ajaxHandicap&arg2=["_d_","section=dygraph"]',
    {yAxisLabelWidth:20});
});