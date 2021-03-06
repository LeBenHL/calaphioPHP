<?php
/**
 * This file is intended as a kluge to keep the chapter running until I have
 * time to create a real dynamic requirements tracker. -Geoffrey Lee
 */
require("include/includes.php");
require("include/Calendar.class.php");
require("include/Template.class.php");
Template::print_head(array("requirements.css", "ggwiki.css", "profile.css"));
Template::print_body_header('Home', 'PROFILES');
 ca

?>

<script language="javascript" type="text/javascript" src="popup.js"></script>
<script type="text/javascript" src="ajax.js"></script>
<script type="text/javascript" src="short_search.js"></script>
<form method="get" action="" onsubmit="return false">
User Search: <input id="apo_short_search_input" type="text" />
</form>
<div id="apo_short_search_result"></div>
</br>
<?php

/**
 *
 */
function process_attendance($attended, $flaked, $chair)
{
	if (!$attended && !$flaked && $chair) {
		return "Chairing";
	} else if (!$attended && !$flaked && !$chair) {
		return "Signed Up";
	} else if ($flaked) {
		return "Flaked!";
	} else if ($attended && $chair) {
		return "Chaired";
	} else if ($attended) {
		return "Attended";
	} else {
		trigger_error("Woops, something happened behind the scenes that wasn't expected. Please contact the webmaster!", E_USER_ERROR);
		return "";
	}
}

/**
 *
 */
function event_link($event_id, $title)
{
	$popup_width = CALENDAR_POPUP_WIDTH;
	$popup_height = CALENDAR_POPUP_HEIGHT;
	$session_id = session_id(); // JavaScript popups in IE tend to block cookies, so need to explicitly set session id
	return "<a href=\"event.php?id=$event_id&sid=$session_id\" onclick=\"return popup('event.php?id=$event_id&sid=$session_id', $popup_width, $popup_height)\">$title</a>";
}

// Best Function Ever
function auto_link($text) {
	$pattern = "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,3})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
	$text = preg_replace($pattern, "<a href='$1'>$1</a>", $text);
	$text = preg_replace("/href='www\./", "href='http://www\.", $text);
	$pattern = "/((<a href='(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,3})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})'>)(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,3})?)\/?[a-z0-9.,_\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})(<\/a>)( &quot;([\w\W ]+)&quot;))/is";
    $orig_text = $text;
    $count = 14;
    while ($count < strlen($orig_text)) {
        $text = preg_replace($pattern, "$2$19$17", substr($orig_text, 0, $count));
        $orig_text = $text . substr($orig_text, $count);
        $count = strlen($text);
        if ($count + 1 == strlen($orig_text)) {
            break;
        } else {
            $count = $count + 14;
            if ($count >= strlen($orig_text)) {
                $count = strlen($orig_text) - 1;
            }
        }
    }
	return $orig_text;
}

// Second Best Function Ever
function auto_youtube($text) {
	$pattern = "/http[s]?:\/\/(?:[a-zA_Z]{2,3}.)?(?:youtube\.com\/watch\?)((?:[\w\d\-\_\=]+&amp;(?:amp;)?)*v(?:&lt;[A-Z]+&gt;)?=([0-9a-zA-Z\-\_]+))/i";
	preg_match($pattern, $text, $matches);
	if($matches[2]) {
		$ret = "<br /><br /><iframe width=\"560\" height=\"315\" src=\"http://www.youtube.com/embed/" . $matches[2] . "\" frameborder=\"0\" allowfullscreen></iframe>";
	}
	return $ret;
}

function info_maker_helper($key, $value) {
	$info = "<tr>";
	$info .= "<td class=\"key\">";
	$info .= $key;
	$info .= "</td>";
	$info .= "<td class=\"value\">";
	$info .= $value;
	$info .= "</td>";
	$info .= "</tr>";
	return $info;
}

function basic_info($user_id) {
	$info = "";
	global $g_user;
	if ($g_user->is_logged_in()) {
		$query = new Query(sprintf("SELECT * FROM apo_users WHERE user_id=%d and depledged=0 LIMIT 1", $user_id));
		$row = $query->fetch_row();
		$info .= "<table class='basic-info'>";
		$info .= info_maker_helper("Email", $row['email']);
		$info .= info_maker_helper("Cellphone", $row['cellphone']);
		$info .= info_maker_helper("Major", $row['major']);
		$info .= info_maker_helper("Address", $row['address']);
		$info .= info_maker_helper("Shirt Size", $row['shirtsize']);
		$info .= info_maker_helper("Birthday", $row['birthday']);
        $info .= info_maker_helper("Dynasty", $row['dynasty']);
		$info .= "</table>";
	}
	return $info;
}

