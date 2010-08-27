<?php defined('_JEXEC') or die('Restricted Access') ?>
<?php

if ($self){$limit = '';}else{$limit = ' LIMIT 0,5';}

$this->db->setQuery('SELECT #__community_handicap_rounds.*, #__community_handicap_courses.name FROM #__community_handicap_rounds LEFT JOIN #__community_handicap_courses ON #__community_handicap_rounds.course_id = #__community_handicap_courses.id WHERE #__community_handicap_rounds.user_id=' . $user->id . ' ORDER BY #__community_handicap_rounds.date DESC '.$limit);

$rounds = $this->db->loadAssocList();

ob_start();

?>
<?php if ($notify) { ?>
<h3><?php echo $notify ?></h3><br />
<?php } ?>
<?php if (count($rounds)) { ?>
<div id="score-graph" style="width:100%;height:250px;"></div>
<br />
<p>
  Select <a id="hc-select-all" href="#">All</a> | <a id="hc-select-none" href="#">None</a>
</p>
<br />
<div style="height:100px;overflow:auto;">
  <form id="hc-delete-form" action="" method="" onsubmit="javascript:return false;">
    <table class="hc-table" style="width:100%;" cellspacing="1">
      <colgroup width="10%" />
      <colgroup width="25%" />
      <colgroup width="25%" />
      <colgroup width="15%" />
      <colgroup width="25%" />
      <tr class="hc-table-title">
        <th>#</th>
        <th>Date</th>
        <th>Course</th>
        <th>Score</th>
        <th>Course Hcap</th>
      </tr>
      <?php for ($n=1;$n<=count($rounds);$n++) { ?>
        <?php $round = $rounds[$n-1]; ?>
      <tr>
        <td class="hc-table-title"><input type="checkbox" name="ajax[course][<?php echo $n ?>]" /> <?php echo $n ?></td>
        <td><?php echo date('d/m/Y',$round['timestamp']) ?></td>
        <td><?php $this->_hsc($round['name']) ?></td>
        <td><?php echo $round['par_total'] ?></td>
        <td><?php echo $round['score_total'] ?></td>
      </tr>
      <?php } ?>
    </table>
  </form>
</div><br />
<p>
 <a id="hc-new-round" href="#"> Add new round</a> | <a id="hc-edit-score" href="#"> Edit score</a> | <a id="hc-delete-round" href="#"> Delete</a>
</p>
<?php } else { ?>
<h3>There are no rounds to show</h3>
<p>
 <a id="hc-new-round" href="#"> Add new round</a>
</p>
<?php } ?>
<?php $layout = ob_get_clean();