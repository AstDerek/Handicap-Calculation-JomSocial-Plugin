<?php

/*

Copyright (C) 2010  Ast Derek [Cesar Garcia] (ast.dere@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
http://creativecommons.org/licenses/by-sa/3.0/

*/

defined('_JEXEC') or die( 'Restricted access' );

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php');
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filter.filterinput' );

class plgCommunityHandicap extends CApplications {

  var $name = "Handicap";
  var $_name = "handicap";
  var $_user = "";
  var $_my = "";
  var $_avatar = "";
  var $_path = "";
  var $public_only = array(
    'my-stats',
    'my-scores',
    'my-courses',
    'dygraph',
  );
  var $self_only = array(
    'game-configuration',
    'add-new-round',
    'edit-score',
  );
  var $admin_only = array(
    'add-new-course',
    'edit-course',
  );
  var $dynamic_js = array(
    'add-new-round',
    'edit-score',
  );
  var $ranges = array(
    20 => 10,
    19 => 9,
    18 => 8,
    17 => 7,
    15 => 6,
    13 => 5,
    11 => 4,
    9 => 3,
    7 => 2,
    5 => 1,
  );
  var $pagination = 10;

  function plgCommunityHandicap(& $subject, $config) {
    $this->_user =& CFactory::getActiveProfile();
    $this->_my =& CFactory::getUser();
    $this->db = JFactory::getDBO();
    
    parent::__construct($subject, $config);
  }
  
  function _verifyTables () {
    if (!$this->db) {
      $this->db = JFactory::getDBO();
    }
    
    // Main table __handicap
    $query = 'SELECT * FROM #__community_handicap LIMIT 0,1';
    $this->db->setQuery($query);
    
    if (!$this->db->query()) {
      $query = 'CREATE TABLE `#__community_handicap` ('.
        '`id` INT(11) NOT NULL AUTO_INCREMENT,'.
        '`user_id` INT(11) NOT NULL DEFAULT "1",'.
        '`handicap` FLOAT,'.
        '`round_count` INT(11),'.
        '`score_avg` FLOAT,'.
        '`score_min` INT(11),'.
        '`score_max` INT(11),'.
        '`in_avg` FLOAT,'.
        '`out_avg` FLOAT,'.
        '`config` TEXT,'.
        '`timestamp` TIMESTAMP,'.
        'PRIMARY KEY  (id)'.
        ') ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
      $this->db->setQuery($query);
      $this->db->query();
    }
    
    // Per-user scores, serialized __handicap_rounds
    $query = 'SELECT * FROM #__community_handicap_rounds LIMIT 0,1';
    $this->db->setQuery($query);
    
    if (!$this->db->query()) {
      $query = 'CREATE TABLE `#__community_handicap_rounds` (' .
        '`id` INT(11) NOT NULL AUTO_INCREMENT,' .
        '`user_id` TEXT NOT NULL,' .
        '`course_id` INT(11),' .
        '`tee` INT(11),' .
        '`par` TEXT NOT NULL,' .
        '`score` TEXT NOT NULL,' .
        '`other` TEXT NOT NULL,' .
        '`par_total` INT(11),' .
        '`score_total` INT(11),' .
        '`score_in` INT(11),' .
        '`score_out` INT(11),' .
        '`differential` FLOAT,' .
        '`handicap` FLOAT,' .
        '`date` TIMESTAMP,' .
        '`timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,' .
        'PRIMARY KEY  (id)' .
        ') ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
      $this->db->setQuery($query);
      $this->db->query();
    }
    
    // Golf Courses __handicap_courses
    $query = 'SELECT * FROM #__community_handicap_courses LIMIT 0,1';
    $this->db->setQuery($query);
    
    if (!$this->db->query()) {
      $query = 'CREATE TABLE `#__community_handicap_courses` ('.
        '`id` INT(11) NOT NULL AUTO_INCREMENT,'.
        '`user_id` INT(11),'.
        '`name` TEXT NOT NULL,'.
        '`country_id` INT(11),'.
        '`location_id` INT(11),'.
        '`tees` TEXT,'.
        '`par` TEXT,'.
        '`public` BOOLEAN,'.
        '`timestamp` TIMESTAMP,'.
        'PRIMARY KEY  (id)'.
        ') ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
      $this->db->setQuery($query);
      $this->db->query();
    }
    
    // Countries & locations __handicap_locations
    $query = 'SELECT * FROM #__community_handicap_locations LIMIT 0,1';
    $this->db->setQuery($query);
    
    if (!$this->db->query()) {
      $query = 'CREATE TABLE `#__community_handicap_locations` ('.
        '`id` INT(11) NOT NULL AUTO_INCREMENT,'.
        '`user_id` TEXT NOT NULL,'.
        '`name` TEXT NOT NULL,'.
        '`country` BOOLEAN,'.
        '`timestamp` TIMESTAMP,'.
        'PRIMARY KEY  (id)'.
        ') ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;';
      $this->db->setQuery($query);
      $this->db->query();
    }
  }