function print_profile($user_id) {
	global $g_user;
    $hide_family = $g_user->hide_family($user_id);
	$content .= "<div class=\"position\">";
	$content .= "<div class=\"section\">";
	$content .= "<h2 class=\"title\">";
	$content .= Positions;
	$content .= "</h2>";
	$query = new Query(sprintf("SELECT * FROM apo_wiki_positions as pos, apo_wiki_positions_basic_info as bas WHERE user_id=%d and pos.basic_info_id=bas.basic_info_id ORDER BY bas.year ASC, bas.semester ASC, pos.position_type ASC", $user_id));
	while ($row = $query->fetch_row()) {
		$position_type = $row['position_type'];
		$position_title = $row['position_title'];
		$position_name = $row['position_name'];
		$semester = $row['semester'] == 0 ? "Spring" : "Fall";
		if ($semester_year != $semester . " " . $row['year']) {
		  $semester_year = $semester . " " . $row['year'];
		  if ($subcontent != "") {
		    $subcontent .= "</p>";
		    $subcontent .= "</div>";	
		  }
		  $subcontent .= "<div class=\"subsection\">";
		  $sem_query = new Query(sprintf("SELECT * FROM apo_wiki_semesters WHERE semester=%d and year=%d", $row['semester'], $row['year']));
		  if ($sem_row = $sem_query->fetch_row()) {
			$ns = $sem_row['namesake_short'];
		  }
		  else {
			$ns = "Unknown Namesake";
		  }
		  $page_query = new Query(sprintf("SELECT * FROM apo_wiki_pages WHERE page_name LIKE '%s'", ("%(" . $ns. ")" . " Semester%")));
		  if ($page_row = $page_query->fetch_row()) {
			$page_id = $page_row['page_id'];
			$subcontent .= sprintf("<a href =\"ggwiki.php?page_id=%d#home\">", $page_id);
			$subcontent .= "<h2 class =\"subtitle\">";
			$subcontent .= $ns . " Semester (" . $semester_year  . ")";
		  	$subcontent .= "</h2>";
		  	$subcontent .= "</a>";
		  }
		  else {
		  	$subcontent .= "<h2 class =\"subtitle\">";
			$subcontent .= $ns . " Semester (" . $semester_year  . ")";
		  	$subcontent .= "</h2>";
		  }

		  $subcontent .= "<p class=\"description\">";
		  $array = array();
		}
		if ($position_type == 1 || $position_type == 2 || $position_type == 3 || $position_type == 4 || $position_type == 7 || $position_type == 8) {
		  	if ($position_type == 3 || $position_type == 4) {
				$sentence = "<b>" . $position_name . "</b>: " . $position_title . "<br />";
			}
			else {
				$sentence = "<b>" . $position_title . "</b><br />";
			}
		}
		elseif ($position_type == 6 || $position_type == 11 || $position_type == 12 || $position_type == 13) {
		  	if ($position_type == 11) {
				$sentence = $position_title . " for " . $position_name . " (Small Family)<br />";
                if ($hide_family) {
                    $sentence = "";
                }
			}
		  	elseif ($position_type == 12) {
				$sentence = $position_title . " for " . $position_name . " (Big Family)<br />"; 
                if ($hide_family) {
                    $sentence = "";
                }
			}
			else {
				$sentence = $position_title . " for " . $position_name . "<br />"; 
			}
		}
		elseif ($position_type == 5) {
		  $sentence = "<b>Chairing</b>: " . $position_title . " for " . $position_name . "<br />";
		}
		elseif ( $position_type == 9 || $position_type == 10 ) {
		  $sentence = "<b>" . $position_name . " Recipient</b> for " . $position_title . "<br />";
		}
		elseif ( $position_type == 14) {
		  $sentence = $position_title . "<br />";
		}
		if (!in_array($sentence, $array)) {
		  $subcontent .= $sentence;
		  array_push($array, $sentence);
		}
	}
	if ($subcontent != "") {
	  $subcontent .= "</p>";
	  $subcontent .= "</div>";	
	}
	$content .= $subcontent;
	$content .= "</div>";
	$content .= "</div>";

	$query = new Query(sprintf("SELECT * FROM apo_wiki_user_description WHERE user_id=%d", $user_id));
	$row = $query->fetch_row();
	$description = $row['description'];
	$youtube = auto_youtube($description);
	$description = auto_link($description);
	$description .= $youtube;
	$about_me .= "<div class=\"about-me\">";
	$about_me .= "<div class=\"section\">";
	$about_me .= "<h2 class=\"title\">";
	$about_me .= "About Me";
	$about_me .= "</h2>";
	if ($g_user->data['user_id'] == $user_id) {
		$about_me .= "<p class=\"description\"><button href=\"ggwiki_edit.php?function=edit_main_human&user_id=$user_id\" class=\"edit\" onclick=\"return popup('ggwiki_edit.php?function=edit_main_human&user_id=$user_id', 550, 560)\" resize=\"none\">Edit About Me</button></p>";
	}
	$about_me .= "<div class=\"subsection\">";
	$about_me .= $description;
	$about_me .= "</div>";
	$about_me .= "</div>";
	$about_me .= "</div>";

	echo <<<HEREDOC
		<div class="profile-content">
		$about_me
		$content
		</div>
HEREDOC;
}

