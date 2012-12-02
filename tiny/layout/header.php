<!-- Main default unit for a primary marketing message or call to action -->
<div class="default-unit">

<?php if ($hasheader) {

    if (!isloggedin() or isguestuser()) { ?>

        <div id="page-header">
           <?php
        if ($haswelcomenote) {
            echo $PAGE->theme->settings->welcomenote;
        } else {
            echo get_string('welcomenotetxt', 'theme_tiny');
        } ?>

        <p><a class="btn btn-large" href="http://docs.moodle.org/23/en/Tiny_theme_for_Moodle_2.3"><i class="icon-hand-right"></i>&nbsp;&nbsp;<?php echo get_string('learnmore', 'theme_tiny'); ?></a></p>
        </div>

        <?php
    } else {
        echo '<div id="page-header">';
    if ($hasheading) {
        echo '<h1 class="header-icon">'.$PAGE->heading.'</h1>';
    }
        echo '</div>';
} ?>

    <?php
} ?>

</div>