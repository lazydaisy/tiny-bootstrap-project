<?php

class theme_tiny_core_renderer extends core_renderer {

        /**
         * The standard tags (typically performance information and validation links,
         * if we are in developer debug mode) that should be output in the footer area
         * of the page. Designed to be called in theme layout.php files.
         *
         * @return string HTML fragment.
         */
        public function standard_footer_html() {
            global $CFG, $SCRIPT;

            // This function is normally called from a layout.php file in {@link core_renderer::header()}
            // but some of the content won't be known until later, so we return a placeholder
            // for now. This will be replaced with the real content in {@link core_renderer::footer()}.
            $output = $this->unique_performance_info_token;
            if ($this->page->devicetypeinuse == 'legacy') {
                // The legacy theme is in use print the notification
                $output .= html_writer::tag('div', get_string('legacythemeinuse'), array('class'=>'legacythemeinuse'));
            }

            // Get links to switch device types (only shown for users not on a default device)
            $output .= $this->theme_switch_links();

            if (!empty($CFG->debugpageinfo)) {
                $output .= '<div class="performanceinfo pageinfo well"><i class="icon-cog"></i>&nbsp;&nbsp;This page is: ' . $this->page->debug_summary() . '</div>';
            }
            if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', context_system::instance())) {  // Only in developer mode
                // Add link to profiling report if necessary
                if (function_exists('profiling_is_running') && profiling_is_running()) {
                    $txt = get_string('profiledscript', 'admin');
                    $title = get_string('profiledscriptview', 'admin');
                    $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                    $link= '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
                    $output .= '<div class="profilingfooter">' . $link . '</div>';
                }
                $output .= '<div class="purgecaches"><a class="btn btn-small" href="'.$CFG->wwwroot.'/'.$CFG->admin.'/purgecaches.php?confirm=1&amp;sesskey='.sesskey().'"><i class="icon-trash"></i>&nbsp;&nbsp;'.get_string('purgecaches', 'admin').'</a></div>';
            }
            if (!empty($CFG->debugvalidators)) {
                // NOTE: this is not a nice hack, $PAGE->url is not always accurate and $FULLME neither, it is not a bug if it fails. --skodak
                $output .= '<div class="validators"><ul>
                  <li><a class="btn btn-small btn-info" href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri=' . urlencode(qualified_me()) . '"><i class="icon-cog icon-white"></i>&nbsp;&nbsp;Validate HTML</a></li>
                  <li><a class="btn btn-small btn-info" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=' . urlencode(qualified_me()) . '"><i class="icon-cog icon-white"></i>&nbsp;&nbsp;Section 508 Check</a></li>
                  <li><a class="btn btn-small btn-info" href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1=' . urlencode(qualified_me()) . '"><i class="icon-cog icon-white"></i>&nbsp;&nbsp;WCAG 1 (2,3) Check</a></li>
                </ul><br /></div>';
            }
            if (!empty($CFG->additionalhtmlfooter)) {
                $output .= "\n".$CFG->additionalhtmlfooter;
            }
            return $output;
        }

        /**
         * Return the 'back' link that normally appears in the footer.
         *
         * @return string HTML fragment.
         */
        public function home_link() {
            global $CFG, $SITE;

            if ($this->page->pagetype == 'site-index') {
                // Special case for site home page - please do not remove
                return '<div class="sitelink">' .
                       '<a title="Moodle" href="http://moodle.org/">' .
                       '<img style="width:100px;height:30px" src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

            } else if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
                // Special case for during install/upgrade.
                return '<div class="sitelink">'.
                       '<a title="Moodle" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                       '<img style="width:100px;height:30px" src="' . $this->pix_url('moodlelogo') . '" alt="moodlelogo" /></a></div>';

            } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
                return '<div class="homelink"><a class="btn btn-small" href="' . $CFG->wwwroot . '/"><i class="icon-home"></i>&nbsp;&nbsp;' .
                        get_string('home') . '</a></div>';

            } else {
                return '<div class="homelink"><a class="btn btn-small" href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '"><i class="icon-home"></i>&nbsp;&nbsp;' .
                        format_string($this->page->course->shortname, true, array('context' => $this->page->context)) . '</a></div>';
            }
        }

        /**
         * Return the standard string that says whether you are logged in (and switched
         * roles/logged in as another user).
         * @param bool $withlinks if false, then don't include any links in the HTML produced.
         * If not set, the default is the nologinlinks option from the theme config.php file,
         * and if that is not set, then links are included.
         * @return string HTML fragment.
         */
        public function login_info($withlinks = null) {
            global $USER, $CFG, $DB, $SESSION;

            if (during_initial_install()) {
                return '';
            }

            if (is_null($withlinks)) {
                $withlinks = empty($this->page->layout_options['nologinlinks']);
            }

            $loginpage = ((string)$this->page->url === get_login_url());
            $course = $this->page->course;
            if (session_is_loggedinas()) {
                $realuser = session_get_realuser();
                $fullname = fullname($realuser, true);
                if ($withlinks) {
                    $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=".sesskey()."\">$fullname</a>] ";
                } else {
                    $realuserinfo = " [$fullname] ";
                }
            } else {
                $realuserinfo = '';
            }

            $loginurl = get_login_url();

            if (empty($course->id)) {
                // $course->id is not defined during installation
                return '';
            } else if (isloggedin()) {
                $context = context_course::instance($course->id);

                $fullname = fullname($USER, true);
                // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
                if ($withlinks) {
                    $username = "<a href=\"$CFG->wwwroot/user/profile.php?id=$USER->id\">$fullname</a>";
                } else {
                    $username = $fullname;
                }
                if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                    if ($withlinks) {
                        $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
                    } else {
                        $username .= " from {$idprovider->name}";
                    }
                }
                if (isguestuser()) {
                    $loggedinas = $realuserinfo.get_string('loggedinasguest');
                    if (!$loginpage && $withlinks) {
                        $loggedinas .= " <a class=\"btn btn-small btn-primary\" href=\"$loginurl\"><i class=\"icon-hand-right icon-white\"></i>&nbsp;&nbsp;".get_string('login').'</a>';
                    }
                } else if (is_role_switched($course->id)) { // Has switched roles
                    $rolename = '';
                    if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                        $rolename = ': '.format_string($role->name);
                    }
                    $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename;
                    if ($withlinks) {
                        $loggedinas .= " (<a href=\"$CFG->wwwroot/course/view.php?id=$course->id&amp;switchrole=0&amp;sesskey=".sesskey()."\">".get_string('switchrolereturn').'</a>)';
                    }
                } else {
                    $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username);
                    if ($withlinks) {
                        $loggedinas .= '&nbsp;&nbsp;<a class="btn btn-small" href="$CFG->wwwroot/login/logout.php?sesskey='.sesskey().'">'.get_string('logout').'&nbsp;&nbsp;<i class="icon-hand-left"></i></a>';
                    }
                }
            } else {
                $loggedinas = get_string('loggedinnot', 'moodle');
                if (!$loginpage && $withlinks) {
                    $loggedinas .= " <a class=\"btn btn-small btn-primary\" href=\"$loginurl\"><i class=\"icon-hand-right icon-white\"></i>&nbsp;&nbsp;".get_string('login').'</a>';
                }
            }

            $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

            if (isset($SESSION->justloggedin)) {
                unset($SESSION->justloggedin);
                if (!empty($CFG->displayloginfailures)) {
                    if (!isguestuser()) {
                        if ($count = count_login_failures($CFG->displayloginfailures, $USER->username, $USER->lastlogin)) {
                            $loggedinas .= '&nbsp;<div class="loginfailures">';
                            if (empty($count->accounts)) {
                                $loggedinas .= get_string('failedloginattempts', '', $count);
                            } else {
                                $loggedinas .= get_string('failedloginattemptsall', '', $count);
                            }
                            if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                                $loggedinas .= ' <a href="'.$CFG->wwwroot.'/report/log/index.php'.
                                                     '?chooselog=1&amp;id=1&amp;modid=site_errors">'.get_string('logs').'</a>';
                            }
                            $loggedinas .= '</div>';
                        }
                    }
                }
            }

            return $loggedinas;
    }

         /**
         * Redirects the user by any means possible given the current state
         *
         * This function should not be called directly, it should always be called using
         * the redirect function in lib/weblib.php
         *
         * The redirect function should really only be called before page output has started
         * however it will allow itself to be called during the state STATE_IN_BODY
         *
         * @param string $encodedurl The URL to send to encoded if required
         * @param string $message The message to display to the user if any
         * @param int $delay The delay before redirecting a user, if $message has been
         *         set this is a requirement and defaults to 3, set to 0 no delay
         * @param boolean $debugdisableredirect this redirect has been disabled for
         *         debugging purposes. Display a message that explains, and don't
         *         trigger the redirect.
         * @return string The HTML to display to the user before dying, may contain
         *         meta refresh, javascript refresh, and may have set header redirects
         */
        public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {
            global $CFG;
            $url = str_replace('&amp;', '&', $encodedurl);

            switch ($this->page->state) {
                case moodle_page::STATE_BEFORE_HEADER :
                    // No output yet it is safe to delivery the full arsenal of redirect methods
                    if (!$debugdisableredirect) {
                        // Don't use exactly the same time here, it can cause problems when both redirects fire at the same time.
                        $this->metarefreshtag = '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />'."\n";
                        $this->page->requires->js_function_call('document.location.replace', array($url), false, ($delay + 3));
                    }
                    $output = $this->header();
                    break;
                case moodle_page::STATE_PRINTING_HEADER :
                    // We should hopefully never get here
                    throw new coding_exception('You cannot redirect while printing the page header');
                    break;
                case moodle_page::STATE_IN_BODY :
                    // We really shouldn't be here but we can deal with this
                    debugging("You should really redirect before you start page output");
                    if (!$debugdisableredirect) {
                        $this->page->requires->js_function_call('document.location.replace', array($url), false, $delay);
                    }
                    $output = $this->opencontainers->pop_all_but_last();
                    break;
                case moodle_page::STATE_DONE :
                    // Too late to be calling redirect now
                    throw new coding_exception('You cannot redirect after the entire page has been generated');
                    break;
            }
            $output .= $this->notification($message, 'redirectmessage');
            $output .= '<div class="continuebutton"><a class="btn btn-small btn-info" href="'. $encodedurl .'">'. get_string('continue') .'&nbsp;&nbsp;<i class="icon-forward icon-white"></i></a></div>';
            if ($debugdisableredirect) {
                $output .= '<p><strong>Error output, so disabling automatic redirect.</strong></p>';
            }
            $output .= $this->footer();
            return $output;
    }

        /**
         * Returns HTML to display a "Turn editing on/off" button in a form.
         *
         * @param moodle_url $url The URL + params to send through when clicking the button
         * @return string HTML the button
         * written for Tiny Bootstrap Project byLazyDaisy.co.uk
         */
        public function edit_button(moodle_url $url) {

            $url->param('sesskey', sesskey());
            if ($this->page->user_is_editing()) {
                $url->param('edit', 'off');
                $btn = 'btn-danger';
                $icon = 'icon-edit';
                $alt = get_string('turneditingoff');
            } else {
                $url->param('edit', 'on');
                $btn = 'btn-success';
                $icon = 'icon-off';
                $alt = get_string('turneditingon');
            }

            $content = '';
            $content .=  html_writer::start_tag('a', array('href' => $url, 'class' => 'btn btn-small '.$btn, 'title' => $alt, 'alt' => $alt));
            $content .=  html_writer::start_tag('i', array('class' => 'icon-white '.$icon));
            $content .=  html_writer::end_tag('i');
            $content .=  html_writer::end_tag('a');

            return $content;

   }

    /*
    * The standard navigation bar (breadcrumb)
    * shows the course category
    * For this theme the course category has been removed
    */
        public function navbar() {
            $items = $this->page->navbar->get_items();

            $htmlblocks = array();
            // Iterate the navarray and display each node
            $itemcount = count($items);
            $separator = get_separator();
            for ($i=0;$i < $itemcount;$i++) {
                $item = $items[$i];
                if ($item->type == "0" || $item->type == "30") {
                    continue;
                }
                $item->hideicon = true;
                if ($i===0) {
                    $content = html_writer::tag('li', $this->render($item));
                } else {
                    $content = html_writer::tag('li', $separator.$this->render($item));
                }
                $htmlblocks[] = $content;
            }
            //accessibility: heading for navbar list (MDL-20446)
            $navbarcontent = html_writer::tag('span', get_string('pagepath'), array('class'=>'accesshide'));
            $navbarcontent .= html_writer::tag('ul', join('', $htmlblocks));
            // XHTML
            return $navbarcontent;
        }

       /*
        * this renders the bootstrap top menu
        */

        protected function render_custom_menu(custom_menu $menu) {
        global $OUTPUT, $USER;
            // If the menu has no children return an empty string
        if (!$menu->has_children()) {
            return '';
        }
        // Initialise this custom menu


        $content = html_writer::start_tag('ul', array('class'=>'nav'));

        // Render each child
        foreach ($menu->get_children() as $item) {
        $content .= $this->render_custom_menu_item($item);
        }

        // Close the open tags
        // $content .= html_writer::end_tag('ul');

        // Return the custom menu
        return $content;
        }

       /*
        * This code renders the custom menu items for the
        * bootstrap dropdown menu
        */

        protected function render_custom_menu_item(custom_menu_item $menunode) {
        // Required to ensure we get unique trackable id's
        static $submenucount = 0;

        if ($menunode->has_children()) { // thanks to Amy Groshek the submenu now works.
            if ( !count($menunode->get_parent()) ) {
                $content = html_writer::start_tag('li', array('class'=>'dropdown'));
            } else {
                $content = html_writer::start_tag('li', array('class'=>'dropdown-submenu'));
            }
        //if ($menunode->has_children()) {
        //  $content = html_writer::start_tag('li', array('class'=>'dropdown'));
        // If the child has menus render it as a sub menu
            $submenucount++;
        if ($menunode->get_url() !== null) {
            $url = $menunode->get_url();
        } else {
            $url = '#cm_submenu_'.$submenucount;
        }

            $content .= html_writer::start_tag('a', array('href'=>$url,'class'=>'dropdown-toggle','data-toggle'=>'dropdown'));
        if ( !count($menunode->get_parent()) ) {
            $content .= html_writer::start_tag('i', array('class'=>"icon-leaf icon-white"));
        } else {
            $content .= html_writer::start_tag('i', array('class'=>"icon-leaf icon"));
        }
            $content .= html_writer::end_tag('i').'&nbsp;';


            $content .= $menunode->get_title().'&nbsp;';
        if ( !count($menunode->get_parent()) ) {
            $content .= html_writer::start_tag('b', array('class'=>'caret'));
            $content .= html_writer::end_tag('b');
        }
            $content .= html_writer::end_tag('a');
            $content .= html_writer::start_tag('ul', array('class'=>'dropdown-menu'));
            foreach ($menunode->get_children() as $menunode) {
            $content .= $this->render_custom_menu_item($menunode);
        }
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::end_tag('li');
        } else {
            $content = html_writer::start_tag('li');
        // The node doesn't have children so produce a final menuitem

        if ($menunode->get_url() !== null) {
            $url = $menunode->get_url();
        } else {
            $url = '#';
        }
        $content .= html_writer::link($url, $menunode->get_text(), array('title'=>$menunode->       get_title()));
        }
        $content .= html_writer::end_tag('li');
        // Return the sub menu
        return $content;
        }

}

include_once ($CFG->dirroot. '/course/renderer.php');


class theme_tiny_course_renderer extends core_course_renderer {

   /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|course_in_list $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        global $CFG;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';

        // display course summary
        if ($course->has_summary()) {
            $content .= html_writer::start_tag('div', array('class' => 'summary well pull-left'));
            $content .= $chelper->get_course_formatted_summary($course,
                    array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('div'); // .summary
        }

        // display course overview files
        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimages .= html_writer::tag('div',
                        html_writer::empty_tag('img', array('src' => $url)),
                        array('class' => 'courseimage well'));
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                        html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        $content .= $contentimages. $contentfiles;

        // display course contacts. See course_in_list::get_course_contacts()
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
            foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                $name = $coursecontact['rolename'].': '.
                        html_writer::link(new moodle_url('/user/view.php',
                                array('id' => $userid, 'course' => SITEID)),
                            $coursecontact['username']);
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul'); // .teachers
        }

        // display course category if necessary (for example in search results)
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                $content .= html_writer::end_tag('div'); // .coursecat
            }
        }

        return $content;
    }

}