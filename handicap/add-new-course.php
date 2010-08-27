<?php defined('_JEXEC') or die('Restricted Access') ?>
<?php

$ajax['name'] = JFilterInput::clean($ajax['name'],'string');

$ajax['location'] = JFilterInput::clean($ajax['location'],'string');
$ajax['country'] = JFilterInput::clean($ajax['country'],'string');

if ($ajax['save']) {
  $notice = $this->add_new_course($ajax);
}

$countries = $this->list_countries();
$locations = $this->list_locations();

ob_start();

?>
<?php if ($notice === true) { ?>
<h3>Course saved successfully!</h3>
<input type="hidden" name="ajax[success]" />
<br />
<?php } else if (is_array($notice) && count($notice)) { ?>
<p>
  <ul>
    <li><?php echo join('</li><li>',$notice) ?></li>
  </ul>
</p>
<?php } ?>
<form id="hc-add-new-course" action="" method="" onsubmit="javascript:return Handicap.newCourseSubmit();">
  <table style="width:100%;">
    <tr>
      <td colspan="2">
        <h3>Course name</h3>
        <input type="text" name="ajax[name]" value="<?php $this->_hsc($ajax['name']) ?>" style="width:80%;" />
      </td>
    </tr>
    <tr>
      <td>
        <h3>Location</h3>
        <input type="text" name="ajax[location]" value="<?php $this->_hsc($ajax['location']) ?>" />
        <br />
        <hr />
      </td>
      <td>
        <h3>Country</h3>
        <input type="text" name="ajax[country]" value="<?php $this->_hsc($ajax['country']) ?>" />
        <br />
        <hr />
      </td>
    </tr>
    <tr>
      <td>
        <h3>Forward Tee</h3>
        <b>Rating</b><br />
        <input type="hidden" name="ajax[tees][0][color]" value="forward" />
        <input type="text" name="ajax[tees][0][ranking]" value="<?php echo number_format($ajax['tees'][0]['ranking'],2) ?>" /><br />
        <b>Slope</b><br />
        <input type="text" name="ajax[tees][0][slope]" value="<?php echo number_format($ajax['tees'][0]['slope'],0) ?>" /><br />
        <hr />
      </td>
      <td>
        <h3>Middle Tee</h3>
        <b>Rating</b><br />
        <input type="hidden" name="ajax[tees][1][color]" value="middle" />
        <input type="text" name="ajax[tees][1][ranking]" value="<?php echo number_format($ajax['tees'][1]['ranking'],2) ?>" /><br />
        <b>Slope</b><br />
        <input type="text" name="ajax[tees][1][slope]" value="<?php echo number_format($ajax['tees'][1]['slope'],0) ?>" /><br />
        <hr />
      </td>
    </tr>
    <tr>
      <td>
        <h3>Back Tee</h3>
        <b>Rating</b><br />
        <input type="hidden" name="ajax[tees][2][color]" value="back" />
        <input type="text" name="ajax[tees][2][ranking]" value="<?php echo number_format($ajax['tees'][2]['ranking'],2) ?>" /><br />
        <b>Slope</b><br />
        <input type="text" name="ajax[tees][2][slope]" value="<?php echo number_format($ajax['tees'][2]['slope'],0) ?>" /><br />
        <hr />
      </td>
      <td>
        <h3>Championship Tee</h3>
        <b>Rating</b><br />
        <input type="hidden" name="ajax[tees][3][color]" value="championship" />
        <input type="text" name="ajax[tees][3][ranking]" value="<?php echo number_format($ajax['tees'][3]['ranking'],2) ?>" /><br />
        <b>Slope</b><br />
        <input type="text" name="ajax[tees][3][slope]" value="<?php echo number_format($ajax['tees'][3]['slope'],0) ?>" /><br />
        <hr />
      </td>
    </tr>
    <tr>
      <td>
        <h3>Other</h3>
        <b>Rating</b><br />
        <input type="hidden" name="ajax[tees][4][color]" value="other" />
        <input type="text" name="ajax[tees][4][ranking]" value="<?php echo number_format($ajax['tees'][4]['ranking'],2) ?>" /><br />
        <b>Slope</b><br />
        <input type="text" name="ajax[tees][4][slope]" value="<?php echo number_format($ajax['tees'][4]['slope'],0) ?>" /><br />
        <hr />
      </td>
      <td>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <h3>Par In</h3>
      <?php for ($p=1;$p<10;$p++) { ?>
        <input type="text" size="1" name="ajax[par][<?php echo $p ?>]" value="<?php echo number_format($ajax['par'][$p],0) ?>" />
      <?php } ?>
        <h3>Par Out</h3>
      <?php for ($p=10;$p<19;$p++) { ?>
        <input type="text" size="1" name="ajax[par][<?php echo $p ?>]" value="<?php echo number_format($ajax['par'][$p],0) ?>" />
      <?php } ?>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <br />
        <input type="hidden" name="ajax[save]" value="1" />
        <input type="submit" value="Save" /> <span class="message"></span>
      </td>
    </td>
  </table>
</form>
<?php $layout = ob_get_clean();