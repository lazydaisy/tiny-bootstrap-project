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