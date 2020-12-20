<?php

/**
 *
 * @param unknown_type $atts
 * @return string
 */
function iwcCoursesListHtmlPage($theme, $cats, $item_per_page, $show_filter_bar) {
    $themes_dir = get_template_directory();
    $btport_theme = $themes_dir . '/iw_courses/';
    $themes = '';
    if (file_exists($btport_theme) && is_dir($btport_theme)) {
        $themes = $btport_theme;
    } else {
        $themes = WP_PLUGIN_DIR . '/iw_courses/themes/' . $theme;
    }
    $iwc_theme = $themes . '/list_courses.php';
    if (file_exists($iwc_theme)) {
        require $iwc_theme;
    } else {
        echo __('No theme was found','inwavethemes');
    }
}

function iwcTeacherListHtmlPage($theme, $item_per_page, $show_paging, $page) {
    $themes_dir = get_template_directory();
    $btport_theme = $themes_dir . '/iw_courses/';
    $themes = 'athlete';
    if (file_exists($btport_theme) && is_dir($btport_theme)) {
        $themes = $btport_theme;
    } else {
        $themes = WP_PLUGIN_DIR . '/iw_courses/themes/' . $theme;
    }
    $iwc_theme = $themes . '/list_teachers.php';
    if (file_exists($iwc_theme)) {
        require $iwc_theme;
    } else {
        echo __('No theme was found','inwavethemes');
    }
}

function iwcAjaxVote() {
    require_once 'utility.php';
    global $wpdb;
    $result = array();
    $result['success'] = true;
    $utility = new iwcUtility();
    $bt_options = get_option('iw_courses_settings');
    $user = get_current_user_id();
    if ($user == 0 && $utility->getCoursesOption('allow_guest_vote', 0) == 0) {
        $result['success'] = false;
        $result['message'] = __('Only registered users can vote. Please login to cast your vote','inwavethemes');
    } else {
        $postid = $_POST['id'];
        $rating = $_POST['rating'];

        $sqlQuery = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "posts WHERE id=%d", $postid);
        $post = $wpdb->get_row($sqlQuery);

        // Fake submit
        if (!$post || $rating == 0 || $rating > 5) {
            die();
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($user) {
            $sqlQuery = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "iw_courses_vote WHERE item_id=%d AND user_id=%d", $postid, $user);
        } else {
            $sqlQuery = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "iw_courses_vote WHERE item_id=%d AND ip=%s AND user_id = 0", $postid, $ip);
        }
        if ($wpdb->get_var($sqlQuery) > 0) {
            $result['success'] = false;
            $result['message'] = __('You have already voted for this item','inwavethemes');
        } else {
            $ins = $wpdb->insert($wpdb->prefix . "iw_courses_vote", array('item_id' => $postid, 'user_id' => $user, 'created' => time(), 'vote' => $rating, 'ip' => $ip));

            $result['message'] = __('Thanks for voting. You rock!!! ;o');
            $result['rating_sum'] = $media_item->vote_sum + $rating;
            $result['rating_count'] = $media_item->vote_count + 1;
            $result['rating'] = $result['rating_sum'] / $result['rating_count'];
            $result['rating_text'] = sprintf(__('%d votes'), $result['rating_count']);
            $result['rating_width'] = round(15 * $result['rating']);
        }
    }
    $utility->obEndClear();
    echo json_encode($result);
    exit();
}

add_action('wp_ajax_nopriv_iwcAjaxVote', 'iwcAjaxVote');
add_action('wp_ajax_iwcAjaxVote', 'iwcAjaxVote');

//Ajax vote for Athlete theme
function iwcAthleteAjaxVote() {
    require_once 'utility.php';
    global $wpdb;
    $result = array();
    $result['success'] = true;
    $utility = new iwcUtility();
    $user = get_current_user_id();
    if ($user == 0 && $utility->getCoursesOption('allow_guest_vote', 0) == 0) {
        $result['success'] = false;
        $result['message'] = __('Only registered users can vote. Please login to cast your vote','inwavethemes');
    } else {
        $postid = $_POST['id'];
        $rating = $_POST['rating'];

        $sqlQuery = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "posts WHERE id=%d", $postid);
        $post = $wpdb->get_row($sqlQuery);

        // Fake submit
        if (!$post || $rating == 0 || $rating > 5) {
            die();
        }
        $ip = $_SERVER['REMOTE_ADDR'];
        if ($user) {
            $sqlQuery = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "iw_courses_vote WHERE item_id=%d AND user_id=%d", $postid, $user);
        } else {
            $sqlQuery = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "iw_courses_vote WHERE item_id=%d AND ip=%s AND user_id = 0", $postid, $ip);
        }
        if ($wpdb->get_var($sqlQuery) > 0) {
            $result['success'] = false;
            $result['message'] = __('You have already voted for this item','inwavethemes');
        } else {
            $wpdb->insert($wpdb->prefix . "iw_courses_vote", array('item_id' => $postid, 'user_id' => $user, 'created' => time(), 'vote' => $rating, 'ip' => $ip));
            $vote = $wpdb->get_row($wpdb->prepare('SELECT count(id) AS vote_count, SUM(vote) as vote_sum FROM ' . $wpdb->prefix . 'iw_courses_vote WHERE item_id=%d', $postid));

            $result['message'] = __('Thanks for voting. You rock!!! ;o','inwavethemes');
            $result['rating_sum'] = $vote->vote_sum;
            $result['rating_count'] = $vote->vote_count;
            $result['rating'] = $result['rating_sum'] / $result['rating_count'];
            $result['rating_text'] = sprintf(__('%d votes','inwavethemes'), $result['rating_count']);
            $result['rating_width'] = round(20 * $result['rating']);
        }
    }
    $utility->obEndClear();
    echo json_encode($result);
    exit();
}