  function onProfileDisplay() {
    $user = $this->_user;
    $my = $this->_my;
    
    $guest = $my->guest || is_null($my->password);
    $self = $my->id === $user->id;
    
    $displayName = $self ? 'My' : $user->getDisplayName()."'s";
    $thumbAvatar = $user->getThumbAvatar();
    $uid = $user->id;
    
    $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
    
    // CSS / JS LOAD
    $document       =& JFactory::getDocument();
    
    JHTML::_('behavior.calendar');
    $path = JURI::base() . 'plugins/community/handicap/';
    $this->_path = $path;
    
    $document->addStyleSheet($path . 'default.css');
    
    $document->addScript($protocol . '://platform.twitter.com/anywhere.js?id=' . 'VXH20MeBtgIG8K4UJtAhw' . '&v=1');
    //$document->addScript($protocol . '://connect.facebook.net/en_US/all.js?id=' . 'VXH20MeBtgIG8K4UJtAhw' . '&v=1');
    $document->addScript($path . 'handicap.js');
    $document->addScript($path . 'dygraph.combined.js');
    $document->addCustomTag('<!--[if IE]><script type="text/javascript" src="' . $path . 'excanvas.compiled.js"></script><![endif]-->');
    $document->addCustomTag('<script type="text/javascript">if (typeof(Handicap) !== "object"){Handicap = {};}Handicap.path = "' . $path . '";Handicap.base = "' . JURI::base() . '";Handicap.userid = ' . $user->id . ';Handicap.displayName = "' . $this->_hsc($displayName,true) . '";</script>');
    
    $this->db = JFactory::getDBO();
    $this->_verifyTables();
    
    include 'handicap' . DS . 'handicap.php';
    return $layout;
  }
  
  function ajaxHandicap ($response, $message, $uniqueId='', $cache_id='') {
    $my = CFactory::getUser();
    $user = CFactory::getRequestUser();
    
    $guest = $my->guest || is_null($my->password);
    $self = $my->id === $user->id;
    
    $path = JURI::base() . 'plugins'. DS .'community' . DS . 'handicap' . DS;
    
    parse_str($message,$parsed);
    $ajax = is_array($parsed['ajax']) ? $parsed['ajax'] : array();
    
    if ($message == 'calculateHandicap') {
      $messages = $this->_calculateHandicap($user->id);
      exit(print_r($messages,true));
    }
    
    if (
        in_array($parsed['section'],$this->public_only) ||
        ($self && in_array($parsed['section'],$this->self_only)) ||
        (($user->usertype == 'Super Administrator' || $user->usertype == 'Administrator') && in_array($parsed['section'],$this->admin_only))
    ) {
      @include_once 'handicap' . DS . "{$parsed[section]}.php";
      $layout = $layout ? $layout : "<p>The section <b>{$parsed[section]}</b> is temporarily unavaiable. Please try again in a few minutes</p>";
      
      if (in_array($parsed['section'],$this->dynamic_js)) {
        @include_once 'handicap' . DS . "{$parsed[section]}.js.php";
        $script = $script ? $script : '';
      }
      else {
        $script = @JFile::read("{$path}{$parsed[section]}.js");
      }
    }
    else {
      $layout = '<h3>Wrong or unauthorized section</h3>';
    }
    
    $response->addAssign('cWindowContent','innerHTML',$layout);
    if ($script) {
      $response->addScriptCall(preg_replace('/\n[ ]+/',"\n",$script));
    }
    
    return $response;
  }
  
