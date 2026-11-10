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
 * Delete a Quick Links entry.
 * Handles the deletion of a single link from the block_quicklinks_links table,
 * including its associated logo files. Displays a confirmation dialog before
 * performing the deletion.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at/tlc}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Set up the external admin page and require a valid session key.
admin_externalpage_setup('block_quicklinks_manage');
require_sesskey();

// Get required and optional parameters.
$linkid = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

// Check that the user has the required capability.
$context = context_system::instance();
require_capability('block/quicklinks:managelinks', $context);

// Load the link record or throw an error if it does not exist.
$link = $DB->get_record('block_quicklinks_links', ['id' => $linkid], '*', MUST_EXIST);

if ($confirm) {
    // Delete all logo files associated with this link.
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'block_quicklinks', 'logo', $link->id);

    // Delete the link record from the database.
    $DB->delete_records('block_quicklinks_links', ['id' => $link->id]);

    // Redirect back to the management page with a success message.
    redirect(new moodle_url('/blocks/quicklinks/manage.php'),
        get_string('linkdeleted', 'block_quicklinks'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Display confirmation dialog.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('deletelink', 'block_quicklinks'));
echo $OUTPUT->confirm(
    get_string('deleteconfirm', 'block_quicklinks', $link->label),
    new moodle_url('/blocks/quicklinks/delete.php', ['id' => $link->id, 'confirm' => 1, 'sesskey' => sesskey()]),
    new moodle_url('/blocks/quicklinks/manage.php')
);
echo $OUTPUT->footer();
