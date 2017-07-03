<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   turnitintooltwo
 * @copyright 2012 iParadigms LLC
 */

require_once(__DIR__."/turnitintooltwo_view.class.php");

function activate_migration() {
    global $DB, $CFG;
    $migration_enabled_params = array(
        'plugin' => 'turnitintooltwo',
        'name' => 'migration_enabled'
    );
    $migration_enabled = $DB->get_record('config_plugins', $migration_enabled_params);

    $activation_properties = new stdClass;
    $activation_properties->plugin = 'turnitintooltwo';
    $activation_properties->name = 'migration_enabled';
    $activation_properties->value  = 1;

    if (empty($migration_enabled)) {
        $activation = $DB->insert_record('config_plugins', $activation_properties);
    } else {
        $activation = $DB->update_record('config_plugins', $activation_properties);
    }

    if ($activation) {
        $urlparams = array('activation' => 'success');
    } else {
        $urlparams = array('activation' => 'failure');
    }
    redirect(new moodle_url('/mod/turnitintooltwo/settings.php', $urlparams));
}

function display_page() {
    $turnitintooltwoview = new turnitintooltwo_view();
    $turnitintooltwoview->load_page_components();

    $notice = html_writer::tag(
        'div',
        get_string('activatemigrationnotice', 'turnitintooltwo'),
        array('class'=>'alert alert-info')
    );
    $warning = html_writer(
        'div',
        get_string('activatemigrationwarning', 'turnitintooltwo'),
        array('class'=>'alert alert-warning')
    );
    $button = html_writer::link(
        new moodle_url('/mod/turnitintooltwo/activate_migration.php', array('do_migration' => 1)),
        get_string('activatemigration', 'turnitintooltwo'),
        array('class'=>'btn btn-default', 'role' => 'button')
    );

    echo $OUTPUT->header();
    echo html_writer::start_tag('div', array('class' => 'mod_turnitintooltwo'));
    echo $OUTPUT->heading(get_string('pluginname', 'turnitintooltwo'), 2, 'main');
    echo $notice;
    echo $warning;
    echo $button;
    echo html_writer::end_tag("div");
}

if ($ADMIN->full_tree) {
    $do_migration = optional_param('do_migration', 0, PARAM_INT);

    if ($do_migration) {
        activate_migration();
    } else {
        display_page();
    }
} else {
    die(get_string('notadmin', 'turnitintooltwo'));
}