function profile_header($user_id) {
	global $g_user;
	$query = new Query(sprintf("SELECT * FROM apo_users WHERE user_id=%d and depledged=0 LIMIT 1", $user_id));
	$row = $query->fetch_row();
	$name = $row['firstname'] . " " . $row['lastname'] . " (". $row['pledgeclass'] . ")";
	$query = new Query(sprintf("SELECT * FROM apo_wiki_user_description WHERE user_id=%d", $user_id));
	$row = $query->fetch_row();
	$description = $row['description'];
	$img_name = "face/" . $user_id;
	if (file_exists($img_name . ".jpg")) {
		$img_name = $img_name . ".jpg";
	}
	elseif (file_exists($img_name . ".png")) {
		$img_name = $img_name . ".png";
	}
	else {
		$img_name = "face/default.jpg";
	}
	$basic_info = basic_info($user_id);

	if ($g_user->data['user_id'] == $user_id) {
		$edit_profile = "<a href=\"edit_roster.php\"><strong>Edit Account Info</strong></a>";
	} else {
		$edit_profile = "";
	}

	echo <<<HEREDOC
		<div class="profile-header">
			<div class="profile-picture left">
				<a class="pic" href="$img_name">
				<img src="$img_name" alt="$name">
				</a>
			</div>

			<div class="profile-info">
				<h1>$name</h1> $edit_profile
				<p>$basic_info</p>
			</div>
		</div>
HEREDOC;
}

function print_upcoming_events($user_id) {
	global $g_user;
	if ($g_user->is_logged_in()) {
		Calendar::print_upcoming_events_for($user_id, 5);
	}
}

