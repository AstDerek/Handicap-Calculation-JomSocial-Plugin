<?php defined('_JEXEC') or die('Restricted Access') ?>
<?php

$ajax = JFilterInput::clean($ajax,'array');
$ajax['page'] = JFilterInput::clean($ajax['page'],'int');

if ($ajax['delete'] && $self) {
  $ajax['delete'] = JFilterInput::clean($ajax['delete'],'array');
  $ajax['delete'] = array_keys($ajax['delete']);
  $this->_clean_num_array($ajax['delete']);
  
  $this->db->setQuery('DELETE FROM #__community_handicap_courses WHERE id IN (' . join(',',$ajax['delete']) . ')');
  $this->db->query();
  
  if ($this->db->getAffectedRows() > 0) {
    $notice = array(
      $this->db->getAffectedRows() . ' courses have been successfully deleted!'
    );
  }
  else {
    $notice = array(
      'No courses where deleted during the last action'
    );
  }
}

list($courses,$pagination) = $this->courses_from_user($user->id,$ajax);

$this->db->setQuery('SELECT * FROM #__community_handicap_locations');
$locations = $this->db->loadAssocList('id');

ob_start();

?>
<?php if (is_array($notice) && count($notice)) { ?>
  <p>
    <ul>
      <li><?php echo join('</li><li>',$notice) ?></li>
    </ul>
  </p>
<?php } ?>
<?php if ($self) { ?>
<p>
  <a id="hc-new-course" href="#">Add a new course</a>
</p>
<br />
<?php } ?>
<?php if (count($courses)) { ?>
  <div id="handicap-pagination"><?php echo join(" | ",$pagination) ?></div>
<form action="" method="" onsubmit="javascript:return Handicap.submit();" id="hc-delete-courses">
<table class="hc-table" style="width:100%;">
  <tr class="hc-table-title">
    <th>&nbsp;</th>
    <th>#</th>
    <th>Course</th>
    <th>User ID</th>
    <th>Location</th>
    <th>Country</th>
  </tr>
  <?php for ($n=1;$n<=count($courses);$n++) { ?>
    <?php $course = $courses[$n - 1]; ?>
  <tr>
    <td><input type="checkbox" name="ajax[delete][<?php echo $course['id'] ?>]" /></td>
    <td class="hc-table-title"><a href="#<?php echo $course['id'] ?>"><?php echo ($n + ($ajax['page']*$this->pagination)) ?></a></td>
    <td><?php $this->_hsc($course['name']) ?></td>
    <td><?php $this->_hsc($course['user_id']) ?><?php if ($user->id == $course['user_id']) { ?> (You)<?php } ?></td>
    <td><?php $this->_hsc($locations[$course['location_id']]['name']) ?></td>
    <td><?php $this->_hsc($locations[$course['country_id']]['name']) ?></td>
  </tr>
  <?php } ?>
</table>
<br />
<p>
  <a href="#delete">Delete selected</a>
</p>
</form>
<?php } else { ?>
<h3>There are no courses registered</h3>
<?php } ?>
<?php $layout = ob_get_clean();