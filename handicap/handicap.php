<?php defined('_JEXEC') or die( 'Restricted access' ); ?>
<?php

$this->db->setQuery('SELECT * FROM #__community_handicap WHERE user_id=' . $user->id);
$stats = $this->db->loadAssoc();
if (!$stats) {
  $stats = array(
    'handicap'=>0,
    'round_count'=>0,
    'score_avg'=>0,
    'in_avg'=>0,
    'out_avg'=>0,
  );
}

$this->db->setQuery('SELECT #__community_handicap_rounds.*, #__community_handicap_courses.name FROM #__community_handicap_rounds LEFT JOIN #__community_handicap_courses ON #__community_handicap_rounds.course_id = #__community_handicap_courses.id WHERE #__community_handicap_rounds.user_id=' . $user->id . ' ORDER BY #__community_handicap_rounds.date DESC LIMIT 0,5');
$rounds = $this->db->loadAssocList();

ob_start();

?>
  <div id="profile">
    <h2><?php $this->_hsc($displayName) ?> Game</h2>
  <?php if (count($rounds)) { ?>
    <div id="handicap-profile-r">
      <img id="handicap-avatar" src="<?php $this->_hsc($thumbAvatar) ?>" />
      <div class="clear"></div>
      <ul>
        <li>Handicap rating: <b id="hc-handicap-rating"><?php echo number_format($stats['handicap'],1,'.',',') ?></b></li>
        <li>#18-hole Rounds: <b><?php echo number_format($stats['round_count'],0) ?></b></li>
        <li>Average Score : <b id="hc-average-score"><?php echo number_format($stats['score_avg'],2,'.',',') ?></b></li>
        <li>Low Round: <b><?php echo number_format($stats['score_min'],0) ?></b></li>
        <li>High Round: <b><?php echo number_format($stats['score_max'],0) ?></b></li>
      </ul>
  <?php if (!$guest) { ?>
      <h2>Game panel</h2>
      <ul>
        <li><a id="hc-score" href="#"><?php $this->_hsc($displayName) ?> scores</a></li>
    <?php if ($self) { ?>
        <?php if (($user->usertype == 'Administrator') || ($user->usertype == 'Super Administrator')) {?><li><a id="hc-courses" href="#">Edit courses</a></li><?php } ?>
        <li><a id="hc-round" href="#">Add new round</a></li>
        <li><a id="hc-config" href="#">Game configuration</a></li>
    <?php } ?>
      </ul>
  <?php } ?>
    </div>
    
    <div id="handicap-recent-rounds"></div>
    
    <div class="clear"></div>
    
    <br />
    <table class="hc-table" style="width:100%;" cellspacing="1">
      <colgroup width="10%"></colgroup>
      <colgroup width="25%"></colgroup>
      <colgroup width="25%"></colgroup>
      <colgroup width="13%"></colgroup>
      <colgroup width="13%"></colgroup>
      <colgroup width="13%"></colgroup>
      <tr class="hc-table-title">
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
        <td class="hc-table-title"><?php echo $n ?></td>
        <td><?php echo date('Y-m-d',strtotime($round['date'])) ?></td>
        <td><?php $this->_hsc($round['name']) ?></td>
        <td><?php echo $round['score_total'] ?></td>
        <td><?php echo $round['par_total'] ?></td>
        <td><?php echo number_format($round['handicap'],1,'.',',') ?></td>
      </tr>
      <?php } ?>
    </table>
  <?php } else { ?>
    <div id="handicap-profile">
      <h1 id="handicap-name"><?php $this->_hsc($displayName) ?> Game</h1>
      <img id="handicap-avatar" src="<?php $this->_hsc($thumbAvatar) ?>" />
      <div id="handicap-stats">
        <ul>
          <li>Handicap rating: <b id="hc-handicap-rating"><?php echo number_format($stats['handicap'],1,'.',',') ?></b></li>
          <li>#18-hole Rounds: <b><?php echo number_format($stats['round_count'],0) ?></b></li>
          <li>Average Score : <b id="hc-average-score"><?php echo number_format($stats['score_avg'],2,'.',',') ?></b></li>
          <li>Low Round: <b><?php echo number_format($stats['in_avg'],0) ?></b></li>
          <li>High Round: <b><?php echo number_format($stats['out_avg'],0) ?></b></li>
        </ul>
      </div>
    </div>
<?php if (!$guest) { ?>
    <div id="handicap-menu">
      <h2>Game panel</h2>
      <ul>
        <li><a id="hc-score" href="#"><?php $this->_hsc($displayName) ?> scores</a></li>
    <?php if ($self) { ?>
        <?php if (($user->usertype == 'Administrator') || ($user->usertype == 'Super Administrator')) {?><li><a id="hc-courses" href="#">Edit courses</a></li><?php } ?>
        <li><a id="hc-round" href="#">Add new round</a></li>
        <li><a id="hc-config" href="#">Game configuration</a></li>
    <?php } ?>
      </ul>
    </div>
<?php } ?>
    <h3>There are no rounds to show</h3>
  <?php } ?>
<?php if ($guest) { ?>
    <p>Want to see all the stats from this user? <a href="#">create an account</a></p>
<?php } else if ($self) { ?>
<?php } else { ?>
<?php } ?>
    <p>
      <a id="hc-twitter" href="#">
        <img src="<?php $this->_hsc($this->_path) ?>twitter.ico" alt="Tweet this!" />
      </a>
    </p>
  </div>
<?php $layout = ob_get_clean();