  /* HTML functions/output */
  function _hsc ($string='',$return=false) {
    if ($return) {
      return htmlspecialchars($string,ENT_QUOTES);
    }
    
    echo htmlspecialchars($string,ENT_QUOTES);
  }
  
  function _select ($variable='',$value='') {
    if ($return) {
      return ($variable == $value) ? 'selected="selected"' : '';
    }
    
    echo ($variable == $value) ? 'selected="selected"' : '';
  }
  
  function _check ($variable='',$value='') {
    if ($return) {
      return ($variable == $value) ? 'checked="checked"' : '';
    }
    
    echo ($variable == $value) ? 'checked="checked"' : '';
  }
  /* HTML functions/output */
  
  /* Handicap calculations */
  function _calculateHandicap ($uid=-1) {
    $uid = JFilterInput::clean($uid,'int');
    
    if ($uid < 0) {
      return;
    }
    
    $handicap = 0;
    $errors = array();
    
    $this->db->setQuery('SELECT COUNT(*), AVG(score_total), AVG(score_in), AVG(score_out) FROM #__community_handicap_rounds WHERE user_id=' .$uid);
    list($rounds,$score,$score_in,$score_out) = $this->db->loadRow();
    $errors['list rounds'] = $this->db->getErrorMsg();
    
    $this->db->setQuery('SELECT MIN(score_total), MAX(score_total) FROM #__community_handicap_rounds WHERE user_id=' .$uid);
    list($score_min,$score_max) = $this->db->loadRow();
    $errors['list score minmax'] = $this->db->getErrorMsg();
    
    foreach ($this->ranges as $limit=>$count) {
      if ($rounds >= $limit) {
        // N best from M most recent
        //$this->db->setQuery('SELECT AVG(differential)*0.96 FROM #__community_handicap_rounds WHERE user_id=' . $uid . ' ORDER BY differential ASC LIMIT 0,' . $count);
        $this->db->setQuery('SELECT GROUP_CONCAT(id SEPARATOR ",") FROM #__community_handicap_rounds WHERE user_id=' . $uid . ' ORDER BY `date` DESC LIMIT 0,' . $limit);
        $ids = $this->db->loadResult();
        $this->db->setQuery('SELECT AVG(differential)*0.96 FROM #__community_handicap_rounds WHERE user_id=' . $uid . ' AND id IN (' . $ids . ')ORDER BY differential ASC LIMIT 0,' . $count);
        $handicap = $this->db->loadResult();
        $handicap = $handicap < 0 ? 0 : $handicap;
        
        $errors['handicap'] = $this->db->getErrorMsg();
        
        break;
      }
    }
    
    $rounds = $rounds ? $rounds : 0;
    $score = $score ? $score : 0;
    $score_in = $score_in ? $score_in : 0;
    $score_out = $score_out ? $score_out : 0;
    $score_min = $score_min ? $score_min : 0;
    $score_max = $score_max ? $score_max : 0;
    
    $this->db->setQuery('SELECT COUNT(*) FROM #__community_handicap WHERE user_id=' . $uid);
    if (!$this->db->loadResult()) {
      $errors['count registers'] = $this->db->getErrorMsg();
      
      $this->db->setQuery('INSERT INTO #__community_handicap (user_id,handicap,round_count,score_avg,score_min,score_max,in_avg,out_avg)' .
      ' VALUES (' .
      $uid . ',' .
      $handicap . ',' .
      $rounds . ',' .
      $score . ',' .
      $score_min . ',' .
      $score_max . ',' .
      $score_in . ',' .
      $score_out .
      ')');
      $this->db->query();
      $errors['insert details'] = $this->db->getErrorMsg();
    }
    else {
      $this->db->setQuery('UPDATE #__community_handicap SET ' .
      'handicap=' . $handicap . ',' .
      'round_count=' . $rounds . ',' .
      'score_avg=' . $score . ',' .
      'score_min=' . $score_min . ',' .
      'score_max=' . $score_max . ',' .
      'in_avg=' . $score_in . ',' .
      'out_avg=' . $score_out .
      ' WHERE user_id=' . $uid);
      $this->db->query();
      $errors['update details'] = $this->db->getErrorMsg();
    }
    
    $errors[] = array(
        'handicap' => $handicap,
        'rounds' => $rounds,
        'score' => $score,
        'score_min' => $score_min,
        'score_max' => $score_max,
        'score_in' => $score_in,
        'score_out' => $score_out,
        'uid' => $uid,
    );
    
    return $errors;
  }
  /* Handicap calculations */
  
