<?php {

    if (!isloggedin() or isguestuser()) {
        $url = new Moodle_url('/login/index.php');
        $btn = 'btn-primary';
        $title = get_string('login');
        $icon = 'icon-white';
    } else {
        $url = new Moodle_url('/login/logout.php');
        $btn = '';
        $title = get_string('logout');
        $icon = '';
}
        $content = '';
        $content .= html_writer::start_tag('a', array('href' => $url, 'title' => $title));
        $content .= html_writer::start_tag('button', array('type' => 'button', 'class' => 'pull-right btn btn-small ' .$btn));
        $content .= $title . ' ';
        $content .= html_writer::start_tag('i', array('class' => 'icon-hand-left ' .$icon));
        $content .= html_writer::end_tag('i');
        $content .= html_writer::end_tag('button');
        $content .= html_writer::end_tag('a');

        echo $content;

} ?>