add_action('wp_ajax_nopriv_iwcAthleteAjaxVote', 'iwcAthleteAjaxVote');
add_action('wp_ajax_iwcAthleteAjaxVote', 'iwcAthleteAjaxVote');

//Ajax iwcSendMailTakeCourse
function iwcSendMailTakeCourse() {
    $result = array();
    $teacher = get_post($_POST['temail']);
    $result['success'] = false;
    $admin_email = get_option('admin_email');
    $teacher_email = get_post_meta($teacher->ID, 'iw_teacher_email', true);
    $email = $_POST['email'];
    $title = $_POST['title'] . ' [' . $email . ']';
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $message = $_POST['message'];

    if ($teacher_email) {
        $recived_email = $teacher_email;
        $recived_name = $teacher->post_title;
    } else {
        $recived_email = $admin_email;
        $recived_name = __('Administrator', 'inwavethemes');
    }

    $html = '
  ' . __('Email from "TAKE THIS COURSE" form:', 'inwavethemes') . '
  ' . __(sprintf('Hi %s', $recived_name), 'inwavethemes') . '
  ' . __('This email was sent from "TAKE THIS COURSE" form', 'inwavethemes') . '
  ' . __('Name', 'inwavethemes') . ': ' . $name . '
  ' . __('Email', 'inwavethemes') . ': ' . $email . '
  ' . __('Mobile', 'inwavethemes') . ': ' . $mobile . '
  ' . __('Message', 'inwavethemes') . ': 
  ' . $message . '
';
    add_filter('wp_mail_content_type', 'iwc_set_html_content_type');
    if (wp_mail($recived_email, $title, $html)) {
		if($teacher_email){
			wp_mail($admin_email, $title, $html);
		}
        $result['success'] = true;
        $result['message'] = __('Your message was sent, we will contact you soon','inwavethemes');
    } else {
        $result['message'] = __('Can\'t send message, please try again','inwavethemes');
    }
    remove_filter('wp_mail_content_type', 'iwc_set_html_content_type');
    echo json_encode($result);
    exit();
}

function iwc_set_html_content_type() {
    return 'text/plain';
}

add_action('wp_ajax_nopriv_iwcSendMailTakeCourse', 'iwcSendMailTakeCourse');
add_action('wp_ajax_iwcSendMailTakeCourse', 'iwcSendMailTakeCourse');

function iwcAjaxRenderImage() {
    require_once 'utility.php';
    $utility = new iwcUtility();
    $utility->getImageWatermark();
    exit();
}

add_action('wp_ajax_nopriv_iwcAjaxRenderImage', 'iwcAjaxRenderImage');
add_action('wp_ajax_iwcAjaxRenderImage', 'iwcAjaxRenderImage');

function iwcLoadCoursesPosts() {
    $exids = rtrim($_POST['excids'], ',');
    $keysearch = $_POST['keyword'];
    $result = array('success' => false, 'msg' => '', 'data' => '');
    global $wpdb;
    if ($exids) {
        $rs = $wpdb->get_results($wpdb->prepare('SELECT ID, post_title FROM ' . $wpdb->prefix . 'posts WHERE post_status = "publish" AND post_type="iw_courses" AND post_title like %s AND ID NOT IN(' . $exids . ')', $keysearch . '%'));
    } else {
        $rs = $wpdb->get_results($wpdb->prepare('SELECT ID, post_title FROM ' . $wpdb->prefix . 'posts WHERE post_status = "publish" AND post_type="iw_courses" AND post_title like %s', $keysearch . '%'));
    }
    if ($rs) {
        $html = array();
        $html[] = '<ul>';
        for ($index = 0; $index < count($rs); $index++) {
            if ($index == 0) {
                $html[] = '<li class="selected" data-id="' . $rs[$index]->ID . '" data-title="' . $rs[$index]->post_title . '">' . $rs[$index]->post_title . '</li>';
            } else {
                $html[] = '<li data-id="' . $rs[$index]->ID . '" data-title="' . $rs[$index]->post_title . '">' . $rs[$index]->post_title . '</li>';
            }
        }
        $html[] = '</ul>';
        $result['msg'] = __('Loaded post successfully', 'inwavethemes');
        $result['success'] = true;
        $result['data'] = implode($html);
    } else {
        $msg = $wpdb->last_error;
        if ($msg) {
            $result['msg'] = $msg;
        } else {
            $result['msg'] = __('No data was found', 'inwavethemes');
        }
    }

    echo json_encode($result);
    exit();
}
function iw_course_get_template_part( $template) {
    $parent_path = get_template_directory();
    $path = $parent_path . '/iw_courses/' . $template . '.php';
    if (get_stylesheet_directory() != get_template_directory()) {
        //Theme child active
        $child_path = get_stylesheet_directory();
        $file_path = $child_path . '/iw_courses/' . $template . '.php';
        if (file_exists($file_path)) {
            $path = $file_path;
        }
    }
    if (file_exists($path)) {
        include_once($path);
    } else {
        iw_course_get_default_template( $template);
    }
}
function iw_course_get_default_template($template){
    $bt_options = get_option('iw_courses_settings');
    if ($bt_options['theme']) {
        $theme = $bt_options['theme'];
    }else{
        $theme = 'athlete';
    }
    $template_path = WP_PLUGIN_DIR . '/iw_courses/themes/'.$theme.'/'.$template.'.php';
    if (file_exists($template_path)) {
        require_once $template_path;
    } else {
        echo __("No theme was found", "inwavethemes");
    }
}