  /* Value retrieval */
  function round_from_user ($uid,&$ajax) {
    $uid = JFilterInput::clean($uid,'int');
    $ajax['round_id'] = JFilterInput::clean($ajax['round_id'],'int');
    
    $errors = array();
    
    $this->db->setQuery('SELECT * FROM #__community_handicap_rounds WHERE user_id=' . $uid . ' AND id=' . $ajax['round_id']);
    $round = $this->db->loadAssoc();
    
    if ($round) {
      $ajax['course_id'] = $round['course_id'];
      
      $ajax['par'] = unserialize($round['par']);
      $ajax['score'] = unserialize($round['score']);
      $ajax['other'] = unserialize($round['other']);
      
      $ajax['par_total'] = $round['par_total'];
      $ajax['score_total'] = $round['score_total'];
      
      $ajax['score_in'] = $round['score_in'];
      $ajax['score_out'] = $round['score_out'];
      $ajax['differential'] = $round['differential'];
      $ajax['handicap'] = $round['handicap'];
      $ajax['round-date'] = JFilterInput::clean($round['date'],'string');
      
      $ajax['tee'] = JFilterInput::clean($round['tee'],'int');
      
      $this->_clean_num_array($ajax['par'],0);
      $this->_clean_num_array($ajax['score'],0);
      $this->_clean_num_array($ajax['other'],0);
    }
    else {
      $errors[] = 'No round for given ID (' . $ajax['round_id'] . ')';
    }
    
    return $errors;
  }
  
  function rounds_from_user ($uid,&$ajax) {
    $uid = JFilterInput::clean($uid,'int');
    $ajax = JFilterInput::clean($ajax,'array');
    
    $pagination = $this->parse_rounds_pagination($uid,$ajax);
    
    $this->db->setQuery('SELECT #__community_handicap_rounds.*, #__community_handicap_courses.name FROM #__community_handicap_rounds LEFT JOIN #__community_handicap_courses ON #__community_handicap_rounds.course_id = #__community_handicap_courses.id WHERE #__community_handicap_rounds.user_id=' . $uid . ' ORDER BY #__community_handicap_rounds.date DESC LIMIT ' . $ajax['page']*$this->pagination . ',' . $this->pagination);
    $rounds = $this->db->loadAssocList();
    
    return array($rounds,$pagination);
  }
  
  function course_from_user ($uid,&$ajax) {
    $uid = JFilterInput::clean($uid,'int');
    $ajax['course_id'] = JFilterInput::clean($ajax['course_id'],'int');
    
    $errors = array();
    
    if ($this->_user->usertype == 'Administrator' || $this->_user->usertype == 'Super Administrator') {
      $this->db->setQuery('SELECT * FROM #__community_handicap_courses WHERE id=' . $ajax['course_id']);
    }
    else {
      $this->db->setQuery('SELECT * FROM #__community_handicap_courses WHERE user_id=' . $uid . ' AND id=' . $ajax['course_id']);
    }
    
    $course = $this->db->loadAssoc();
    
    if ($course) {
      $ajax['name'] = JFilterInput::clean($course['name'],'string');
      
      $ajax['country_id'] = JFilterInput::clean($course['country_id'],'int');
      $ajax['location_id'] = JFilterInput::clean($course['location_id'],'int');
      
      $ajax['tees'] = unserialize($course['tees']);
      $ajax['par'] = unserialize($course['par']);
      
      $ajax['tees'] = JFilterInput::clean($ajax['tees'],'array');
      $this->_clean_num_array($ajax['par'],0);
    }
    else {
      $errors[] = 'No course for given ID (' . $ajax['round_id'] . ')';
    }
    
    return $errors;
  }
  