function print_requirements($user_id) {
	global $g_user;
	if ($g_user->is_logged_in() && $g_user->data['user_id'] == $user_id) {
		// Find out if user is a pledge
		$query = new Query(sprintf("SELECT user_id FROM %spledges WHERE user_id=%d LIMIT 1", TABLE_PREFIX, $g_user->data['user_id']));
		if ($row = $query->fetch_row()) {
			$is_pledge = true;
		} else {
			$is_pledge = false;
		}
		
		$is_active = !$is_pledge;
		if (isset($_REQUEST['semester'])) {
			$query = new Query(sprintf("SELECT start, end, id FROM apo_semesters WHERE semester='%s'", $_REQUEST['semester']));
			$row = $query->fetch_row();
            $sem_id = $row['id'];
			$start_date = strtotime($row['start']);
			$end_date = strtotime($row['end']);	
			$sql_start_date = date("Y-m-d", $start_date);
			$sql_end_date = date("Y-m-d", $end_date);
            $sem = $_REQUEST['semester'];
		} else {
			$query = new Query(sprintf("SELECT semester, start, end, id FROM apo_semesters ORDER BY end DESC LIMIT 1"));
			$row = $query->fetch_row();
            $sem_id = $row['id'];
			$start_date = strtotime($row['start']);
			$end_date = strtotime($row['end']);
			$sql_start_date = date("Y-m-d", $start_date);
			$sql_end_date = date("Y-m-d", $end_date);
            $sem = $row['semester'];
		}
		$user_id = $g_user->data['user_id'];
        
        // Retrieve Service events
        $service_events = "";
        $service_hours = 0;
        $service_hours_flake = 0;
        $query = new Query(sprintf("SELECT %scalendar_event.*, title, date, attended, flaked, chair, hours, driver FROM %scalendar_event
            JOIN %scalendar_attend USING (event_id)
            WHERE (type_service_chapter=TRUE OR type_service_campus=TRUE OR type_service_community=TRUE OR type_service_country=TRUE OR type_fundraiser=TRUE) AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
            TABLE_PREFIX, TABLE_PREFIX,
            TABLE_PREFIX,
            $sql_start_date, $sql_end_date, $user_id));
        while ($row = $query->fetch_row()) {
            $date = date("M d", strtotime($row['date']));
            //if ($row['driver']) {
            //	$row['hours'] += 1; // 1 service hour for driving
            //}
            $hours = $row['hours'] ? $row['hours'] . ' hrs' : '';
            if ($hours == '') {
                if ($row['time_allday']) {
                    $hours = "All Day";
                } else if ($row['time_start'] == "01:00:00" && $row['time_end'] == "01:00:00") {
                    $hours = "TBA";
                } else if ($row['time_start'] && $row['time_end']) {
                    $hours = sprintf("%s to %s", date("g:ia", strtotime($row['time_start'])), date("g:ia", strtotime($row['time_end'])));
                } else if ($row['time_start']) {
                    $hours = date("g:ia", strtotime($row['time_start']));
                } else {
                    $hours = "TBA";
                }
            }
            if ($row['flaked']) {
                $hours = "-" . $hours;
            }
            $attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
            $title_link = event_link($row['event_id'], $row['title']);
            $service_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\">$hours</td></tr>\r\n";
            if ($row['attended'] && is_numeric($row['hours'])) {
                $service_hours += $row['hours'];
            } else if ($row['flaked'] && is_numeric($row['hours'])) {
                $service_hours_flake += $row['hours'];
            }
        }
        $service_hours_total = $service_hours - $service_hours_flake;

        // Retrieve Service type Chapter
        $service_type_chapter = "";
        $service_type_chapter_hours = 0;
        $query = new Query(sprintf("SELECT %scalendar_event.*, title, date, attended, flaked, chair, hours FROM %scalendar_event
            JOIN %scalendar_attend USING (event_id)
            WHERE type_service_chapter=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
            TABLE_PREFIX, TABLE_PREFIX,
            TABLE_PREFIX,
            $sql_start_date, $sql_end_date, $user_id));
        while ($row = $query->fetch_row()) {
            $date = date("M d", strtotime($row['date']));
            //if ($row['driver']) {
            //	$row['hours'] += 1; // 1 service hour for driving
            //}
            $hours = $row['hours'] ? $row['hours'] . ' hrs' : '';
            if ($hours == '') {
                if ($row['time_allday']) {
                    $hours = "All Day";
                } else if ($row['time_start'] == "01:00:00" && $row['time_end'] == "01:00:00") {
                    $hours = "(TBA)";
                } else if ($row['time_start'] && $row['time_end']) {
                    $start = strtotime($row['time_start']);
                    $end = strtotime($row['time_end']);
                    $hours = sprintf("(%.1f hrs)", ($end - $start)/60.0/60.0);
                } else if ($row['time_start']) {
                    $hours = "(TBA)";
                } else {
                    $hours = "(TBA)";
                }
            }
            if ($row['flaked']) {
                $hours = "-" . $hours;
            }
            $attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
            $title_link = event_link($row['event_id'], $row['title']);
            $service_type_chapter .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\">$hours</td></tr>\r\n";
            if ($row['attended'] && is_numeric($row['hours'])) {
                $service_type_chapter_hours += $row['hours'];
            } else if ($row['flaked'] && is_numeric($row['hours'])) {
                $service_type_chapter_hours -= $row['hours'];
            }
        }

        // Retrieve Service type Campus
        $service_type_campus = "";
        $service_type_campus_hours = 0;
        $query = new Query(sprintf("SELECT %scalendar_event.*, title, date, attended, flaked, chair, hours FROM %scalendar_event
            JOIN %scalendar_attend USING (event_id)
            WHERE type_service_campus=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
            TABLE_PREFIX, TABLE_PREFIX,
            TABLE_PREFIX,
            $sql_start_date, $sql_end_date, $user_id));
        while ($row = $query->fetch_row()) {
            $date = date("M d", strtotime($row['date']));
            //if ($row['driver']) {
            //	$row['hours'] += 1; // 1 service hour for driving
            //}
            $hours = $row['hours'] ? $row['hours'] . ' hrs' : '';
            if ($row['flaked']) {
                $hours = "-" . $hours;
            }
            if ($hours == '') {
                if ($row['time_allday']) {
                    $hours = "All Day";
                } else if ($row['time_start'] == "01:00:00" && $row['time_end'] == "01:00:00") {
                    $hours = "(TBA)";
                } else if ($row['time_start'] && $row['time_end']) {
                    $start = strtotime($row['time_start']);
                    $end = strtotime($row['time_end']);
                    $hours = sprintf("(%.1f hrs)", ($end - $start)/60.0/60.0);
                } else if ($row['time_start']) {
                    $hours = "(TBA)";
                } else {
                    $hours = "(TBA)";
                }
            }
            $attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
            $title_link = event_link($row['event_id'], $row['title']);
            $service_type_campus .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\">$hours</td></tr>\r\n";
            if ($row['attended'] && is_numeric($row['hours'])) {
                $service_type_campus_hours += $row['hours'];
            } else if ($row['flaked'] && is_numeric($row['hours'])) {
                $service_type_campus_hours -= $row['hours'];
            }
        }

        // Retrieve Service type Community
        $service_type_community = "";
        $service_type_community_hours = 0;
        $query = new Query(sprintf("SELECT %scalendar_event.*, title, date, attended, flaked, chair, hours FROM %scalendar_event
            JOIN %scalendar_attend USING (event_id)
            WHERE type_service_community=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
            TABLE_PREFIX, TABLE_PREFIX,
            TABLE_PREFIX,
            $sql_start_date, $sql_end_date, $user_id));
        while ($row = $query->fetch_row()) {
            $date = date("M d", strtotime($row['date']));
            //if ($row['driver']) {
            //	$row['hours'] += 1; // 1 service hour for driving
            //}
            $hours = $row['hours'] ? $row['hours'] . ' hrs' : '';
            if ($hours == '') {
                if ($row['time_allday']) {
                    $hours = "All Day";
                } else if ($row['time_start'] == "01:00:00" && $row['time_end'] == "01:00:00") {
                    $hours = "(TBA)";
                } else if ($row['time_start'] && $row['time_end']) {
                    $start = strtotime($row['time_start']);
                    $end = strtotime($row['time_end']);
                    $hours = sprintf("(%.1f hrs)", ($end - $start)/60.0/60.0);
                } else if ($row['time_start']) {
                    $hours = "(TBA)";
                } else {
                    $hours = "(TBA)";
                }
            }
            if ($row['flaked']) {
                $hours = "-" . $hours;
            }
            $attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
            $title_link = event_link($row['event_id'], $row['title']);
            $service_type_community .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\">$hours</td></tr>\r\n";
            if ($row['attended'] && is_numeric($row['hours'])) {
                $service_type_community_hours += $row['hours'];
            } else if ($row['flaked'] && is_numeric($row['hours'])) {
                $service_type_community_hours -= $row['hours'];
            }
        }

        // Retrieve Service type Country
        $service_type_country = "";
        $service_type_country_hours = 0;
        $query = new Query(sprintf("SELECT %scalendar_event.*, title, date, attended, flaked, chair, hours FROM %scalendar_event
            JOIN %scalendar_attend USING (event_id)
            WHERE type_service_country=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
            TABLE_PREFIX, TABLE_PREFIX,
            TABLE_PREFIX,
            $sql_start_date, $sql_end_date, $user_id));
        while ($row = $query->fetch_row()) {
            $date = date("M d", strtotime($row['date']));
            //if ($row['driver']) {
            //	$row['hours'] += 1; // 1 service hour for driving
            //}
            $hours = $row['hours'] ? $row['hours'] . ' hrs' : '';
            if ($hours == '') {
                if ($row['time_allday']) {
                    $hours = "All Day";
                } else if ($row['time_start'] == "01:00:00" && $row['time_end'] == "01:00:00") {
                    $hours = "(TBA)";
                } else if ($row['time_start'] && $row['time_end']) {
                    $start = strtotime($row['time_start']);
                    $end = strtotime($row['time_end']);
                    $hours = sprintf("(%.1f hrs)", ($end - $start)/60.0/60.0);
                } else if ($row['time_start']) {
                    $hours = "(TBA)";
                } else {
                    $hours = "(TBA)";
                }
            }
            if ($row['flaked']) {
                $hours = "-" . $hours;
            }
            $attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
            $title_link = event_link($row['event_id'], $row['title']);
            $service_type_country .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\">$hours</td></tr>\r\n";
            if ($row['attended'] && is_numeric($row['hours'])) {
                $service_type_country_hours += $row['hours'];
            } else if ($row['flaked'] && is_numeric($row['hours'])) {
                $service_type_country_hours -= $row['hours'];
            }
        }

        // Retrieve Service type count
        $service_type_count = 0;
        if ($service_type_chapter_hours > 0) {
            $service_type_count++;
        }
        if ($service_type_campus_hours > 0) {
            $service_type_count++;
        }
        if ($service_type_community_hours > 0) {
            $service_type_count++;
        }
        if ($service_type_country_hours > 0) {
            $service_type_count++;
        }

        // Retrieve Fellowship events
        $fellowship_events = "";
        $fellowship_events_count = 0;
        $fellowship_events_flaked = 0;
        $query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
            JOIN %scalendar_attend USING (event_id)
            WHERE type_fellowship=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
            TABLE_PREFIX, TABLE_PREFIX,
            TABLE_PREFIX,
            $sql_start_date, $sql_end_date, $user_id));
        while ($row = $query->fetch_row()) {
            $date = date("M d", strtotime($row['date']));
            $attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
            $title_link = event_link($row['event_id'], $row['title']);
            $fellowship_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
            if ($row['attended']) {
                $fellowship_events_count++;
            } else if ($row['flaked']) {
                $fellowship_events_flaked++;
            }
        }
        $fellowship_events_total = $fellowship_events_count - $fellowship_events_flaked;

		if ($is_active) {
			// Retrieve IC events
			$ic_events = "";
			$ic_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_interchapter=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$ic_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$ic_events_count++;
				}
			}
			// Retrieve Dynasty events
			$dynasty_events = "";
			$dynasty_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_interchapter_half=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);

				$dynasty_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$dynasty_events_count++;
				}
			}
			
			
			// Retrieve Fundraiser events
			$fundraiser_events = "";
			$fundraiser_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_fundraiser=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$fundraiser_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$fundraiser_events_count++;
				}
			}
			
			// Retrieve Election events
			$election_events = "";
			$election_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='Elections')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$election_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$election_events_count++;
				}
			}
			
			// Retrieve Tabling hours
			$tabling_events = "";
			$tabling_hours = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair, hours FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='Tabling')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$hours = $row['hours'] ? $row['hours'] . ' hrs' : '';
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$tabling_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\">$hours</td></tr>\r\n";
				if ($row['attended']) {
					$tabling_hours += $row['hours'];
				} else if ($row['flaked']) {
					$tabling_hours -= $row['hours'];
				}
			}
			
			// Retrieve Rush events
			$rush_events = "";
			$rush_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_rush=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$rush_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$rush_events_count++;
				}
			}

			// Retrieve Sponsorship events
			$sponsorship_events = "";
			$sponsorship_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair, hours FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='Sponsorship Event')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$sponsorship_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$sponsorship_events_count++;
				} else if ($row['flaked']) {
					$sponsorship_events_count--;
				}
			}

			
			

			// Retrieve Chapter events
			$chapter_events = "";
			$chapter_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name IN ('Ritual', 'Activation', 'Chapter Forum'))
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$chapter_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$chapter_events_count++;
				}
			}
			
			// Retrieve Chapter Meeting events
			$chaptermeeting_events = "";
			$chaptermeeting_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_active_meeting=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$chaptermeeting_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$chaptermeeting_events_count++;
				}
			}
			
			// Retrieve Active events
			$active_events = "";
			$active_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_custom=12 AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$active_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$active_events_count++;
				} else if ($row['flaked']) {
					$active_events_count--;
				}
			}

			// Retrieve Interfam events
			$interfam_events = "";
			$interfam_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='Interfam')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$interfam_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$interfam_events_count++;
				}
			}

			$query = new Query(sprintf("SELECT semester FROM apo_semesters ORDER BY end DESC"));
			while ($row = $query->fetch_row()) {
				$semester = $row['semester'];
				if ($semester == $_REQUEST['semester']) {
					$semesters = $semesters . '<option class="" value="' . $semester . '" selected="selected">' . $semester . '</option>';
				} else {
					$semesters = $semesters . '<option class="" value="' . $semester . '" >' . $semester . '</option>';
				}
			}
            
            // Retrieve Coffee Chats
            $queryCoffee = new Query("SELECT cm, attended FROM apo_coffee_chats WHERE user_id=$user_id AND semester=$sem_id");
            $coffeeCount = 0;
            while($row = $queryCoffee->fetch_row()) {
                $coffeeCount += $row['attended'];
                $cm = $row['cm'];
                $attended = $row['attended'] ? "Attended" : "Didn't Attend";
                $coffee .=  "<tr><td axis='title'>CM $cm</td><td axis='attended'>$attended</td></tr>";
            }
            
            // Retrieve Leadershp Credits
            $leadership_events = "";
            $leadership_count = 0;
            $sem_arr = explode(' ', $sem);
            $query = new Query(sprintf("SELECT position_title, apo_wiki_positions.position_type as type FROM apo_wiki_positions JOIN apo_wiki_positions_basic_info USING (basic_info_id) WHERE user_id=%d AND semester='%s' AND year=%d AND (apo_wiki_positions.position_type=5 OR apo_wiki_positions.position_type=11) ORDER BY apo_wiki_positions.ordering ASC", $user_id, $sem_arr[0]=="Spring" ? 0 : 1, $sem_arr[1]));
            while ($row = $query->fetch_row()) {
                $title = $row['position_title'];
                $type = $row['type'];
                if ($type!=11 || !($title=="Unofficial" || $title=="Little")){
                    if($type == 5){
                        // Chairing
                        $credits = 1;
                        $prefix = "Chairing: ";
                    } elseif($type == 11 and $title == 'Big'){
                        // Bigging
                        $credits = 2;
                        $prefix = "Family: ";
                    } else{
                        //uncle/aunt
                        $credits = 1;
                        $prefix = "Family: ";
                    }
                    $leadership_events .= "<tr><td axis='title'>$prefix $title</td><td axis='credits'>$credits</td></tr>";
                    $leadership_events_count+=$credits;
                }
			}
            // credits from stuff put in by admin
            $query = new Query(sprintf("SELECT num_credits, reason FROM apo_leadership_credits WHERE user_id=%d AND date BETWEEN '%s' AND '%s'", $user_id, $sql_start_date, $sql_end_date));
            while ($row = $query->fetch_row()) {
                $num_credits = $row['num_credits'];
                $reason = $row['reason'];
                $leadership_events .= "<tr><td axis='title'>$reason</td><td axis='credits'>$num_credits</td></tr>";
                $leadership_events_count+=$num_credits;
			}
            // leadershp credit from chairing events
            $query = new Query(sprintf("SELECT count(*) as chairs FROM apo_calendar_attend JOIN apo_calendar_event USING (event_id) WHERE user_id=%u AND chair=1 AND date BETWEEN '%s' AND '%s'", $user_id, $sql_start_date, $sql_end_date));
            $row = $query->fetch_row();
            $chairing_times = $row['chairs'];
            $credits = floor($chairing_times / 2);
            $leadership_events .= "<tr><td axis='title'>Signed up to chair $chairing_times events</td><td axis='credits'>$credits</td></tr>";
            $leadership_events_count+=$credits;
		
            $chairing_link = $GLOBALS['excomm committees'];
            
			echo <<<DOCHERE
