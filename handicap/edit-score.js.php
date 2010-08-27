<?php ob_start(); ?>
if (typeof(Handicap) !== 'object') {
  Handicap = {};
}

Handicap.courses = new Array();
<?php foreach ($courses as $course) { ?>
Handicap.courses[<?php echo $course['id'] ?>] = {
  tees: new Array(),
  par: new Array()
};
Handicap.courses[<?php echo $course['id'] ?>].par = [0<?php if (count($course['par'])) { ?>,<?php } ?><?php echo join(',',$course['par']) ?>];
  <?php foreach ($course['tees'] as $n=>$tee) { ?>
    <?php if (!$tee['ranking'] && !$tee['slope']) {continue;} ?>
Handicap.courses[<?php echo $course['id'] ?>].tees[<?php echo $n ?>] = {
  color: <?php var_export($tee['color']) ?>,
  ranking: <?php echo number_format($tee['ranking'],2,'.',',') ?>,
  slope: <?php echo number_format($tee['slope'],2,'.',',') ?>
};
  <?php } ?>
<?php } ?>

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

jQuery('select[name="ajax[course_id]"]').change(function(){
  var hc = Handicap.courses[jQuery('option:selected','select[name="ajax[course_id]"]').val()];
  var tees = hc.tees;
  var par = hc.par;
  var options = '';
  var n=0;
  
  par.forEach(function(a,b){
    if (!b) {
      return true;
    }
    
    n += a;
    jQuery('input[name="ajax[par]['+b+']"]').val(a);
  });
  jQuery('input[name="ajax[par_total]"]').val(n);
  jQuery('#handicap-par').html('<b>'+n+'</b>');
  
  for (n=0;n<tees.length;n++) {
    if (typeof(tees[n]) === 'undefined') {
        continue;
    }
    
    options += '<option value="'+n+'">'+tees[n].color+'</option>';
  }
  
  jQuery('select[name="ajax[tee]"]').html(options);
  jQuery('select[name="ajax[tee]"]').change();
});

jQuery('select[name="ajax[tee]"]').change(function(){
  var course = jQuery('option:selected','select[name="ajax[course_id]"]').val();
  var tee = jQuery('option:selected','select[name="ajax[tee]"]').val();
  
  jQuery('input[name="ajax[ranking]"]').val(Handicap.courses[course].tees[tee].ranking)
  jQuery('input[name="ajax[slope]"]').val(Handicap.courses[course].tees[tee].slope)
});

jQuery('input[name="ajax[score_in]"],input[name="ajax[score_out]"]').change(function(){
    jQuery('input[name="ajax[score_total]"]').val(
        parseInt(jQuery('input[name="ajax[score_in]"]').val())+
        parseInt(jQuery('input[name="ajax[score_out]"]').val())
    );
});

Calendar.setup({
  inputField: 'round-date',
  ifFormat: '%Y-%m-%d',
  button: 'round-image_img',
  align: "Tl",
  singleClick: true
});

Handicap.submit = function () {
  if (jQuery('input[type=hidden][name="ajax[course_id]"]','#edit-score').length) {
    jQuery('.message','#cWindowContent').html('Please create a course first (your data might be lost)');
    return false;
  }
  
  if (jQuery('input[value=]','#edit-score').length) {
    jQuery('.message','#cWindowContent').html('Please fill in all the fields');
    return false;
  }
  
  jQuery('.message','#cWindowContent').html('');
  
  jQuery('input[name="ajax[ranking]"]').removeAttr('disabled');
  jQuery('input[name="ajax[slope]"]').removeAttr('disabled');
  jQuery('input[name^="ajax[par]"]').removeAttr('disabled');
  jQuery('input[name="ajax[par_total]"]').removeAttr('disabled');
  
  var serialized = jQuery('#edit-score').serialize();
  cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=edit-score&'+serialized+'")','Saving round changes...',600,450);
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
  jQuery.get('/','userid='+Handicap.userid+'&option=community&no_html=1&task=azrul_ajax&func=plugins,handicap,ajaxHandicap&arg2=["_d_","calculateHandicap"]');
}

jQuery('select[name="ajax[course_id]"] option[value=<?php echo number_format($ajax['course_id'],0) ?>]').attr('selected','selected');
jQuery('select[name="ajax[course_id]"]').change();
jQuery('select[name="ajax[tee]"] option[value=<?php echo number_format($ajax['tee'],0) ?>]').attr('selected','selected');
jQuery('select[name="ajax[tee]"]').change();

jQuery('#round-date_img').click(function(){jQuery('#round-date').click();});

<?php $script = ob_get_clean(); ?>