  function courses_from_user ($uid=null,&$ajax) {
    $uid = JFilterInput::clean($uid,'int');
    
    if ($ajax == -1) {
      $this->db->setQuery('SELECT * FROM #__community_handicap_courses WHERE user_id=' . $uid . ' OR public=1 ORDER BY `timestamp` DESC');
    }
    else {
      $pagination = $this->parse_courses_pagination($uid,$ajax);
      
      $this->db->setQuery('SELECT * FROM #__community_handicap_courses WHERE user_id=' . $uid . ' OR public=1 ORDER BY `timestamp` DESC LIMIT ' . $ajax['page']*$this->pagination . ',' . $this->pagination);
    }
    
    $courses = $this->db->loadAssocList();
    $courses = $courses ? $courses : array();
    
    foreach ($courses as &$course) {
      $course['tees'] = is_string($course['tees']) ? unserialize($course['tees']) : array();
      $course['par'] = is_string($course['par']) ? unserialize($course['par']) : array();
      
      $course['tees'] = is_array($course['tees']) ? $course['tees'] : array();
      $course['par'] = is_array($course['par']) ? $course['par'] : array();
    }
    
    if ($ajax == -1) {
      return $courses;
    }
    else {
      return array($courses,$pagination);
    }
  }
  
  function config_from_user ($uid=null) {
    
    $uid = JFilterInput::clean($uid,'int');
    
    if ($uid < 0) {
      return array(
        'rounds' => array(),
        'stats' => array(),
      );
    }
    
    $this->db->setQuery('SELECT config FROM #__community_handicap WHERE user_id=' . $uid);
    $config = $this->db->loadResult();
    $config = $config ? unserialize($config) : array();
    $config = is_array($config) ? $config : array();
    $config['rounds'] = is_array($config['rounds']) ? $config['rounds'] : array();
    $config['stats'] = is_array($config['stats']) ? $config['stats'] : array();
    
    return $config;
  }
  
  function list_countries () {
    $this->db->setQuery('SELECT * FROM #__community_handicap_locations WHERE country = TRUE');
    $countries = $this->db->loadAssocList('id');
    
    if ($countries) {
      return $countries;
    }
    
    return array();
  }
  
  function list_locations () {
    $this->db->setQuery('SELECT * FROM #__community_handicap_locations WHERE country = FALSE');
    $locations = $this->db->loadAssocList('id');
    
    if ($locations) {
      return $locations;
    }
    
    return array();
  }
  
  function list_places () {
    $this->db->setQuery('SELECT * FROM #__community_handicap_locations');
    $locations = $this->db->loadAssocList('id');
    
    if ($locations) {
      return $locations;
    }
    
    return array();
  }
  /* Value retrieval */
  
  /* Pagination */
  function parse_rounds_pagination ($uid=null,&$ajax) {
    $uid = JFilterInput::clean($uid,'int');
    $ajax = JFilterInput::clean($ajax,'array');
    $ajax['page'] = JFilterInput::clean($ajax['page'],'int');
    
    $this->db->setQuery('SELECT COUNT(*) FROM #__community_handicap_rounds WHERE user_id=' . $uid . ' ORDER BY #__community_handicap_rounds.date DESC');
    $total = $this->db->loadResult();
    
    $pages = ceil($total/$this->pagination);
    
    if (($ajax['page'] < 0) || ($ajax['page'] >= $pages)) {
      $ajax['page'] = 0;
    }
    
    $pagination = array();
    for ($n=0;($pages>1) && ($n<$pages);$n++) {
      $pagination[] = ($ajax['page'] == $n) ? '<strong>' . ($n+1) . '</strong>' : '<a href="#' . $n . '">' . ($n+1) . '</a>';
    }
    
    return $pagination;
  }
  
  function parse_courses_pagination ($uid=null,&$ajax) {
    $uid = JFilterInput::clean($uid,'int');
    $ajax = JFilterInput::clean($ajax,'array');
    $ajax['page'] = JFilterInput::clean($ajax['page'],'int');
    
    $this->db->setQuery('SELECT COUNT(*) FROM #__community_handicap_courses WHERE user_id=' . $uid . ' OR public=1');
    $total = $this->db->loadResult();
    
    $pages = ceil($total/$this->pagination);
    
    if (($ajax['page'] < 0) || ($ajax['page'] >= $pages)) {
      $ajax['page'] = 0;
    }
    
    $pagination = array();
    for ($n=0;($pages>1) && ($n<$pages);$n++) {
      $pagination[] = ($ajax['page'] == $n) ? '<strong>' . ($n+1) . '</strong>' : '<a href="#' . $n . '">' . ($n+1) . '</a>';
    }
    
    return $pagination;
  }
  /* Pagination */
  
