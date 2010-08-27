<?php defined('_JEXEC') or die('Restricted Access') ?>
<?php

$ajax['round_id'] = JFilterInput::clean($ajax['round_id'],'int');

//$this->_clean_num_array($ajax['par'],4);
//$this->_clean_num_array($ajax['score'],0);
$ajax['par'] = JFilterInput::clean($ajax['par'],'array');
$ajax['score'] = JFilterInput::clean($ajax['score'],'array');
$ajax['other'] = JFilterInput::clean($ajax['other'],'array');

$ajax['course_id'] = JFilterInput::clean($ajax['course_id'],'int');

$ajax['ranking'] = JFilterInput::clean($ajax['ranking'],'int');
$ajax['slope'] = JFilterInput::clean($ajax['slope'],'int');
$ajax['tee'] = JFilterInput::clean($ajax['tee'],'string');

$ajax['round-date'] = JFilterInput::clean($ajax['round-date'],'string');

if ($ajax['slope'] < 1) {
  $ajax['slope'] = 1;
}

if ($ajax['save']) {
  $notice = $this->edit_score($ajax);
}
else {
  $notice = $this->round_from_user($user->id,$ajax);
}

$config = $this->config_from_user($user->id);
$dummy = -1;
$courses = $this->courses_from_user($user->id,$dummy);
$total = 0;

ob_start();

?>
<?php if ($notice === true) { ?>
  <h3>Score edited successfully!</h3>
  <input type="hidden" name="ajax[success]" />
  <br />
<?php } else if (is_array($notice) && count($notice)) { ?>
<p>
  <ul>
    <li><?php echo join('</li><li>',$notice) ?></li>
  </ul>
</p>
<?php } ?>
<form id="edit-score" action="" method="" onsubmit="javascript:return Handicap.submit();">
  <table style="width:100%;">
    <colgroup width="65%" />
    <colgroup width="35%" />
    <tr>
      <td>
        <h3>Course</h3>
        <?php if (count($courses)) { ?>
        <select name="ajax[course_id]">
          <optgroup label="Select course...">
            <?php foreach ($courses as $course) { ?>
            <option value="<?php $this->_hsc($course['id']) ?>" <?php $this->_select($ajax['course_id'],$id) ?>><?php $this->_hsc($course['name']) ?></option>
            <?php } ?>
          </optgroup>
        </select>
        <?php } else { ?>
        <input type="hidden" name="ajax[course_id]" />
        No courses registered, please <a id="hc-new-course" href="#">add a new course</a> first.
        <?php } ?>
      </td>
      <td>
        <h3>Date</h3>
        <?php echo JHTML::calendar($ajax['round-date'] ? date('Y-m-d',strtotime($ajax['round-date'])) : date('Y-m-d'),'ajax[round-date]','round-date','%Y-%m-%d') ?>
      </td>
    </tr>
    <tr>
      <td>
        <input type="text" disabled="disabled" name="ajax[ranking]" value="<?php $this->_hsc($ajax['ranking']) ?>" size="4" /> Rating<br />
        <input type="text" disabled="disabled" name="ajax[slope]" value="<?php $this->_hsc($ajax['slope']) ?>" size="4" /> Slope
      </td>
      <td>
        Tees<br />
        <select name="ajax[tee]">
        </select>
      </td>
    </tr>
  </table>
  <br />
  <h3>Scores</h3>
  <br />
  
