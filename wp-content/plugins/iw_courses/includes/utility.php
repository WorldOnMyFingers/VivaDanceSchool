<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class iwcUtility {

    function __construct() {
        
    }

    function getMessage($message, $type = 'success') {
        $html = array();
        $class = 'success';
        if ($type == 'error') {
            $class = 'error';
        }
        if ($type == 'notice') {
            $class = 'notice';
        }
        $html[] = '<div class="bt-message ' . $class . '">';
        $html[] = '<div class="message-text">' . $message . '</div>';
        $html[] = '</div>';
        return implode($html);
    }

    function categoryField($value, $multiple = true) {
        $categories = get_terms('iw_courses_class', 'hide_empty=0');
        $html = array();
        $multiselect = '';
        if ($multiple) {
            $multiselect = 'multiple="multiple"';
            $html[] = '<select name="port_category[]" ' . $multiselect . '>';
            $html[] = '<option value="">' . __('Select all') . '</option>';
        } else {
            $html[] = '<select name="port_category">';
            $html[] = '<option value="">' . __('Select category') . '</option>';
        }
        foreach ($categories as $category) {
            if (is_array($value)) {
                if (in_array($category->term_id, $value)) {
                    $html[] = '<option value="' . $category->term_id . '" selected="selected">' . $category->name . '</option>';
                } else {
                    $html[] = '<option value="' . $category->term_id . '">' . $category->name . '</option>';
                }
            } else {
                $html[] = '<option value="' . $category->term_id . '" ' . (($category->term_id == $value) ? 'selected="selected"' : '') . '>' . $category->name . '</option>';
            }
        }
        $html[] = '</select>';

        return implode($html);
    }

    /**
     * Function create select option field
     * 
     * @param type $id
     * @param String $name Name of field
     * @param String $value The value field
     * @param Array $data list data option of field
     * @param String $text Default value of field
     * @param String $class Class of field
     * @param Bool $multi Field allow multiple select of not
     * @return String Select option field
     * 
     */
    function selectFieldRender($id, $name, $value, $data, $text = '', $class = '', $multi = true) {
        $html = array();
        $multiselect = '';
        //Kiem tra neu bien class ton tai thi them class vao field
        if ($class) {
            $class = 'class="' . $class . '"';
        }

        //Kiem tra neu field can tao cho phep multiple
        if ($multi) {
            $multiselect = 'multiple="multiple"';
            $html[] = '<select id="' . $id . '" ' . $class . ' name="' . $name . '[]" ' . $multiselect . '>';
            if ($text) {
                $html[] = '<option value="">' . __($text) . '</option>';
            }
        } else {
            $html[] = '<select ' . $class . ' name="' . $name . '" id="' . $id . '">';
            if ($text) {
                $html[] = '<option value="">' . __($text) . '</option>';
            }
        }

        //Duyet qua tung phan tu cua mang du lieu de tao option tuong ung
        foreach ($data as $option) {
            if (is_array($value)) {
                if (in_array($option['value'], $value)) {
                    $html[] = '<option value="' . $option['value'] . '" selected="selected">' . $option['text'] . '</option>';
                } else {
                    $html[] = '<option value="' . $option['value'] . '">' . __($option['text']) . '</option>';
                }
            } else {
                $html[] = '<option value="' . $option['value'] . '" ' . (($option['value'] == $value) ? 'selected="selected"' : '') . '>' . __($option['text']) . '</option>';
            }
        }
        $html[] = '</select>';

        return implode($html);
    }

    public function updatePostCommentStatus($status = false) {
        global $wpdb;
        if ($status) {
            $wpdb->update($wpdb->prefix . "posts", array('comment_status' => 'open'), array('post_type' => 'iw_courses'));
        } else {
            $wpdb->update($wpdb->prefix . "posts", array('comment_status' => 'closed'), array('post_type' => 'iw_courses'));
        }
    }

    public function obEndClear() {
        $obLevel = ob_get_level();
        while ($obLevel > 0) {
            ob_end_clean();
            $obLevel--;
        }
    }

    public function themeField($value = 'default') {
        $path = WP_PLUGIN_DIR . '/iw_courses/themes/';
        $dirs = array_filter(glob(WP_PLUGIN_DIR . '/iw_courses/themes/*'), 'is_dir');
        $html = array();
        $html[] = '<select name="theme" id="theme">';
        foreach ($dirs as $dir) {
            $theme = substr($dir, strrpos($dir, '/') + 1);
            $html[] = '<option value="' . $theme . '" ' . (($theme == $value) ? 'selected="selected"' : '') . '>' . $theme . '</option>';
        }
        $html[] = '</select>';

        return implode($html);
    }

    function getCoursesOptions() {
        $btOptions = get_option('iw_courses_settings');
        $options = new stdClass();
        foreach ($btOptions as $key => $option) {
            $options->$key = $option;
        }
        return $options;
    }

    function getCoursesOption($name, $defaultValue = '') {
        $btOptions = get_option('iw_courses_settings');
        $rs = $defaultValue;
        if (isset($btOptions[$name]) && $btOptions[$name]) {
            $rs = $btOptions[$name];
        }
        return $rs;
    }

    public function getRatingPanel($itemid, $rating_sum, $rating_count, $canRate = true, $showCount = true) {
        $width = 15;
        $height = 15;
        $numOfStar = 5;

        if ($rating_count == 0)
            $rating = 0;
        else
            $rating = ($rating_sum / $rating_count);

        $backgroundWidth = $numOfStar * $width;
        $currentWidth = round($rating * $width);

        $html = '<div class="btp-rating-container-' . $itemid . '"><div class="btp-rating-background" style="width: ' . $backgroundWidth . 'px"><div class="btp-rating-current" style="width: ' . $currentWidth . 'px"></div>';

        if ($canRate) {
            for ($i = $numOfStar; $i > 0; $i--) {
                $starWidth = $width * $i;
                $html .= '<a onclick="javascript:rate(' . $itemid . ', ' . $i . ')" href="javascript:void(0);" style="width:' . $starWidth . 'px"></a>';
            }
        }

        $html .= '</div>';
        if ($showCount) {
            $html .= '<div class="btp-rating-notice">' . sprintf(__('%0.1f/5 (%d votes)'), $rating, $rating_count) . '</div>';
        }

        $html .= '</div>';

        return $html;
    }

    public function getAthleteRatingPanel($itemid, $rating_sum, $rating_count, $canRate = true, $showCount = true) {
        $width = 20;
        $numOfStar = 5;

        if ($rating_count == 0)
            $rating = 0;
        else
            $rating = ($rating_sum / $rating_count);

        $currentWidth = round($rating * $width);

        $html = '<div class="btp-rating-container-' . $itemid . '">';
        if ($showCount) {
            $html .= '<div class="btp-rating-notice">' . sprintf(__('%d votes'), $rating_count) . '</div>';
        }
        $html .= '<div class="btp-rating-background" style="width: 63px"><div class="btp-rating-current" style="width: ' . $currentWidth . '%"></div>';

        if ($canRate) {
            for ($i = $numOfStar; $i > 0; $i--) {
                $starWidth = $width * $i;
                $html .= '<span onclick="javascript:rateAthlete(' . $itemid . ', ' . $i . ')" style="width:' . $starWidth . '%"></span>';
            }
        }

        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    public function getPathImage($imageType, $image_id) {
        $watermask = $this->getCoursesOption('enable_watemark', 0);
        $file_path = get_attached_file($image_id);
        if ($imageType == 'thumb' && $this->getCoursesOption('enable_watemark_thumbnail', 0) == 0) {
            $watermask = 0;
        }
        if (!$file_path) {
            if ($imageType == 'thumb') {
                return plugins_url('iw_courses/themes/' . $this->getCoursesOption('theme', 'default') . '/assets/images/photo_default.png');
            }
            if ($imageType == 'large') {
                return plugins_url('iw_courses/themes/' . $this->getCoursesOption('theme', 'default') . '/assets/images/photo_default.png');
            }
        }
        if ($watermask == 0) {
            if ($imageType == 'thumb') {
                if (!file_exists($file_path)) {
                    $imagePath = plugins_url('iw_courses/themes/' . $this->getCoursesOption('theme', 'default') . '/assets/images/photo_default.png');
                } else {
                    $image_thumb = wp_get_attachment_image_src($image_id, 'iw_courses-thumb');
                    $imagePath = $image_thumb[0];
                }
            }
            if ($imageType == 'large') {
                if (!file_exists($imageObj->guid)) {
                    $imagePath = plugins_url('iw_courses/themes/' . $this->getCoursesOption('theme', 'default') . '/assets/images/photo_default.png');
                } else {
                    $image_large = wp_get_attachment_image_src($image_id, 'iw_courses-large');
                    $imagePath = $image_large[0];
                }
            }
        } else {
            $imagePath = admin_url('admin-ajax.php?action=iwcAjaxRenderImage&src=' . $image_id . '&imagetype=' . $imageType);
        }

        return $imagePath;
    }

    public function getImageWatermark() {
        $file = $_GET['src'];
        $file_path = get_attached_file($file);
        $imgType = $_GET['imagetype'];
        $source = '';
        if ($imgType == 'large') {
            if (!$file_path) {
                $source = ABSPATH . 'wp-content/plugins/bt_portfoli/themes/' . $this->getCoursesOption('theme', 'default') . '/assets/images/photo_default.png';
            } else {
                $source = $file_path;
            }
        }
        if ($imgType == 'thumb') {
            if (!$file_path) {
                $source = ABSPATH . 'wp-content/plugins/bt_portfoli/themes/' . $this->getCoursesOption('theme', 'default') . '/assets/images/photo_default.png';
            } else {
                $source = $file_path;
            }
        }
        if ($this->getCoursesOption('enable_watemark', 0) == 1) {
            include(dirname(__FILE__) . '/watermask/watermask.php');
            $iwcWaterMask = new iwcWaterMask();
            $options = $iwcWaterMask->getWaterMarkOptions();
            $options['padding'] = $this->getCoursesOption('wm_padding', $options['padding']);
            $options['font'] = $this->getCoursesOption('wm_font') ? ABSPATH . 'wp-content/plugins/iw_courses/includes/watermask/fonts/' . $this->getCoursesOption('wm_font') . '.ttf' : $options['font'];
            $options['text'] = $this->getCoursesOption('wm_text', $options['text']);
            $options['image'] = $this->getCoursesOption('wm_image') ? JPATH_ROOT . '/' . $this->getCoursesOption('wm_image') : $options['image'];
            $options['type'] = $this->getCoursesOption('wm_type', $options['type']);
            $options['fcolor'] = $this->getCoursesOption('wm-fcolor', $options['fcolor']);
            $options['fsize'] = $this->getCoursesOption('wm-fsize', $options['fsize']);
            $options['bg'] = $this->getCoursesOption('wm_bg', $options['bg']);
            $options['bgcolor'] = $this->getCoursesOption('wm-bgcolor', $options['bgcolor']);
            $options['factor'] = $this->getCoursesOption('wm-factor', $options['factor']);
            $options['position'] = $this->getCoursesOption('wm_position', $options['position']);
            $options['opacity'] = $this->getCoursesOption('wm_opacity', $options['opacity']);
            $options['rotate'] = $this->getCoursesOption('wm_rotate', $options['rotate']);
            $iwcWaterMask->createWaterMark($source, $options);
        } else {
            $size = getimagesize($source);
            $imagetype = $size[2];
            switch ($imagetype) {
                case(1):
                    header('Content-type: image/gif');
                    $image = imagecreatefromgif($source);
                    imagegif($image);
                    break;

                case(2):
                    $image = imagecreatefromjpeg($source);
                    header('Content-type: image/jpeg');
                    imagejpeg($image);
                    break;

                case(3):
                    header('Content-type: image/png');
                    $image = imagecreatefrompng($source);
                    imagepng($image);
                    break;

                case(6):
                    header('Content-type: image/bmp');
                    $image = imagecreatefrombmp($source);
                    imagewbmp($image);
                    break;
            }
        }
        exit;
    }

    public function getSocialShare($social_buttons) {
        wp_enqueue_script('sharethis', 'http://w.sharethis.com/button/buttons.js');
        if (!is_array($social_buttons)) {
            $social_buttons = array($social_buttons);
        }
        foreach ($social_buttons as $button) {
            switch ($button) {
                case 1:
                    //echo "<span class='st_twitter_hcount' displayText='Tweet' st_via='YourTwitterHandleName' st_msg='#YourHashTag and #YourOtherHashTag'></span>";
                    echo "<span class='st_twitter_hcount' displayText='Tweet'></span> ";

                    break;
                case 2:
                    echo "<span class='st_plusone_hcount' displayText='Google +1'></span>";
                    break;
                case 3:
                    echo "<span class='st_linkedin_hcount' displayText='LinkedIn'></span>";
                    break;
                case 4:
                    echo "<span class='st_email_hcount' displayText='Email'></span>";
                    break;
                case 5:
                    echo "<span class='st_facebook_hcount' displayText='Facebook'></span>";
                    break;
                case 6:
                    echo "<span class='st_fbsend_hcount' displayText='Facebook Send'></span>";
                    break;
                case 7:
                    echo "<span class='st_fblike_hcount' displayText='Facebook Like'></span>";
                    break;
                case 8:
                    echo "<span class='st_fbrec_hcount' displayText='Facebook Recommend'></span>";
                    break;
                case 9:
                    echo "<span class='st_pinterest_hcount' displayText='Pinterest'></span>";
                    break;
            }
        }
    }

    public function callBackAllChild($id) {
        global $wpdb;
        $r = $wpdb->get_results($wpdb->prepare("SELECT term_id FROM " . $wpdb->prefix . "term_taxonomy WHERE parent = %d", $id));
        $ids = $id;
        foreach ($r as $i) {
            $ids .= "," . self::callBackAllChild($i->term_id);
        }
        return $ids;
    }

    // Function get list categories for mod_iw_courses_categories
    public function getListChildCategories($categoryId) {
        global $wpdb;
        $query = $wpdb->prepare('SELECT a.term_id, a.name, a.slug, b.parent, b.description FROM ' . $wpdb->prefix . 'terms as a INNER JOIN ' . $wpdb->prefix . 'term_taxonomy as b ON a.term_id = b.term_id WHERE b.parent = %d', $categoryId);
        return $wpdb->get_results($query, object);
    }

    public function courses_display_pagination($query = '') {
        if (!$query) {
            global $wp_query;
            $query = $wp_query;
        }

        $big = 999999999; // need an unlikely integer

        $paginate_links = paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'next_text' => '&raquo;',
            'prev_text' => '&laquo'
        ));
        // Display the pagination if more than one page is found
        if ($paginate_links) :
            ?>

            <div class="post-pagination clearfix">
                <?php echo $paginate_links; ?>
            </div>

            <?php
        endif;
    }

    public function courses_display_pagination_none($query = '', $page = 'page') {
        $rs = array('success' => false, 'data' => '');
            if (!$query) {
                global $wp_query;
                $query = $wp_query;
            }

            $link_args = array(
                'total' => $query->max_num_pages,
                'show_all' => true,
                'prev_text' => __('<'),
                'next_text' => __('>'));
            if ($page == 'page') {
                $link_args['format'] = '?paged=%#%';
                $link_args['current'] = max(1, get_query_var('paged'));
            } else {
                $link_args['format'] = '?page=%#%';
                $link_args['current'] = max(1, get_query_var('page'));
            }
            $paginate_links = paginate_links($link_args);
            // Display the pagination if more than one page is found
            if ($paginate_links) :
                $html = array();
                $html[] = '<div class="post-pagination clearfix" style="display: none;">';
                $html[] = $paginate_links;
                $html[] = '</div>';
                $rs['success'] = true;
                $rs['data'] = implode($html);
            endif;
            return $rs;
    }

    public function getSkitterScript() {
        $html = array();
        $html[] = '<script type="text/javascript" language="javascript">';
        $html[] = 'jQuery(document).ready(function () {';
        $html[] = 'jQuery(".box_skitter_large").skitter({';
        $html[] = "theme: 'clear',";
        $html[] = 'numbers: false,';
        $html[] = 'responsive: true,';
        $html[] = "thumbs: " . ($this->getCoursesOption('media_show_thumbnail', 0) == 1 ? 'true' : 'false') . ",";
        $html[] = 'animation: "' . $this->getCoursesOption('slideshow_skitter_effect', 'randomSmart') . '",';
        $html[] = 'interval: ' . $this->getCoursesOption('interval', '4000') . ',';
        $html[] = "navigation: " . ($this->getCoursesOption('next_button', 0) == 1 ? 'true' : 'false') . ",";
        $html[] = '});';
        $html[] = '});';
        $html[] = '</script>';
        return implode($html);
    }

    public function filterHtmlForm() {
        $html = array();
        $html[] = '<div class="p-filter">';
        $html[] = '<form action="" method="post">';
        $html[] = '<input class="p-first-input" placeholder="' . __('Enter keyword...') . '" type="text" name="keyword" value="' . $_SESSION['filter']['keyword'] . '"/>';
        $html[] = $this->categoryField($_SESSION['filter']['catid'], false);
        $order_data = array(
            array('value' => 'ID', 'text' => 'ID'),
            array('value' => 'post_title', 'text' => 'Title'),
            array('value' => 'post_comment', 'text' => 'Comment'),
            array('value' => 'rand', 'text' => 'Random')
        );
        $html[] = $this->selectFieldRender('port_order', $_SESSION['filter']['port_order'], $order_data, 'Order by', false);
        $direction_data = array(
            array('value' => 'ASC', 'text' => 'ASC'),
            array('value' => 'DESC', 'text' => 'DESC')
        );
        $html[] = $this->selectFieldRender('port_order_direction', ($_SESSION['filter']['port_order_direction']) ? $_SESSION['filter']['port_order_direction'] : 'ASC', $direction_data, 'Direction', false);
        $html[] = '<input type="submit" value="submit"/>';
        $html[] = '</form>';
        $html[] = '</div>';
        return implode($html);
    }

    public function getCoursesList($cats, $item_per_page, $page = 'page') {
        if ($page == 'page') {
            $paged = max(1, get_query_var('paged'));
        } else {
            $paged = max(1, get_query_var('page'));
        }
        $terms = '';
        $keyword = '';
        $order_by = '';
        $direction = '';
        $cat_array = explode(',', $cats);
        $new_cats = array();
        if (in_array('0', $cat_array)) {
            global $wpdb;
            $res = $wpdb->get_results("SELECT term_id FROM " . $wpdb->prefix . "term_taxonomy WHERE taxonomy='iw_courses_class'");
            foreach ($res as $value) {
                $new_cats[] = $value->term_id;
            }
        } else {
            $new_cats = $cat_array;
        }
        if (!empty($_POST)) {
            if ($_POST['keyword']) {
                $keyword = $_POST['keyword'];
                $_SESSION['filter']['keyword'] = $_POST['keyword'];
            } else {
                unset($_SESSION['filter']['keyword']);
            }
            if ($_POST['port_category']) {
                $terms = $_POST['port_category'];
                $_SESSION['filter']['catid'] = $_POST['port_category'];
            } else {
                unset($_SESSION['filter']['catid']);
            }
            if ($_POST['port_order']) {
                $order_by = $_POST['port_order'];
                $_SESSION['filter']['port_order'] = $_POST['port_order'];
            } else {
                unset($_SESSION['filter']['port_order']);
            }
            if ($_POST['port_order_direction']) {
                $direction = $_POST['port_order_direction'];
                $_SESSION['filter']['port_order_direction'] = $_POST['port_order_direction'];
            } else {
                unset($_SESSION['filter']['port_order_direction']);
            }
        }
        $args = array(
            'post_type' => 'iw_courses',
            's' => $keyword,
            'order' => ($direction) ? $direction : $this->getCoursesOption('port_order_direction', 'ASC'),
            'orderby' => ($order_by) ? $order_by : $this->getCoursesOption('port_order', 'ID'),
            'posts_per_page' => $item_per_page,
            'paged' => $paged
        );
        if ( $terms || $new_cats) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'iw_courses_class',
                    'terms' => $terms ? $terms : $new_cats,
                    'include_children' => false
                ),
            );
        }
        return new WP_Query($args);
    }

    public function getCoursesTeacherList($item_per_page, $page = 'page') {
        if ($page == 'page') {
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        } else {
            $paged = (get_query_var('page')) ? get_query_var('page') : 1;
        }
        $keyword = '';
        $order_by = '';
        $direction = '';
        if (!empty($_POST)) {
            if ($_POST['keyword']) {
                $keyword = $_POST['keyword'];
            }
            if ($_POST['port_category']) {
                $terms = $_POST['port_category'];
            }
            if ($_POST['port_order']) {
                $order_by = $_POST['port_order'];
            }
            if ($_POST['port_order_direction']) {
                $direction = $_POST['port_order_direction'];
            }
        }
        $args = array(
            'post_type' => 'iw_teacher',
            's' => $keyword,
            'order' => ($direction) ? $direction : $this->getCoursesOption('port_order_direction', 'ASC'),
            'orderby' => ($order_by) ? $order_by : $this->getCoursesOption('port_order', 'ID'),
            'posts_per_page' => $item_per_page,
            'paged' => $paged
        );
		$query = new WP_Query($args);
        return $query;
    }

    public function getCoursesByClass($item_per_page, $term_slug) {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $keyword = '';
        $order_by = '';
        $direction = '';
        if (!empty($_POST)) {
            if ($_POST['port_category']) {
                $terms = $_POST['port_category'];
            }
            if ($_POST['port_order']) {
                $order_by = $_POST['port_order'];
            }
            if ($_POST['port_order_direction']) {
                $direction = $_POST['port_order_direction'];
            }
        }
        $args = array(
            'post_type' => 'iw_courses',
            's' => $keyword,
            'order' => ($direction) ? $direction : $this->getCoursesOption('port_order_direction', 'ASC'),
            'orderby' => ($order_by) ? $order_by : $this->getCoursesOption('port_order', 'ID'),
            'posts_per_page' => $item_per_page,
            'tax_query' => array(
                array(
                    'taxonomy' => 'iw_courses_class',
                    'field' => 'slug',
                    'terms' => $term_slug,
                ),
            ),
            'paged' => $paged
        );
        return new WP_Query($args);
    }

    /**
     * Function truncate string by number of word
     * @param string $string
     * @param type $length
     * @param type $etc
     * @return string
     */
    public function truncateString($string, $length, $etc = '...') {
        $string = strip_tags($string);
        $text_array = explode(' ', $string);
        if (count($text_array) > $length) {
            $string_array = array_slice($text_array, 0, $length);
            $string = implode(' ', $string_array) . $etc;
        }
        return $string;
    }
    
    function getIWEventcurrencies() {
            return array(
                array('value' => 'AED', 'text' => __('United Arab Emirates dirham', 'inwavethemes')),
                array('value' => 'AFN', 'text' => __('Afghan afghani', 'inwavethemes')),
                array('value' => 'ALL', 'text' => __('Albanian lek', 'inwavethemes')),
                array('value' => 'AMD', 'text' => __('Armenian dram', 'inwavethemes')),
                array('value' => 'ANG', 'text' => __('Netherlands Antillean guilder', 'inwavethemes')),
                array('value' => 'AOA', 'text' => __('Angolan kwanza', 'inwavethemes')),
                array('value' => 'ARS', 'text' => __('Argentine peso', 'inwavethemes')),
                array('value' => 'AUD', 'text' => __('Australian dollar', 'inwavethemes')),
                array('value' => 'AWG', 'text' => __('Aruban florin', 'inwavethemes')),
                array('value' => 'AZN', 'text' => __('Azerbaijani manat', 'inwavethemes')),
                array('value' => 'BAM', 'text' => __('Bosnia and Herzegovina convertible mark', 'inwavethemes')),
                array('value' => 'BBD', 'text' => __('Barbadian dollar', 'inwavethemes')),
                array('value' => 'BDT', 'text' => __('Bangladeshi taka', 'inwavethemes')),
                array('value' => 'BGN', 'text' => __('Bulgarian lev', 'inwavethemes')),
                array('value' => 'BHD', 'text' => __('Bahraini dinar', 'inwavethemes')),
                array('value' => 'BIF', 'text' => __('Burundian franc', 'inwavethemes')),
                array('value' => 'BMD', 'text' => __('Bermudian dollar', 'inwavethemes')),
                array('value' => 'BND', 'text' => __('Brunei dollar', 'inwavethemes')),
                array('value' => 'BOB', 'text' => __('Bolivian boliviano', 'inwavethemes')),
                array('value' => 'BRL', 'text' => __('Brazilian real', 'inwavethemes')),
                array('value' => 'BSD', 'text' => __('Bahamian dollar', 'inwavethemes')),
                array('value' => 'BTC', 'text' => __('Bitcoin', 'inwavethemes')),
                array('value' => 'BTN', 'text' => __('Bhutanese ngultrum', 'inwavethemes')),
                array('value' => 'BWP', 'text' => __('Botswana pula', 'inwavethemes')),
                array('value' => 'BYR', 'text' => __('Belarusian ruble', 'inwavethemes')),
                array('value' => 'BZD', 'text' => __('Belize dollar', 'inwavethemes')),
                array('value' => 'CAD', 'text' => __('Canadian dollar', 'inwavethemes')),
                array('value' => 'CDF', 'text' => __('Congolese franc', 'inwavethemes')),
                array('value' => 'CHF', 'text' => __('Swiss franc', 'inwavethemes')),
                array('value' => 'CLP', 'text' => __('Chilean peso', 'inwavethemes')),
                array('value' => 'CNY', 'text' => __('Chinese yuan', 'inwavethemes')),
                array('value' => 'COP', 'text' => __('Colombian peso', 'inwavethemes')),
                array('value' => 'CRC', 'text' => __('Costa Rican col&oacute;n', 'inwavethemes')),
                array('value' => 'CUC', 'text' => __('Cuban convertible peso', 'inwavethemes')),
                array('value' => 'CUP', 'text' => __('Cuban peso', 'inwavethemes')),
                array('value' => 'CVE', 'text' => __('Cape Verdean escudo', 'inwavethemes')),
                array('value' => 'CZK', 'text' => __('Czech koruna', 'inwavethemes')),
                array('value' => 'DJF', 'text' => __('Djiboutian franc', 'inwavethemes')),
                array('value' => 'DKK', 'text' => __('Danish krone', 'inwavethemes')),
                array('value' => 'DOP', 'text' => __('Dominican peso', 'inwavethemes')),
                array('value' => 'DZD', 'text' => __('Algerian dinar', 'inwavethemes')),
                array('value' => 'EGP', 'text' => __('Egyptian pound', 'inwavethemes')),
                array('value' => 'ERN', 'text' => __('Eritrean nakfa', 'inwavethemes')),
                array('value' => 'ETB', 'text' => __('Ethiopian birr', 'inwavethemes')),
                array('value' => 'EUR', 'text' => __('Euro', 'inwavethemes')),
                array('value' => 'FJD', 'text' => __('Fijian dollar', 'inwavethemes')),
                array('value' => 'FKP', 'text' => __('Falkland Islands pound', 'inwavethemes')),
                array('value' => 'GBP', 'text' => __('Pound sterling', 'inwavethemes')),
                array('value' => 'GEL', 'text' => __('Georgian lari', 'inwavethemes')),
                array('value' => 'GGP', 'text' => __('Guernsey pound', 'inwavethemes')),
                array('value' => 'GHS', 'text' => __('Ghana cedi', 'inwavethemes')),
                array('value' => 'GIP', 'text' => __('Gibraltar pound', 'inwavethemes')),
                array('value' => 'GMD', 'text' => __('Gambian dalasi', 'inwavethemes')),
                array('value' => 'GNF', 'text' => __('Guinean franc', 'inwavethemes')),
                array('value' => 'GTQ', 'text' => __('Guatemalan quetzal', 'inwavethemes')),
                array('value' => 'GYD', 'text' => __('Guyanese dollar', 'inwavethemes')),
                array('value' => 'HKD', 'text' => __('Hong Kong dollar', 'inwavethemes')),
                array('value' => 'HNL', 'text' => __('Honduran lempira', 'inwavethemes')),
                array('value' => 'HRK', 'text' => __('Croatian kuna', 'inwavethemes')),
                array('value' => 'HTG', 'text' => __('Haitian gourde', 'inwavethemes')),
                array('value' => 'HUF', 'text' => __('Hungarian forint', 'inwavethemes')),
                array('value' => 'IDR', 'text' => __('Indonesian rupiah', 'inwavethemes')),
                array('value' => 'ILS', 'text' => __('Israeli new shekel', 'inwavethemes')),
                array('value' => 'IMP', 'text' => __('Manx pound', 'inwavethemes')),
                array('value' => 'INR', 'text' => __('Indian rupee', 'inwavethemes')),
                array('value' => 'IQD', 'text' => __('Iraqi dinar', 'inwavethemes')),
                array('value' => 'IRR', 'text' => __('Iranian rial', 'inwavethemes')),
                array('value' => 'ISK', 'text' => __('Icelandic kr&oacute;na', 'inwavethemes')),
                array('value' => 'JEP', 'text' => __('Jersey pound', 'inwavethemes')),
                array('value' => 'JMD', 'text' => __('Jamaican dollar', 'inwavethemes')),
                array('value' => 'JOD', 'text' => __('Jordanian dinar', 'inwavethemes')),
                array('value' => 'JPY', 'text' => __('Japanese yen', 'inwavethemes')),
                array('value' => 'KES', 'text' => __('Kenyan shilling', 'inwavethemes')),
                array('value' => 'KGS', 'text' => __('Kyrgyzstani som', 'inwavethemes')),
                array('value' => 'KHR', 'text' => __('Cambodian riel', 'inwavethemes')),
                array('value' => 'KMF', 'text' => __('Comorian franc', 'inwavethemes')),
                array('value' => 'KPW', 'text' => __('North Korean won', 'inwavethemes')),
                array('value' => 'KRW', 'text' => __('South Korean won', 'inwavethemes')),
                array('value' => 'KWD', 'text' => __('Kuwaiti dinar', 'inwavethemes')),
                array('value' => 'KYD', 'text' => __('Cayman Islands dollar', 'inwavethemes')),
                array('value' => 'KZT', 'text' => __('Kazakhstani tenge', 'inwavethemes')),
                array('value' => 'LAK', 'text' => __('Lao kip', 'inwavethemes')),
                array('value' => 'LBP', 'text' => __('Lebanese pound', 'inwavethemes')),
                array('value' => 'LKR', 'text' => __('Sri Lankan rupee', 'inwavethemes')),
                array('value' => 'LRD', 'text' => __('Liberian dollar', 'inwavethemes')),
                array('value' => 'LSL', 'text' => __('Lesotho loti', 'inwavethemes')),
                array('value' => 'LYD', 'text' => __('Libyan dinar', 'inwavethemes')),
                array('value' => 'MAD', 'text' => __('Moroccan dirham', 'inwavethemes')),
                array('value' => 'MDL', 'text' => __('Moldovan leu', 'inwavethemes')),
                array('value' => 'MGA', 'text' => __('Malagasy ariary', 'inwavethemes')),
                array('value' => 'MKD', 'text' => __('Macedonian denar', 'inwavethemes')),
                array('value' => 'MMK', 'text' => __('Burmese kyat', 'inwavethemes')),
                array('value' => 'MNT', 'text' => __('Mongolian t&ouml;gr&ouml;g', 'inwavethemes')),
                array('value' => 'MOP', 'text' => __('Macanese pataca', 'inwavethemes')),
                array('value' => 'MRO', 'text' => __('Mauritanian ouguiya', 'inwavethemes')),
                array('value' => 'MUR', 'text' => __('Mauritian rupee', 'inwavethemes')),
                array('value' => 'MVR', 'text' => __('Maldivian rufiyaa', 'inwavethemes')),
                array('value' => 'MWK', 'text' => __('Malawian kwacha', 'inwavethemes')),
                array('value' => 'MXN', 'text' => __('Mexican peso', 'inwavethemes')),
                array('value' => 'MYR', 'text' => __('Malaysian ringgit', 'inwavethemes')),
                array('value' => 'MZN', 'text' => __('Mozambican metical', 'inwavethemes')),
                array('value' => 'NAD', 'text' => __('Namibian dollar', 'inwavethemes')),
                array('value' => 'NGN', 'text' => __('Nigerian naira', 'inwavethemes')),
                array('value' => 'NIO', 'text' => __('Nicaraguan c&oacute;rdoba', 'inwavethemes')),
                array('value' => 'NOK', 'text' => __('Norwegian krone', 'inwavethemes')),
                array('value' => 'NPR', 'text' => __('Nepalese rupee', 'inwavethemes')),
                array('value' => 'NZD', 'text' => __('New Zealand dollar', 'inwavethemes')),
                array('value' => 'OMR', 'text' => __('Omani rial', 'inwavethemes')),
                array('value' => 'PAB', 'text' => __('Panamanian balboa', 'inwavethemes')),
                array('value' => 'PEN', 'text' => __('Peruvian nuevo sol', 'inwavethemes')),
                array('value' => 'PGK', 'text' => __('Papua New Guinean kina', 'inwavethemes')),
                array('value' => 'PHP', 'text' => __('Philippine peso', 'inwavethemes')),
                array('value' => 'PKR', 'text' => __('Pakistani rupee', 'inwavethemes')),
                array('value' => 'PLN', 'text' => __('Polish z&#x142;oty', 'inwavethemes')),
                array('value' => 'PRB', 'text' => __('Transnistrian ruble', 'inwavethemes')),
                array('value' => 'PYG', 'text' => __('Paraguayan guaran&iacute;', 'inwavethemes')),
                array('value' => 'QAR', 'text' => __('Qatari riyal', 'inwavethemes')),
                array('value' => 'RON', 'text' => __('Romanian leu', 'inwavethemes')),
                array('value' => 'RSD', 'text' => __('Serbian dinar', 'inwavethemes')),
                array('value' => 'RUB', 'text' => __('Russian ruble', 'inwavethemes')),
                array('value' => 'RWF', 'text' => __('Rwandan franc', 'inwavethemes')),
                array('value' => 'SAR', 'text' => __('Saudi riyal', 'inwavethemes')),
                array('value' => 'SBD', 'text' => __('Solomon Islands dollar', 'inwavethemes')),
                array('value' => 'SCR', 'text' => __('Seychellois rupee', 'inwavethemes')),
                array('value' => 'SDG', 'text' => __('Sudanese pound', 'inwavethemes')),
                array('value' => 'SEK', 'text' => __('Swedish krona', 'inwavethemes')),
                array('value' => 'SGD', 'text' => __('Singapore dollar', 'inwavethemes')),
                array('value' => 'SHP', 'text' => __('Saint Helena pound', 'inwavethemes')),
                array('value' => 'SLL', 'text' => __('Sierra Leonean leone', 'inwavethemes')),
                array('value' => 'SOS', 'text' => __('Somali shilling', 'inwavethemes')),
                array('value' => 'SRD', 'text' => __('Surinamese dollar', 'inwavethemes')),
                array('value' => 'SSP', 'text' => __('South Sudanese pound', 'inwavethemes')),
                array('value' => 'STD', 'text' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'inwavethemes')),
                array('value' => 'SYP', 'text' => __('Syrian pound', 'inwavethemes')),
                array('value' => 'SZL', 'text' => __('Swazi lilangeni', 'inwavethemes')),
                array('value' => 'THB', 'text' => __('Thai baht', 'inwavethemes')),
                array('value' => 'TJS', 'text' => __('Tajikistani somoni', 'inwavethemes')),
                array('value' => 'TMT', 'text' => __('Turkmenistan manat', 'inwavethemes')),
                array('value' => 'TND', 'text' => __('Tunisian dinar', 'inwavethemes')),
                array('value' => 'TOP', 'text' => __('Tongan pa&#x2bb;anga', 'inwavethemes')),
                array('value' => 'TRY', 'text' => __('Turkish lira', 'inwavethemes')),
                array('value' => 'TTD', 'text' => __('Trinidad and Tobago dollar', 'inwavethemes')),
                array('value' => 'TWD', 'text' => __('New Taiwan dollar', 'inwavethemes')),
                array('value' => 'TZS', 'text' => __('Tanzanian shilling', 'inwavethemes')),
                array('value' => 'UAH', 'text' => __('Ukrainian hryvnia', 'inwavethemes')),
                array('value' => 'UGX', 'text' => __('Ugandan shilling', 'inwavethemes')),
                array('value' => 'USD', 'text' => __('United States dollar', 'inwavethemes')),
                array('value' => 'UYU', 'text' => __('Uruguayan peso', 'inwavethemes')),
                array('value' => 'UZS', 'text' => __('Uzbekistani som', 'inwavethemes')),
                array('value' => 'VEF', 'text' => __('Venezuelan bol&iacute;var', 'inwavethemes')),
                array('value' => 'VND', 'text' => __('Vietnamese &#x111;&#x1ed3;ng', 'inwavethemes')),
                array('value' => 'VUV', 'text' => __('Vanuatu vatu', 'inwavethemes')),
                array('value' => 'WST', 'text' => __('Samoan t&#x101;l&#x101;', 'inwavethemes')),
                array('value' => 'XAF', 'text' => __('Central African CFA franc', 'inwavethemes')),
                array('value' => 'XCD', 'text' => __('East Caribbean dollar', 'inwavethemes')),
                array('value' => 'XOF', 'text' => __('West African CFA franc', 'inwavethemes')),
                array('value' => 'XPF', 'text' => __('CFP franc', 'inwavethemes')),
                array('value' => 'YER', 'text' => __('Yemeni rial', 'inwavethemes')),
                array('value' => 'ZAR', 'text' => __('South African rand', 'inwavethemes')),
                array('value' => 'ZMW', 'text' => __('Zambian kwacha', 'inwavethemes'))
            );
        }

        public static function getIWCurrencySymbol($currency) {
            $symbols = array(
                'AED' => '&#x62f;.&#x625;',
                'AFN' => '&#x60b;',
                'ALL' => 'L',
                'AMD' => 'AMD',
                'ANG' => '&fnof;',
                'AOA' => 'Kz',
                'ARS' => '&#36;',
                'AUD' => '&#36;',
                'AWG' => '&fnof;',
                'AZN' => 'AZN',
                'BAM' => 'KM',
                'BBD' => '&#36;',
                'BDT' => '&#2547;&nbsp;',
                'BGN' => '&#1083;&#1074;.',
                'BHD' => '.&#x62f;.&#x628;',
                'BIF' => 'Fr',
                'BMD' => '&#36;',
                'BND' => '&#36;',
                'BOB' => 'Bs.',
                'BRL' => '&#82;&#36;',
                'BSD' => '&#36;',
                'BTC' => '&#3647;',
                'BTN' => 'Nu.',
                'BWP' => 'P',
                'BYR' => 'Br',
                'BZD' => '&#36;',
                'CAD' => '&#36;',
                'CDF' => 'Fr',
                'CHF' => '&#67;&#72;&#70;',
                'CLP' => '&#36;',
                'CNY' => '&yen;',
                'COP' => '&#36;',
                'CRC' => '&#x20a1;',
                'CUC' => '&#36;',
                'CUP' => '&#36;',
                'CVE' => '&#36;',
                'CZK' => '&#75;&#269;',
                'DJF' => 'Fr',
                'DKK' => 'DKK',
                'DOP' => 'RD&#36;',
                'DZD' => '&#x62f;.&#x62c;',
                'EGP' => 'EGP',
                'ERN' => 'Nfk',
                'ETB' => 'Br',
                'EUR' => '&euro;',
                'FJD' => '&#36;',
                'FKP' => '&pound;',
                'GBP' => '&pound;',
                'GEL' => '&#x10da;',
                'GGP' => '&pound;',
                'GHS' => '&#x20b5;',
                'GIP' => '&pound;',
                'GMD' => 'D',
                'GNF' => 'Fr',
                'GTQ' => 'Q',
                'GYD' => '&#36;',
                'HKD' => '&#36;',
                'HNL' => 'L',
                'HRK' => 'Kn',
                'HTG' => 'G',
                'HUF' => '&#70;&#116;',
                'IDR' => 'Rp',
                'ILS' => '&#8362;',
                'IMP' => '&pound;',
                'INR' => '&#8377;',
                'IQD' => '&#x639;.&#x62f;',
                'IRR' => '&#xfdfc;',
                'ISK' => 'Kr.',
                'JEP' => '&pound;',
                'JMD' => '&#36;',
                'JOD' => '&#x62f;.&#x627;',
                'JPY' => '&yen;',
                'KES' => 'KSh',
                'KGS' => '&#x43b;&#x432;',
                'KHR' => '&#x17db;',
                'KMF' => 'Fr',
                'KPW' => '&#x20a9;',
                'KRW' => '&#8361;',
                'KWD' => '&#x62f;.&#x643;',
                'KYD' => '&#36;',
                'KZT' => 'KZT',
                'LAK' => '&#8365;',
                'LBP' => '&#x644;.&#x644;',
                'LKR' => '&#xdbb;&#xdd4;',
                'LRD' => '&#36;',
                'LSL' => 'L',
                'LYD' => '&#x644;.&#x62f;',
                'MAD' => '&#x62f;. &#x645;.',
                'MAD' => '&#x62f;.&#x645;.',
                'MDL' => 'L',
                'MGA' => 'Ar',
                'MKD' => '&#x434;&#x435;&#x43d;',
                'MMK' => 'Ks',
                'MNT' => '&#x20ae;',
                'MOP' => 'P',
                'MRO' => 'UM',
                'MUR' => '&#x20a8;',
                'MVR' => '.&#x783;',
                'MWK' => 'MK',
                'MXN' => '&#36;',
                'MYR' => '&#82;&#77;',
                'MZN' => 'MT',
                'NAD' => '&#36;',
                'NGN' => '&#8358;',
                'NIO' => 'C&#36;',
                'NOK' => '&#107;&#114;',
                'NPR' => '&#8360;',
                'NZD' => '&#36;',
                'OMR' => '&#x631;.&#x639;.',
                'PAB' => 'B/.',
                'PEN' => 'S/.',
                'PGK' => 'K',
                'PHP' => '&#8369;',
                'PKR' => '&#8360;',
                'PLN' => '&#122;&#322;',
                'PRB' => '&#x440;.',
                'PYG' => '&#8370;',
                'QAR' => '&#x631;.&#x642;',
                'RMB' => '&yen;',
                'RON' => 'lei',
                'RSD' => '&#x434;&#x438;&#x43d;.',
                'RUB' => '&#8381;',
                'RWF' => 'Fr',
                'SAR' => '&#x631;.&#x633;',
                'SBD' => '&#36;',
                'SCR' => '&#x20a8;',
                'SDG' => '&#x62c;.&#x633;.',
                'SEK' => '&#107;&#114;',
                'SGD' => '&#36;',
                'SHP' => '&pound;',
                'SLL' => 'Le',
                'SOS' => 'Sh',
                'SRD' => '&#36;',
                'SSP' => '&pound;',
                'STD' => 'Db',
                'SYP' => '&#x644;.&#x633;',
                'SZL' => 'L',
                'THB' => '&#3647;',
                'TJS' => '&#x405;&#x41c;',
                'TMT' => 'm',
                'TND' => '&#x62f;.&#x62a;',
                'TOP' => 'T&#36;',
                'TRY' => '&#8378;',
                'TTD' => '&#36;',
                'TWD' => '&#78;&#84;&#36;',
                'TZS' => 'Sh',
                'UAH' => '&#8372;',
                'UGX' => 'UGX',
                'USD' => '&#36;',
                'UYU' => '&#36;',
                'UZS' => 'UZS',
                'VEF' => 'Bs F',
                'VND' => '&#8363;',
                'VUV' => 'Vt',
                'WST' => 'T',
                'XAF' => 'Fr',
                'XCD' => '&#36;',
                'XOF' => 'Fr',
                'XPF' => 'Fr',
                'YER' => '&#xfdfc;',
                'ZAR' => '&#82;',
                'ZMW' => 'ZK'
            );
            return $symbols[$currency];
        }
        
        public function checkUseWoocommercePayment() {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            return (is_plugin_active('woocommerce/woocommerce.php') && $this->getCoursesOption('enable_wc_booking', '0'));
        }
		
		public function getMoneyFormatedString($value, $currency = '') {
            if(!$value){
                return;
            }
            if (!$currency) {
                $currency = $this->getCoursesOption('currency', 'USD');
            }
            $currency_sym = $this->getIWCurrencySymbol($currency);
            $currency_pos = $this->getCoursesOption('currency_pos','left');
            $dec_number = (intval($this->getCoursesOption('number_of_decimals')) && intval($this->getCoursesOption('number_of_decimals')) > 0) ? intval($this->getCoursesOption('number_of_decimals')) : 0;
            $thousands_separator = $this->getCoursesOption('thousands_separator') == 'none' ? '' : $this->getCoursesOption('thousands_separator');

            $f_formated = number_format($value, $dec_number, $this->getCoursesOption('decimal_separator'), $thousands_separator);
            $result = $currency_sym . $f_formated;
            if ($currency_pos == 'left_space') {
                $result = $currency_sym . ' ' . $f_formated;
            }
            if ($currency_pos == 'right') {
                $result = $f_formated . $currency_sym;
            }
            if ($currency_pos == 'right_space') {
                $result = $f_formated . ' ' . $currency_sym;
            }
            return esc_attr($result);
        }

}
