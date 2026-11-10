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
 * Description and version information for block quicklinks
 *
 * Settings for the Quick Links block.
 * Defines the admin settings page for the block, accessible via
 * Site Administration → Plugins → Blocks → Quick Links.
 * Provides a custom block title setting and a shortcut button
 * to the link management page.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at/tlc}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
if ($hassiteconfig) {
    // Register the hidden external admin page for link management.
    $ADMIN->add('blocksettings', new admin_externalpage(
        'block_quicklinks_manage',
        get_string('managelinks', 'block_quicklinks'),
        new moodle_url('/blocks/quicklinks/manage.php'),
        'block/quicklinks:managelinks',
        true // Hidden - not shown in the admin navigation tree.
    ));	
    // Setting: custom block title (overrides the default 'Quick Links' heading).
    $settings->add(new admin_setting_configtext(
        'block_quicklinks/blocktitle',
        get_string('blocktitle', 'block_quicklinks'),
        get_string('blocktitle_desc', 'block_quicklinks'),
        get_string('pluginname', 'block_quicklinks')
    ));

	// Heading with a button linking to the link management page.
    $settings->add(new admin_setting_heading(
        'block_quicklinks/manageheading',
        get_string('managelinks', 'block_quicklinks'),
        html_writer::link(
            new moodle_url('/blocks/quicklinks/manage.php'),
            get_string('managelinks', 'block_quicklinks'),
            ['class' => 'btn btn-primary']
        )
    ));
}