<div style="margin:1em 0em">
	<form action="#" method="post" onsubmit="">
		<span style="font-weight:bold;margin-right:1em"> Semester </span>
		<select id="semester" name="semester" style="padding:3 12 6 2;">$semesters</select>
		<input class="btn btn-primary btn-small" type="submit" value="View">
	</form>
</div>	
<div id="requirements">
<table>
<caption>Complete 20 Service Hours - You have completed $service_hours_total hours (Attended $service_hours, Flaked $service_hours_flake)<br>
</caption>
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Chapter</caption>
$service_type_chapter
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Campus</caption>
$service_type_campus
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Community</caption>
$service_type_community
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Country</caption>
$service_type_country
</table>
<table>
<caption>Attend 5 Fellowships - You have completed {$fellowship_events_total} (Attended $fellowship_events_count, Flaked $fellowship_events_flaked)</caption>
$fellowship_events
</table>
<table>
<caption>Attend 1 Fundraiser - You have completed $fundraiser_events_count</caption>
$fundraiser_events
</table>
<table>
<caption>Complete 1 IC Credit (Attend 1 InterChapter Event) - You have completed $ic_events_count</caption>
$ic_events
</table>
<table>
<caption>Attend 5 of 8 Chapter Meetings - You have completed $chaptermeeting_events_count</caption>
$chaptermeeting_events
</table>
<table>
<caption>Attend 1 of 1 Elections (2, if necessary) - You have completed $election_events_count</caption>
$election_events
</table>
<table>
<caption>Attend 1 Info Night, Meet the Chapter, 3 Rush Events, and complete 4 Hours of Rush Flyering - You have completed $rush_events_count</caption>
$rush_events
</table>
<table>
<caption>Attend 2 Dynasty Events - You have attended $dynasty_events_count</caption>
$dynasty_events
</table>
<table width="100%">
<caption>Chair 2 Positions - You can get credit for this by doing any of the following:</caption>
<tr><td axis="name">Serving on ExComm (counts as 2 positions)</td></tr>
<tr><td axis="name">Serving on PComm (counts as 2 positions)</td></tr>
<tr><td axis="name">Serving on DComm (counts as 2 positions)</td></tr>
<tr><td axis="name">Being a Big -- this excludes Aunts, Uncles, and Unofficials (counts as 1 position)</td></tr>
</table>
<table>
<caption>Other Active Requirements:</caption>
<tr><td axis="name">Pay Active Dues</td></tr>
<tr><td axis="name">Attend 2 of 4 Chapter Events (Ritual, Activation, Mid-Semester Forum, End-of-Semester Forum)</td></tr>
<tr><td axis="name">Chair 1 Service Event</td></tr>
<tr><td axis="name">Chair 1 Fellowship</td></tr>
<tr><td axis="name">Attend 1 Rush Workshop</td></tr>
</table>
</div>

