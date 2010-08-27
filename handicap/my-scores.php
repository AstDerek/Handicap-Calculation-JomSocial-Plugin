<?php defined('_JEXEC') or die( 'Restricted access' ); ?>
<?php

$ajax['page'] = JFilterInput::clean($ajax['page'],'int');
if ($ajax['delete'] && $self) {
  $ajax['delete'] = JFilterInput::clean($ajax['delete'],'array');
  $ajax['delete'] = array_keys($ajax['delete']);
  $this->_clean_num_array($ajax['delete']);
  
  $this->db->setQuery('DELETE FROM #__community_handicap_rounds WHERE user_id=' . $user->id . ' AND id IN (' . join(',',$ajax['delete']) . ')');
  $this->db->query();
  
  if ($this->db->getAffectedRows() > 0) {
    $this->db->setQuery('UPDATE #__community_handicap_rounds SET `timestamp`=NOW() WHERE user_id=' . $user->id);
    $this->db->query();
    
    $notice = array(
      $this->db->getAffectedRows() . ' rounds have been successfully deleted!'
    );
    
    $this->_calculateHandicap($this->user);
  }
  else {
    $notice = array(
      'No rounds where deleted during the last action'
    );
  }
}

list($rounds,$pagination) = $this->rounds_from_user($user->id,$ajax);
ob_start();

?>
<?php if (is_array($notice) && count($notice)) { ?>
  <p>
    <ul>
      <li><?php echo join('</li><li>',$notice) ?></li>
    </ul>
  </p>
<?php } ?>
  <?php if (count($rounds)) { ?>
  <div id="handicap-pagination"><?php echo join(" | ",$pagination) ?></div>
  <br />
  <?php if ($self) { ?><form action="" method="" onsubmit="javascript:return Handicap.submit();" id="hc-delete-rounds"><?php } ?>
    <table class="hc-table" style="width:100%;" cellspacing="1">
      <?php if ($self) { ?><colgroup width="5%"></colgroup><?php } ?>
      <colgroup width="5%"></colgroup>
      <colgroup width="25%"></colgroup>
      <colgroup width="25%"></colgroup>
      <colgroup width="13%"></colgroup>
      <colgroup width="13%"></colgroup>
      <colgroup width="13%"></colgroup>
      <tr class="hc-table-title">
        <?php if ($self) { ?><th></th><?php } ?>
        <th>#</th>
        <th>Date</th>
        <th>Course</th>
        <th>Score</th>
        <th>Par</th>
        <th>Handicap</th>
      </tr>
      <?php for ($n=1;$n<=count($rounds);$n++) { ?>
        <?php $round = $rounds[$n-1]; ?>
      <tr>
        <?php if ($self) { ?><td><input type="checkbox" name="ajax[delete][<?php echo $round['id'] ?>]" /></td><?php } ?>
        <td class="hc-table-title"><a href="#<?php echo $round['id'] ?>"><?php echo ($n + ($ajax['page']*$this->pagination)) ?></a></td>
        <td><?php echo date('Y-m-d', strtotime($round['date'])) ?></td>
        <td><?php $this->_hsc($round['name']) ?></td>
        <td><?php echo number_format($round['score_total'],0) ?></td>
        <td><?php echo number_format($round['par_total'],0) ?></td>
        <td><?php echo number_format($round['handicap'],0) ?></td>
      </tr>
      <?php } ?>
    </table>
    <br />
    <?php if ($self) { ?><p>
      <a href="#delete">Delete selected</a>
    </p>
  </form><?php } ?>
  <?php } else { ?>
    <h3>There are no rounds to show</h3>
  <?php } ?>
    <br />
<?php if ($guest) { ?>
    <p>Want to see all the stats from this user? <a href="#">create an account</a></p>
<?php } else if ($self) { ?>
<?php } else { ?>
<?php } ?>
<?php $layout = ob_get_clean();