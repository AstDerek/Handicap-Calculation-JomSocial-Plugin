<?php defined('_JEXEC') or die('Restricted Access') ?>
<?php

if ($ajax['save']) {
  $notice = $this->save_game_configuration($ajax);
}

$this->db->setQuery('SELECT config FROM #__community_handicap WHERE user_id=' . $user->id);
$config = $this->db->loadResult();
$config = $config ? unserialize($config) : array();

$stats = $config['stats'] ? $config['stats'] : array();
$rounds = $config['rounds'] ? $config['rounds'] : array();

ob_start();

?>
<?php if ($notice === true) { ?>
  <h3>Configuration edited successfully!</h3>
  <br />
<?php } else if (is_array($notice) && count($notice)) { ?>
<p>
  <ul>
    <li><?php echo join('</li><li>',$notice) ?></li>
  </ul>
</p>
<?php } ?>
<form id="game-configuration" action="" method="" onsubmit="javascript:return false;">
<table style="width:100%;">
  <colgroup width="50%" />
  <colgroup width="50%" />
  <tr>
      <?php if (0) { ?><th>Which Statistics would you like to track?</th><?php } ?>
      <th>Select the fields you would like on the Rounds Played page</th>
  </tr>
  <tr>
    <?php if (0) { ?><td>
      <p>
        <input type="radio" name="ajax[stats][all_rounds]" value="1" <?php $this->_check($stats['all_rounds'],1) ?> /> All my rounds<br />
        <input type="radio" name="ajax[stats][all_rounds]" value="0" <?php $this->_check($stats['all_rounds'],0) ?> /> Just this year's rounds
      </p>
      <p>
        <input type="checkbox" name="ajax[stats][green_fees]" <?php $this->_check($stats['green_fees'],'on') ?> /> Green fees<br />
        <input type="checkbox" name="ajax[stats][fairways_hit]" <?php $this->_check($stats['fairways_hit'],'on') ?> /> Fairways hit on Par 4's &amp; 5's<br />
        <input type="checkbox" name="ajax[stats][driving_distance]" <?php $this->_check($stats['driving_distance'],'on') ?> /> Driving Distance<br />
        <input type="checkbox" name="ajax[stats][miss_hits]" <?php $this->_check($stats['miss_hits'],'on') ?> /> Miss hits<br />
        <input type="checkbox" name="ajax[stats][penalty_strokes]" <?php $this->_check($stats['penalty_strokes'],'on') ?> /> Penalty Strokes<br />
        <input type="checkbox" name="ajax[stats][sand_saves]" <?php $this->_check($stats['sand_saves'],'on') ?> /> Sand saves<br />
        <input type="checkbox" name="ajax[stats][greens_regulation]" <?php $this->_check($stats['greens_regulation'],'on') ?> /> Greens in Regulation<br />
        <input type="checkbox" name="ajax[stats][putts]" <?php $this->_check($stats['putts'],'on') ?> /> Putts
      </p>
    </td><?php } ?>
    <td>
      <p>
        <input type="radio" name="ajax[rounds][simple]" value="1" <?php $this->_check($rounds['simple'],1) ?> /> Simplified view at round edition<br />
        <input type="radio" name="ajax[rounds][simple]" value="0" <?php $this->_check($rounds['simple'],0) ?> /> Full view at round edition
      </p>
      <p>
        <?php if (0) { ?><input type="checkbox" name="ajax[rounds][gross_score]" <?php $this->_check($rounds['gross_score'],'on') ?> /> Gross Score<br />
        <input type="checkbox" name="ajax[rounds][adjusted_score]" <?php $this->_check($rounds['adjusted_score'],'on') ?> /> Adjusted Score<br />
        <input type="checkbox" name="ajax[rounds][plus_minus]" <?php $this->_check($rounds['plus_minus'],'on') ?> /> Plus/minus<br />
        <input type="checkbox" name="ajax[rounds][differential]" <?php $this->_check($rounds['differential'],'on') ?> /> Handicap Differential<br /><?php } ?>
        <input type="checkbox" name="ajax[rounds][callaway_scores]" <?php $this->_check($rounds['callaway_scores'],'on') ?> /> Callaway Scores<br />
        <input type="checkbox" name="ajax[rounds][green_fees]" <?php $this->_check($rounds['green_fees'],'on') ?> /> Green fees<br />
        <input type="checkbox" name="ajax[rounds][fairways_hit]" <?php $this->_check($rounds['fairways_hit'],'on') ?> /> Fairways hit on Par 4's &amp; 5's<br />
        <input type="checkbox" name="ajax[rounds][miss_hits]" <?php $this->_check($rounds['miss_hits'],'on') ?> /> Miss hits<br />
        <input type="checkbox" name="ajax[rounds][penalty_strokes]" <?php $this->_check($rounds['penalty_strokes'],'on') ?> /> Penalty Strokes<br />
      </p>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <p style="text-align:center;">
        <input type="hidden" name="ajax[save]" value="1" />
        <input id="hc-save-config" type="submit" value="Save" />
        <input type="reset" value="Reset" />
      </p>
    </td>
  </tr>
</form>
<?php $layout = ob_get_clean();