DOCHERE;
		} else if ($is_pledge) {
			// Retrieve IC events
			$ic_events = "";
			$ic_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_interchapter=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$ic_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$ic_events_count++;
				}
			}
			
			// Retrieve Fundraiser events
			$fundraiser_events = "";
			$fundraiser_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_fundraiser=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$fundraiser_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$fundraiser_events_count++;
				}
			}
			
			// Retrieve Election events
			$election_events = "";
			$election_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='Elections')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$election_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$election_events_count++;
				}
			}
			
			// Retrieve Interfam events
			$interfam_events = "";
			$interfam_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='Interfam')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$interfam_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$interfam_events_count++;
				}
			}
			
			// Retrieve ExComm Meeting events
			$excomm_events = "";
			$excomm_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				JOIN %scalendar_event_type_custom ON (type_id=type_custom AND type_name='ExComm Meeting')
				WHERE deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$excomm_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$excomm_events_count++;
				}
			}
			
			// Retrieve Chapter Meeting events
			$chaptermeeting_events = "";
			$chaptermeeting_events_count = 0;
			$query = new Query(sprintf("SELECT %scalendar_event.event_id, title, date, attended, flaked, chair FROM %scalendar_event
				JOIN %scalendar_attend USING (event_id)
				WHERE type_active_meeting=TRUE AND deleted=FALSE AND date BETWEEN '%s' AND '%s' AND user_id=%d ORDER BY date ASC",
				TABLE_PREFIX, TABLE_PREFIX,
				TABLE_PREFIX,
				$sql_start_date, $sql_end_date, $user_id));
			while ($row = $query->fetch_row()) {
				$date = date("M d", strtotime($row['date']));
				$attendance = process_attendance($row['attended'], $row['flaked'], $row['chair']);
				$title_link = event_link($row['event_id'], $row['title']);
				$chaptermeeting_events .= "<tr><td class=\"date\" axis=\"date\">$date</td><td axis=\"title\">$title_link</td><td class=\"attendance\" axis=\"attendance\">$attendance</td><td class=\"hours\" axis=\"hours\"></td></tr>\r\n";
				if ($row['attended']) {
					$chaptermeeting_events_count++;
				}
			}
		
			echo <<<DOCHERE
