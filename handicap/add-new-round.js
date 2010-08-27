jQuery('input.handicap-par').change(function(){
  var total = 0, tin = 0, tout = 0, c, m, n, p;
  
  c = jQuery('.handicap-par');
  for (n=0;n<c.length;n++) {
    m = parseInt(jQuery(c.get(n)).val());
    if (isNaN(m)) {
      continue;
    }
    
    p = jQuery('.handicap-score[name="'+jQuery(c.get(n)).attr('name').replace('par','score')+'"]');
    if (p.val() === '') {
      p.val(0);
    }
    p = jQuery('.handicap-other[name="'+jQuery(c.get(n)).attr('name').replace('par','other')+'"]');
    if (p.val() === '') {
      p.val(0);
    }
    
    total += m;
    if (jQuery(c.get(n)).hasClass('handicap-in')) {
      tin += m;
    }
    else {
      tout += m;
    }
  }
  
  jQuery('#handicap-par').text(total);
  jQuery('#hc-par-in').text(tin);
  jQuery('#hc-par-out').text(tout);
});

jQuery('input.handicap-score').change(function(){
  var total = 0, tin = 0, tout = 0, c, m, n, p;
  
  c = jQuery('.handicap-score');
  for (n=0;n<c.length;n++) {
    m = parseInt(jQuery(c.get(n)).val());
    if (isNaN(m)) {
      continue;
    }
    
    p = jQuery('.handicap-par[name="'+jQuery(c.get(n)).attr('name').replace('score','par')+'"]');
    if (p.val() === '') {
      p.val(0);
    }
    p = jQuery('.handicap-other[name="'+jQuery(c.get(n)).attr('name').replace('score','other')+'"]');
    if (p.val() === '') {
      p.val(0);
    }
    
    total += m;
    if (jQuery(c.get(n)).hasClass('handicap-in')) {
      tin += m;
    }
    else {
      tout += m;
    }
  }
  
  jQuery('#handicap-score').text(total);
  jQuery('#hc-score-in').text(tin);
  jQuery('#hc-score-out').text(tout);
});

jQuery('input.handicap-other').change(function(){
  var total = 0, tin = 0, tout = 0, c, m, n, p;
  
  c = jQuery('.handicap-other');
  for (n=0;n<c.length;n++) {
    m = parseInt(jQuery(c.get(n)).val());
    if (isNaN(m)) {
      continue;
    }
    
    p = jQuery('.handicap-par[name="'+jQuery(c.get(n)).attr('name').replace('other','par')+'"]');
    if (p.val() === '') {
      p.val(0);
    }
    p = jQuery('.handicap-score[name="'+jQuery(c.get(n)).attr('name').replace('other','score')+'"]');
    if (p.val() === '') {
      p.val(0);
    }
    
    if (jQuery(c.get(n)).hasClass('handicap-in')) {
      tin += m;
    }
    else {
      tout += m;
    }
  }
});

jQuery('select[name="ajax[course]"]').change(function(){
  var tees = Handicap.courses[jQuery('option:selected','select[name="ajax[course]"]').val()].tees;
  var options = '';
  var n;
  
  for (n=0;n<tees.length;n++) {
    options += '<option value="'+n+'">'+tees[n].color+'</option>';
  }
  
  jQuery('select[name="ajax[tee]"]').html(options);
  jQuery('select[name="ajax[tee]"]').change();
});

jQuery('select[name="ajax[tee]"]').change(function(){
  var course = jQuery('option:selected','select[name="ajax[course]"]').val();
  var tee = jQuery('option:selected','select[name="ajax[tee]"]').val();
  
  jQuery('input[name="ajax[ranking]"]').val(Handicap.courses[course].tees[tee].ranking)
  jQuery('input[name="ajax[slope]"]').val(Handicap.courses[course].tees[tee].slope)
});

Calendar.setup({
  inputField: 'round-date',
  ifFormat: '%Y-%m-%d',
  button: 'round-image_img',
  align: "Tl",
  singleClick: true
});

if (typeof(Handicap) !== 'object') {
  Handicap = {};
}

Handicap.submit = function () {
  if (jQuery('input[type=hidden][name="ajax[course]"]','#add-new-round').length) {
    jQuery('.message','#cWindowContent').html('Please create a course first (your data might be lost)');
    return false;
  }
  
  if (jQuery('input[value=]','#add-new-round').length) {
    jQuery('.message','#cWindowContent').html('Please fill in all the fields');
    return false;
  }
  
  jQuery('.message','#cWindowContent').html('');
  
  var serialized = jQuery('#add-new-round').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=add-new-round&'+serialized+'")','Adding new round...',600,450);
  return false;
};

jQuery('#hc-new-course').click(function(){
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=add-new-course")', 'Add new course', 600, 450);
  jQuery('#cWindowContent').focus();
  return false;
});

jQuery('#hc-cancel').click(function(){
  cWindowClose();
  return false;
});

if (jQuery('input[name="ajax[success]"]').length) {
  jQuery('#cWindowContent input,select').attr('disabled','disabled');
}