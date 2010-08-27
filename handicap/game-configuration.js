jQuery('#hc-save-config').click(function(){
  var serialized = jQuery('#game-configuration').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=game-configuration&'+serialized+'")','Game configuration',600,300);
  return false;
});