<div id="requirements">
<table>
<caption>Complete 20 Service Hours - You have completed $service_hours_total hours (Attended $service_hours, Flaked $service_hours_flake)<br>
</caption>
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Chapter</caption>
$service_type_chapter
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Campus</caption>
$service_type_campus
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Community</caption>
$service_type_community
</table>
<table>
<caption style="background-color: #FFFFFF;border: none;text-decoration: underline;">Service to the Country</caption>
$service_type_country
</table>
<table>
<caption>Attend 5 Fellowships - You have completed {$fellowship_events_total} (Attended $fellowship_events_count, Flaked $fellowship_events_flaked)</caption>
$fellowship_events
</table>
<table>
<caption>Attend 4 of 5 Chapter Meetings - You have completed $chaptermeeting_events_count</caption>
$chaptermeeting_events
</table>
<table>
<caption>Attend 1 of 1 Elections (2, if necessary) - You have completed $election_events_count</caption>
$election_events
</table>
<table>
<caption>Attend 1 Fundraiser - You have completed $fundraiser_events_count</caption>
$fundraiser_events
</table>
<table>
<caption>Complete 1 IC Credit (Attend 1 InterChapter Event) - You have completed $ic_events_count</caption>
$ic_events
</table>
<table>
<caption>Attend 1 ExComm Meeting - You have completed $excomm_events_count</caption>
$excomm_events
</table>
<table>
<caption>Other Pledge Requirements:</caption>
<tr><td axis="name">Attend Ritual</td></tr>
<tr><td axis="name">Pay $90 Pledge Dues</td></tr>
<tr><td axis="name">Wear Pledge Pin at all times (except the 5 S's)</td></tr>
<tr><td axis="name">Attend 5 of 5 Pledge Reviews (PRs)</td></tr>
<tr><td axis="name">Pass 4 of 4 Pledge Review Quizzes</td></tr>
<tr><td axis="name">Attend Sib Social</td></tr>
<tr><td axis="name">Attend Pledge Class Retreat (PCR)</td></tr>
<tr><td axis="name">Attend Pledge Class Service Project I (PCSP I)</td></tr>
<tr><td axis="name">Attend Pledge Class Fellowship</td></tr>
<tr><td axis="name">Attend Pledge Class Service Project II (PCSP II)</td></tr>
<tr><td axis="name">Attend Campout</td></tr>
<tr><td axis="name">Attend Talent Show/Mr. & Mrs. APO</td></tr>
<tr><td axis="name">Attend Judgement Night</td></tr>
<tr><td axis="name">Attend 1 Dynasty Event</td></tr>
<tr><td axis="name">Attend 1 Alumni Event</td></tr>
<tr><td axis="name">Attend 1 Pledge Class Bonding (PCB)</td></tr>
<tr><td axis="name">Attend 2 Calendar Events with your Family before PR2</td></tr>
<tr><td axis="name">Attend Mid-Semester Forum</td></tr>
<tr><td axis="name">Join an ExComm Committee</td></tr>
<tr><td axis="name">Attend 1 of each PComm Office Hours (11 total)</td></tr>
<tr><td axis="name">Complete 25 Interviews</td></tr>
<tr><td axis="name">Complete 3 Chumming Assignments</td></tr>
<tr><td axis="name">Complete 4 Reflections</td></tr>
<tr><td axis="name">Complete Committee Requirements</td></tr>
<tr><td axis="name">Complete Signature Sheets</td></tr>
<tr><td axis="name">Complete Pledge Binder</td></tr>
<tr><td axis="name">Pass Pledge Test</td></tr>
<tr><td axis="name">Attend Activation</td></tr>
</table>
</div>

DOCHERE;
		} else {
			// User is neither an active or a pledge
		}
	} else {
	}
}
?>

