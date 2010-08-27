<?php defined('_JEXEC') or die('Restricted Access') ?>
<?php

header('Content-Type: text/csv');

if ($self) {
  $this->db->setQuery('SELECT DATE_FORMAT(`date`,"%Y%m%d") AS `date`,score_total,par_total,handicap FROM #__community_handicap_rounds WHERE user_id=' . $user->id . ' ORDER BY `timestamp`');
  $rounds = $this->db->loadAssocList();
  
  $this->db->setQuery('SELECT UNIX_TIMESTAMP(MAX(`timestamp`)) FROM #__community_handicap_rounds WHERE user_id=' . $user->id);
  $seconds = $this->db->loadResult();
  
  if (count($rounds) && $seconds) {
    header('Last-Modified: '.gmdate("D, d M Y H:i:s", $seconds)." GMT");
  }
  
  echo "Date,Score,Par,Handicap\n";
  foreach ($rounds as $round) {
    echo "{$round[date]},{$round[score_total]},{$round[par_total]},{$round[handicap]}\n";
  }
}
else {
  $this->db->setQuery('SELECT DATE_FORMAT(`date`,"%Y%m%d") AS `date`,score_total,par_total,handicap FROM #__community_handicap_rounds WHERE user_id=' . $user->id . ' ORDER BY `timestamp`');
  $rounds = $this->db->loadAssocList();
  
  $this->db->setQuery('SELECT UNIX_TIMESTAMP(MAX(`timestamp`)) FROM #__community_handicap_rounds WHERE user_id=' . $user->id);
  $seconds = $this->db->loadResult();
  
  if (count($rounds) && $seconds) {
    header('Last-Modified: '.gmdate("D, d M Y H:i:s", $seconds)." GMT");
  }
  
  echo "Date,Score,Par,Handicap\n";
  foreach ($rounds as $round) {
    echo "{$round[date]},{$round[score_total]},{$round[par_total]},{$round[handicap]}\n";
  }
}

exit();