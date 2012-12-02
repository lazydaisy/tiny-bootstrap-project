<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {


    // Font file setting
    $name = 'theme_tiny/font';
    $title = get_string('font','theme_tiny');
    $description = get_string('fontdesc', 'theme_tiny');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $settings->add($setting);

    // Font-Face setting
    $name = 'theme_tiny/fontface';
    $title = get_string('fontface','theme_tiny');
    $description = get_string('fontfacedesc', 'theme_tiny');
    $default = 'Tempus Sans ITC';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $settings->add($setting);

    // Tiny Navbar (Background brand icon)
    $name = 'theme_tiny/navbaricon';
    $title=get_string('navbaricon','theme_tiny');
    $description = get_string('navbaricondesc', 'theme_tiny');
    $default = '';
    $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
    $settings->add($setting);

    // Custom CSS file
    $name = 'theme_tiny/customcss';
    $title = get_string('customcss','theme_tiny');
    $description = get_string('customcssdesc', 'theme_tiny');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

    // Welcome note setting
    $name = 'theme_tiny/welcomenote';
    $title = get_string('welcomenote','theme_tiny');
    $description = get_string('welcomenotedesc', 'theme_tiny');
    $default = get_string('welcomenotetxt', 'theme_tiny');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    // Footnote setting
    $name = 'theme_tiny/footnote';
    $title = get_string('footnote','theme_tiny');
    $description = get_string('footnotedesc', 'theme_tiny');
    $default = get_string('footnotetxt', 'theme_tiny');
    $setting = new admin_setting_confightmleditor($name, $title, $description, $default);
    $settings->add($setting);

    // Theme override of custommenu items
    $name = 'theme_tiny/custommenuitems';
    $title = get_string('custommenuitems', 'admin');
    $description = get_string('configcustommenuitems', 'admin');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $settings->add($setting);

}