  /* Value insertion */
  function save_game_configuration (&$ajax) {
    $config = array(
      'stats' => $ajax['stats'],
      'rounds' => $ajax['rounds'],
    );
    
    $this->db->setQuery('SELECT COUNT(*) FROM #__community_handicap WHERE user_id=' . $this->_user->id);
    if ($this->db->loadResult()) {
      $this->db->setQuery('UPDATE #__community_handicap SET config=' . $this->db->quote(serialize($config)) . ' WHERE user_id=' . $this->_user->id);
    }
    else {
      $this->db->setQuery('INSERT INTO #__community_handicap (user_id,config) VALUES (' . $this->_user->id . ',' . $this->db->quote(serialize($config)) . ')');
    }
    
    $this->db->query();
    
    return true;
  }
  
  function add_new_course (&$ajax) {
    $ajax['name'] = JFilterInput::clean($ajax['name'],'string');
    
    $ajax['location'] = JFilterInput::clean($ajax['location'],'string');
    $ajax['country'] = JFilterInput::clean($ajax['country'],'string');
    
    $this->_clean_num_array($ajax['par']);
    
    $errors = array();
    
    if (!trim($ajax['name']) || 
      !trim($ajax['location']) ||
      !trim($ajax['country'])
    ) {
      $errors[] = 'Please fill in all the fields before proceed. Please note that all the fields are required';
    }
    else {
      $this->db->setQuery('SELECT id FROM #__community_handicap_locations WHERE country = FALSE AND name LIKE = ' . $this->db->quote($ajax['location']));
      $ajax['location_id'] = $this->db->loadResult();
      
      if (is_null($ajax['location_id'])) {
        $this->db->setQuery('INSERT INTO #__community_handicap_locations (user_id,name,country) VALUES (' . $this->_user->id . ',' . $this->db->quote($ajax['location']) . ', FALSE)');
        $this->db->query();
        $ajax['location_id'] = $this->db->insertid();
      }
      
      $this->db->setQuery('SELECT id FROM #__community_handicap_locations WHERE country = TRUE AND name LIKE = ' . $this->db->quote($ajax['location']));
      $ajax['country_id'] = $this->db->loadResult();
      
      if (is_null($ajax['country_id'])) {
        $this->db->setQuery('INSERT INTO #__community_handicap_locations (user_id,name,country) VALUES (' . $this->_user->id . ',' . $this->db->quote($ajax['country']) . ', TRUE)');
        $this->db->query();
        $ajax['country_id'] = $this->db->insertid();
      }
      
      $this->db->setQuery('INSERT INTO #__community_handicap_courses (user_id,name,location_id,country_id,tees,par,public) VALUES (' .
        $this->_user->id . ',' .
        $this->db->quote($ajax['name']) . ',' .
        $ajax['location_id'] . ',' .
        $ajax['country_id'] . ',' .
        $this->db->quote(serialize($ajax['tees'])) . ',' .
        $this->db->quote(serialize($ajax['par'])) . ',' .
        ($this->_user->usertype == 'Super Administrator' || $this->_user->usertype == 'Administrator') . ')');
      $this->db->query();
      
      return true;
    }
    
    return $errors;
  }
  