<?php
if (isset($_REQUEST['user_id']) && is_numeric($_REQUEST['user_id'])) {
	$user_id = $_REQUEST['user_id'];
	$is_human = false;
	$query = new Query(sprintf("SELECT * FROM apo_users WHERE user_id=%d and depledged=0 LIMIT 1", $user_id));
	$row = $query->fetch_row();
	if (!$row) {
		trigger_error("This User does not exist.", E_USER_ERROR);
	} else {
		echo <<<DOCHERE
		<div class="profile-left">
DOCHERE;
		profile_header($user_id);
		echo <<<DOCHERE
		<div class="main-profile">
DOCHERE;
		if ($g_user->data['user_id'] == $user_id) {
			echo <<<DOCHERE
			<ul class="nav nav-tabs" id="profileTabs">
			  <li><a href="#profile" data-toggle="tab"><strong>Profile</strong></a></li>
			  <li><a href="#profile-requirements" data-toggle="tab"><strong>My Requirements</strong></a></li>
			</ul>
DOCHERE;
		 
		 	echo <<<DOCHERE
			<div class="tab-content">
			  <div class="tab-pane active" id="profile">
DOCHERE;
			print_profile($user_id);
			echo <<<DOCHERE
			  </div>
			  <div class="tab-pane" id="profile-requirements">
DOCHERE;
			print_requirements($user_id);
			echo <<<DOCHERE
				</div>
			</div>
DOCHERE;

			$requirements = $_REQUEST['requirements'];
			if ($requirements == "true") {
				echo <<<DOCHERE
				<script>
					$('#profileTabs a[href="#profile-requirements"]').tab('show');
				</script>
DOCHERE;
			} else {
				echo <<<DOCHERE
				<script>
					$('#profileTabs a[href="#profile"]').tab('show');
				</script>
DOCHERE;
			}
		} else {
			print_profile($user_id);
		}
		echo <<<DOCHERE
			</div>
		</div>
		<div class="profile-right">
DOCHERE;
		print_upcoming_events($user_id);
		echo <<<DOCHERE
		</div>
DOCHERE;

	}
} else {
	trigger_error("No User Specified", E_USER_ERROR);	
}
?>

<script>
		$('.profile-picture').photobox('a.pic',{ thumbs:true });
	</script>
<?php
Template::print_body_footer();
Template::print_disclaimer();
?>