<?php if ($config['rounds']['simple']) { ?>
  <div class="hc-config-label">Front 9 Total</div>
  <div class="hc-config-option"><input type="text" name="ajax[score_in]" value="<?php echo number_format($ajax['score_in'],0) ?>" /></div>
  <div class="hc-config-label">Back 9 Total</div>
  <div class="hc-config-option"><input type="text" name="ajax[score_out]" value="<?php echo number_format($ajax['score_out'],0) ?>" /></div>
  <div class="hc-config-label">Total</div>
  <div class="hc-config-option">
    <input type="hidden" disabled="disabled" name="ajax[par_total]" value="<?php echo number_format($ajax['par_total'],0) ?>" />
    <input type="text" disabled="disabled" name="ajax[score_total]" value="<?php echo number_format($ajax['score_total'],0) ?>" />
  </div>
<?php } else { ?>
  <table class="hc-table" cellspacing="1" style="width:100%;">
    <colgroup width="10%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <colgroup width="9%" />
    <tr class="hc-table-title">
      <td>Hole</td>
      <?php for ($n=1;$n<10;$n++) { ?><td><?php echo $n ?></td><?php } ?> 
      <td>Out</td>
    </tr>
    <tr>
      <td class="hc-table-title">Par</td>
      <?php for ($n=1,$total=0;$n<10;$total+=$ajax['par'][$n],$n++) { ?><td><input class="handicap-par handicap-out" type="text" disabled="disabled" name="ajax[par][<?php echo $n ?>]" value="<?php echo number_format($ajax['par'][$n],0) ?>" /></td><?php } ?> 
      <td><span id="hc-par-out"><?php echo $total ?></span></td>
    </tr>
    <tr>
      <td class="hc-table-title">Score</td>
      <?php for ($n=1,$total=0;$n<10;$total+=$ajax['score'][$n],$n++) { ?><td><input class="handicap-score handicap-out" type="text" name="ajax[score][<?php echo $n ?>]" value="<?php echo number_format($ajax['score'][$n],0) ?>" /></td><?php } ?> 
      <td><span id="hc-score-out"><?php echo $total ?></span></td>
    </tr>
    <?php if (0) { ?><tr>
      <td class="hc-table-title">HCP</td>
      <?php for ($n=1,$total=0;$n<10;$total+=$ajax['other'][$n],$n++) { ?><td><input class="handicap-other handicap-out" type="text" name="ajax[other][<?php echo $n ?>]" value="<?php echo number_format($ajax['other'][$n],0) ?>" /></td><?php } ?> 
      <td><span id="hc-other-out"><?php echo $total ?></span></td>
    </tr><?php } ?>
    <tr>
      <td colspan="11"></td>
    </tr>
    <tr class="hc-table-title">
      <td>Hole</td>
      <?php for ($n=10;$n<19;$n++) { ?><td><?php echo $n ?></td><?php } ?> 
      <td>In</td>
    </tr>
    <tr>
      <td class="hc-table-title">Par</td>
      <?php for ($n=10,$total=0;$n<19;$total+=$ajax['par'][$n],$n++) { ?><td><input class="handicap-par handicap-in" type="text" disabled="disabled" name="ajax[par][<?php echo $n ?>]" value="<?php echo number_format($ajax['par'][$n],0) ?>" /></td><?php } ?> 
      <td><span id="hc-par-in"><?php echo $total ?></span></td>
    </tr>
    <tr>
      <td class="hc-table-title">Score</td>
      <?php for ($n=10,$total=0;$n<19;$total+=$ajax['score'][$n],$n++) { ?><td><input class="handicap-score handicap-in" type="text" name="ajax[score][<?php echo $n ?>]" value="<?php echo number_format($ajax['score'][$n],0) ?>" /></td><?php } ?> 
      <td><span id="hc-score-in"><?php echo $total ?></span></td>
    </tr>
    <?php if (0) { ?><tr>
      <td class="hc-table-title">HCP</td>
      <?php for ($n=10,$total=0;$n<19;$total+=$ajax['other'][$n],$n++) { ?><td><input class="handicap-other handicap-in" type="text" name="ajax[other][<?php echo $n ?>]" value="<?php echo number_format($ajax['other'][$n],0) ?>" /></td><?php } ?> 
      <td><span id="hc-other-in"><?php echo $total ?></span></td>
    </tr><?php } ?>
  </table>
  <br />
  <p>
    Par <span id="handicap-par"><?php echo array_sum($ajax['par']) ?></span> - Total score is <span id="handicap-score"><?php echo array_sum($ajax['score']) ?></span>
  </p>
<?php } ?>
  <br />
  
<?php if ($config['rounds']['callaway_scores'] || $config['rounds']['green_fees'] || $config['rounds']['fairways_hit'] || $config['rounds']['miss_hits'] || $config['rounds']['penalty_strokes']) { ?>
  <h3>Misc</h3>
  <?php if ($config['rounds']['callaway_scores'] || $ajax['other']['callaway_scores']) { ?>
  <div class="hc-config-label">Callaway scores</div>
  <div class="hc-config-option"><input type="text" name="ajax[other][callaway_scores]" value="<?php echo number_format($ajax['other']['callaway_scores'],0) ?>" /></div>
  <?php } ?>
  <?php if ($config['rounds']['green_fees'] || $ajax['other']['green_fees']) { ?>
  <div class="hc-config-label">Green fees</div>
  <div class="hc-config-option"><input type="text" name="ajax[other][green_fees]" value="<?php echo number_format($ajax['other']['green_fees'],0) ?>" /></div>
  <?php } ?>
  <?php if ($config['rounds']['fairways_hit'] || $ajax['other']['fairways_hit']) { ?>
  <div class="hc-config-label">Fairways</div>
  <div class="hc-config-option"><input type="text" name="ajax[other][fairways_hit]" value="<?php echo number_format($ajax['other']['fairways_hit'],0) ?>" /></div>
  <?php } ?>
  <?php if ($config['rounds']['miss_hits'] || $ajax['other']['miss_hits']) { ?>
  <div class="hc-config-label">Miss hits</div>
  <div class="hc-config-option"><input type="text" name="ajax[other][miss_hits]" value="<?php echo number_format($ajax['other']['miss_hits'],0) ?>" /></div>
  <?php } ?>
  <?php if ($config['rounds']['penalty_strokes'] || $ajax['other']['penalty_strokes']) { ?>
  <div class="hc-config-label">Penalty strokes</div>
  <div class="hc-config-option"><input type="text" name="ajax[other][penalty_strokes]" value="<?php echo number_format($ajax['other']['penalty_strokes'],0) ?>" /></div>
  <?php } ?>
<?php } ?>
  <div class="clear"></div>
  
  <input type="hidden" name="ajax[round_id]" value="<?php echo $ajax['round_id'] ?>" />
  <input type="hidden" name="ajax[save]" value="1" />
  <input type="submit" value="Save" /> <span class="message"></span>
</form>
<?php

$layout = ob_get_clean();