  function add_new_round (&$ajax) {
    $this->_clean_num_array($ajax['par'],4);
    $this->_clean_num_array($ajax['score'],0);
    $ajax['other'] = JFilterInput::clean($ajax['other'],'array');
    
    $ajax['course_id'] = JFilterInput::clean($ajax['course_id'],'int');
    
    $ajax['ranking'] = JFilterInput::clean($ajax['ranking'],'float');
    $ajax['slope'] = JFilterInput::clean($ajax['slope'],'float');
    $ajax['tee'] = JFilterInput::clean($ajax['tee'],'string');
    
    $ajax['round-date'] = JFilterInput::clean($ajax['round-date'],'string');
    
    if ($ajax['slope'] < 1) {
      $ajax['slope'] = 1;
    }
    
    if (isset($ajax['score_in']) || isset($ajax['score_out']) || isset($ajax['par_total'])) {
      $ajax['score_in'] = JFilterInput::clean($ajax['score_in'],'int');
      $ajax['score_out'] = JFilterInput::clean($ajax['score_out'],'int');
      $ajax['par_total'] = JFilterInput::clean($ajax['par_total'],'int');
      
      $score_in = $ajax['score_in'];
      $score_out = $ajax['score_out'];
      
      $score_total = $score_in + $score_out;
      $par_total = $ajax['par_total'];
    }
    else {
      $score_in = array_sum(array_slice($ajax['score'],9,9));
      $score_out = array_sum(array_slice($ajax['score'],0,9));
      $score_total = array_sum($ajax['score']);
      $par_total = array_sum($ajax['par']);
    }
    
    $errors = array();
    
    $this->db->setQuery('SELECT COUNT(*) FROM #__community_handicap_courses WHERE id=' . $ajax['course_id']);
    if ($this->db->query()) {
      // Save values to DB
      $this->db->setQuery('SELECT handicap FROM #__community_handicap WHERE user_id=' . $this->_user->id);
      $handicap = $this->db->loadResult();
      
      $this->db->setQuery('INSERT INTO #__community_handicap_rounds ' .
        '(user_id,course_id,par,score,other,par_total,score_total,score_in,score_out,differential,handicap,tee,date)' .
        ' VALUES (' .
        $this->_user->id . ',' .
        $ajax['course_id'] . ',' .
        $this->db->quote(serialize($ajax['par'])) . ',' .
        $this->db->quote(serialize($ajax['score'])) . ',' .
        $this->db->quote(serialize($ajax['other'])) . ',' .
        $par_total . ',' .
        $score_total . ',' .
        $score_in . ',' .
        $score_out . ',' .
        ($score_total - $ajax['ranking'])*113/$ajax['slope'] . ',' .
        $handicap . ',' .
        $this->db->quote($ajax['tee']) . ',' .
        'TIMESTAMP(' . $this->db->quote($ajax['round-date']) . ')' .
        ')');
      $this->db->query();
      
      $this->_calculateHandicap($thid->_user->id);
      
      return true;
    }
    else {
      // Invalid course
      $errors[] = 'Please select a valid course field, or add a new one if none exists';
    }
    
    return $errors;
  }
  
  function edit_course (&$ajax) {
    $ajax['course_id'] = JFilterInput::clean($ajax['course_id'],'int');
    $ajax['name'] = JFilterInput::clean($ajax['name'],'string');
    
    $ajax['location'] = JFilterInput::clean($ajax['location'],'string');
    $ajax['country'] = JFilterInput::clean($ajax['country'],'string');
    
    $this->_clean_num_array($ajax['par']);
    
    $errors = array();
    
    if (!trim($ajax['name']) || 
      !trim($ajax['location']) ||
      !trim($ajax['country'])
    ) {
      $errors[] = 'Please fill in all the fields before proceed. Please note that all the fields are required';
    }
    else {
      $this->db->setQuery('SELECT id FROM #__community_handicap_locations WHERE country = FALSE AND name LIKE = ' . $this->db->quote($ajax['location']));
      $ajax['location_id'] = $this->db->loadResult();
      
      if (is_null($ajax['location_id'])) {
        $this->db->setQuery('INSERT INTO #__community_handicap_locations (user_id,name,country) VALUES (' . $this->_user->id . ',' . $this->db->quote($ajax['location']) . ', FALSE)');
        $this->db->query();
        $ajax['location_id'] = $this->db->insertid();
      }
      
      $this->db->setQuery('SELECT id FROM #__community_handicap_locations WHERE country = TRUE AND name LIKE = ' . $this->db->quote($ajax['location']));
      $ajax['country_id'] = $this->db->loadResult();
      
      if (is_null($ajax['country_id'])) {
        $this->db->setQuery('INSERT INTO #__community_handicap_locations (user_id,name,country) VALUES (' . $this->_user->id . ',' . $this->db->quote($ajax['country']) . ', TRUE)');
        $this->db->query();
        $ajax['country_id'] = $this->db->insertid();
      }
      
      $this->db->setQuery('UPDATE #__community_handicap_courses SET ' .
        'user_id=' . $this->_user->id . ',' .
        'name=' . $this->db->quote($ajax['name']) . ',' .
        'location_id=' . $ajax['location_id'] . ',' .
        'country_id=' . $ajax['country_id'] . ',' .
        'tees=' . $this->db->quote(serialize($ajax['tees'])) . ',' .
        'par=' . $this->db->quote(serialize($ajax['par'])) . ',' .
        'public=' . ($this->_user->usertype == 'Super Administrator' || $this->_user->usertype == 'Administrator') .
        ' WHERE id=' . $ajax['course_id']
        );
      $this->db->query();
      
      return true;
    }
    
    return $errors;
  }
  
  function edit_score (&$ajax) {
    $this->_clean_num_array($ajax['par'],4);
    $this->_clean_num_array($ajax['score'],0);
    $ajax['other'] = JFilterInput::clean($ajax['other'],'array');
    
    $ajax['round_id'] = JFilterInput::clean($ajax['round_id'],'int');
    $ajax['course_id'] = JFilterInput::clean($ajax['course_id'],'int');
    
    $ajax['ranking'] = JFilterInput::clean($ajax['ranking'],'float');
    $ajax['slope'] = JFilterInput::clean($ajax['slope'],'float');
    $ajax['tee'] = JFilterInput::clean($ajax['tee'],'string');
    
    $ajax['round-date'] = JFilterInput::clean($ajax['round-date'],'string');
    
    if ($ajax['slope'] < 1) {
      $ajax['slope'] = 1;
    }
    
    if (isset($ajax['score_in']) || isset($ajax['score_out']) || isset($ajax['par_total'])) {
      $ajax['score_in'] = JFilterInput::clean($ajax['score_in'],'int');
      $ajax['score_out'] = JFilterInput::clean($ajax['score_out'],'int');
      $ajax['par_total'] = JFilterInput::clean($ajax['par_total'],'int');
      
      $score_in = $ajax['score_in'];
      $score_out = $ajax['score_out'];
      
      $score_total = $score_in + $score_out;
      $par_total = $ajax['par_total'];
    }
    else {
      $score_in = array_sum(array_slice($ajax['score'],9,9));
      $score_out = array_sum(array_slice($ajax['score'],0,9));
      $score_total = array_sum($ajax['score']);
      $par_total = array_sum($ajax['par']);
    }
    
    $errors = array();
    
    $this->db->setQuery('SELECT COUNT(*) FROM #__community_handicap_courses WHERE id=' . $ajax['course_id']);
    if ($this->db->query()) {
      // Save values to DB
      $this->db->setQuery('SELECT handicap FROM #__community_handicap WHERE user_id=' . $this->_user->id);
      $handicap = $this->db->loadResult();
      
      $this->db->setQuery('UPDATE #__community_handicap_rounds SET ' .
        'course_id=' . $ajax['course_id'] . ',' .
        'par=' . $this->db->quote(serialize($ajax['par'])) . ',' .
        'score=' . $this->db->quote(serialize($ajax['score'])) . ',' .
        'other=' . $this->db->quote(serialize($ajax['other'])) . ',' .
        'par_total=' . $par_total . ',' .
        'score_total=' . $score_total . ',' .
        'score_in=' . $score_in . ',' .
        'score_out=' . $score_out . ',' .
        'differential=' . ($score_total - $ajax['ranking'])*113/$ajax['slope'] . ',' .
        'handicap=' . $handicap . ',' .
        'tee=' . $this->db->quote($ajax['tee']) . ',' .
        'date=' . 'TIMESTAMP(' . $this->db->quote($ajax['round-date']) . ')' .
        ' WHERE user_id=' . $this->_user->id . ' AND id=' . $ajax['round_id']);
      $this->db->query();
      
      $this->_calculateHandicap($thid->_user->id);
      
      return true;
    }
    else {
      // Invalid course
      $errors[] = 'Please select a valid course field, or add a new one if none exists';
    }
    
    return $errors;
  }
  /* Value insertion */
  
  /* Safe values */
  function _clean_num_array (&$array,$default=0,$max=18) {
    if (!is_array($array)) {
      $array = array();
    }
    
    $array = array_slice($array,0,$max,true);
    
    foreach ($array as $key=>&$value) {
      if (is_numeric($value) && is_float($default)) {
        $value = floatval($value);
      }
      else if (is_numeric($value) && is_int($default)) {
        $value = intval($value);
      }
      else if (gettype($value) !== gettype($default)) {
        $value = $default;
      }
    }
  }
  /* Safe values */
}