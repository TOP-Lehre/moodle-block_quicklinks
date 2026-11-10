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
 * Management page for Quick Links.
 * Provides a full CRUD interface for administrators to manage the links
 * displayed in the Quick Links block. Supports the following actions:
 * - list:              Display all links in a sortable table (default).
 * - add:               Show the form to create a new link.
 * - edit:              Show the form to edit an existing link.
 * - moveup / movedown: Change the sort order of a link.
 * - togglevisibility:  Show or hide a link without deleting it.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at/tlc}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

require_login();

// Page setup
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/quicklinks/manage.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('managelinks', 'block_quicklinks'));
$PAGE->set_heading(get_string('managelinks', 'block_quicklinks'));

// Ensure the user has permission to manage links.
require_capability('block/quicklinks:managelinks', $context);

// Determine the requested action and optional link ID.
$action = optional_param('action', 'list', PARAM_ALPHA);
$linkid = optional_param('id', 0, PARAM_INT);

// ---- EDIT / ADD ----
if ($action === 'edit' || $action === 'add') {
    $formurl = new moodle_url('/blocks/quicklinks/manage.php', ['action' => $action, 'id' => $linkid]);
    $form = new \block_quicklinks\form\edit_link($formurl);

    // Pre-populate the form when editing an existing link.
    if ($action === 'edit' && $linkid) {
        $record = $DB->get_record('block_quicklinks_links', ['id' => $linkid], '*', MUST_EXIST);

        // Prepare the draft file area so the current logo is shown in the form.
        $draftitemid = file_get_submitted_draft_itemid('logo');
        file_prepare_draft_area($draftitemid, $context->id, 'block_quicklinks', 'logo', $linkid,
            ['maxbytes' => 1048576, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif']]);
        $record->logo = $draftitemid;

        // Set form data from the existing record.
        $form->set_data([
            'id'                    => $record->id,
            'label'                 => $record->label,
            'source_type'           => $record->source_type,
            'manual_url'            => $record->manual_url,
            'customfield_shortname' => $record->customfield_shortname,
            'sortorder'             => $record->sortorder,
            'logo'                  => $draftitemid,
        ]);
    }

    // Handle form cancellation.
    if ($form->is_cancelled()) {
        redirect(new moodle_url('/blocks/quicklinks/manage.php'));
    }

    // Process submitted form data.
    if ($data = $form->get_data()) {
        $now = time();

        $record = new stdClass();
        $record->label = $data->label;
        $record->source_type = $data->source_type;
        $record->customfield_shortname = ($data->source_type === 'customfield') ? $data->customfield_shortname : '';
        $record->manual_url = ($data->source_type === 'manual') ? $data->manual_url : '';
		
		// For existing links keep the submitted sortorder; for new links auto-increment.
		if (!empty($data->id)) {
			$record->sortorder = $data->sortorder;
		} else {
			$maxsort = $DB->get_field_sql("SELECT MAX(sortorder) FROM {block_quicklinks_links}");
		$record->sortorder = ($maxsort !== false) ? $maxsort + 1 : 1;
		}
        $record->timemodified = $now;

        if (!empty($data->id)) {
			// Update existing record.
            $record->id = $data->id;
            $DB->update_record('block_quicklinks_links', $record);
            $recordid = $record->id;
        } else {
			// Insert new record.
            $record->timecreated = $now;
            $recordid = $DB->insert_record('block_quicklinks_links', $record);
        }

        // Save the uploaded logo file from the draft area.
        file_save_draft_area_files($data->logo, $context->id, 'block_quicklinks', 'logo', $recordid,
            ['maxbytes' => 1048576, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif']]);

        redirect(new moodle_url('/blocks/quicklinks/manage.php'),
            get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    // Display the add/edit form.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(($action === 'edit') ?
        get_string('editlink', 'block_quicklinks') :
        get_string('addlink', 'block_quicklinks'));
    $form->display();
    echo $OUTPUT->footer();
    exit;
}

// ---- MOVE UP / DOWN ----
// Swap the sortorder of the selected link with its immediate neighbour.
if ($action === 'moveup' || $action === 'movedown') {
    require_sesskey();
    $link = $DB->get_record('block_quicklinks_links', ['id' => $linkid], '*', MUST_EXIST);

    if ($action === 'moveup') {
		// Find the nearest link with a lower sortorder.
        $swap = $DB->get_records_select(
            'block_quicklinks_links',
            'sortorder < ?',
            [$link->sortorder],
            'sortorder DESC',
            '*',
            0,
            1
        );
    } else {
		// Find the nearest link with a higher sortorder.
        $swap = $DB->get_records_select(
            'block_quicklinks_links',
            'sortorder > ?',
            [$link->sortorder],
            'sortorder ASC',
            '*',
            0,
            1
        );
    }

    $swap = reset($swap);

    // Exchange sortorder values between the two links.
    if ($swap) {
        $oldsort = $link->sortorder;
        $DB->set_field('block_quicklinks_links', 'sortorder', $swap->sortorder, ['id' => $link->id]);
        $DB->set_field('block_quicklinks_links', 'sortorder', $oldsort, ['id' => $swap->id]);
    }

    redirect(new moodle_url('/blocks/quicklinks/manage.php'));
}

// ---- TOGGLE VISIBILITY ----
// Switch the visible flag between 0 and 1.
if ($action === 'togglevisibility') {
    require_sesskey();
    $link = $DB->get_record('block_quicklinks_links', ['id' => $linkid], '*', MUST_EXIST);
    $newvisible = $link->visible ? 0 : 1;
    $DB->set_field('block_quicklinks_links', 'visible', $newvisible, ['id' => $link->id]);
    redirect(new moodle_url('/blocks/quicklinks/manage.php'));
}


// ---- LIST ----
// Display all links in a table with action icons.
$links = $DB->get_records('block_quicklinks_links', null, 'sortorder ASC');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managelinks', 'block_quicklinks'));

// "Add link" button.
$addurl = new moodle_url('/blocks/quicklinks/manage.php', ['action' => 'add']);
echo html_writer::tag('a', get_string('addlink', 'block_quicklinks'), [
    'href' => $addurl->out(),
    'class' => 'btn btn-primary mb-3',
]);

if ($links) {
	// Build the links table.
    $table = new html_table();
    $table->head = [
        get_string('logo', 'block_quicklinks'),
        get_string('linklabel', 'block_quicklinks'),
        get_string('sourcetype', 'block_quicklinks'),
        get_string('linkurl', 'block_quicklinks'),
        get_string('visiblecolumn', 'block_quicklinks'),
        get_string('sortorder', 'block_quicklinks'),
		get_string('visible'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    $fs = get_file_storage();
    $count = count($links);
    $i = 0;

    foreach ($links as $link) {
    $i++;

    // Render a small logo thumbnail if a file exists.
    $logohtml = '';
    $files = $fs->get_area_files($context->id, 'block_quicklinks', 'logo', $link->id, 'sortorder', false);
    if ($files) {
        $file = reset($files);
        $logourl = moodle_url::make_pluginfile_url(
            $file->get_contextid(), $file->get_component(), $file->get_filearea(),
            $file->get_itemid(), $file->get_filepath(), $file->get_filename());
        $logohtml = html_writer::img($logourl->out(), $link->label, ['height' => 32]);
    }

    // Display the source type and custom field shortname if applicable.
    if ($link->source_type === 'customfield') {
        $sourceinfo = get_string('source_customfield', 'block_quicklinks') .
            ': ' . $link->customfield_shortname;
    } else {
        $sourceinfo = get_string('source_manual', 'block_quicklinks');
    }

    // Show the manual URL or a dash for custom field links.
    $urlinfo = ($link->source_type === 'manual') ? $link->manual_url : '-';

    // Visibility toggle icon and label
    $toggleurl = new moodle_url('/blocks/quicklinks/manage.php', [
        'action' => 'togglevisibility',
        'id' => $link->id,
        'sesskey' => sesskey(),
    ]);

    if ($link->visible) {
        $visibleicon = $OUTPUT->pix_icon('t/hide', get_string('hide'));
        $visibletext = get_string('visible');
    } else {
        $visibleicon = $OUTPUT->pix_icon('t/show', get_string('show'));
        $visibletext = get_string('hidden', 'block_quicklinks');
    }
    $visibletoggle = html_writer::tag('a', $visibleicon, ['href' => $toggleurl->out(false)]) . ' ' . $visibletext;

    // Action icons: edit, delete, move up, move down.
    $editurl = new moodle_url('/blocks/quicklinks/manage.php', ['action' => 'edit', 'id' => $link->id]);
    $deleteurl = new moodle_url('/blocks/quicklinks/delete.php', ['id' => $link->id, 'sesskey' => sesskey()]);
    $upurl = new moodle_url('/blocks/quicklinks/manage.php',
        ['action' => 'moveup', 'id' => $link->id, 'sesskey' => sesskey()]);
    $downurl = new moodle_url('/blocks/quicklinks/manage.php',
        ['action' => 'movedown', 'id' => $link->id, 'sesskey' => sesskey()]);

    $actions = html_writer::tag('a', $OUTPUT->pix_icon('t/edit', get_string('edit')),
            ['href' => $editurl->out(false)]) . ' ';
    $actions .= html_writer::tag('a', $OUTPUT->pix_icon('t/delete', get_string('delete')),
            ['href' => $deleteurl->out(false)]) . ' ';
    if ($i > 1) {
        $actions .= html_writer::tag('a', $OUTPUT->pix_icon('t/up', get_string('moveup')),
                ['href' => $upurl->out(false)]) . ' ';
    }
    if ($i < $count) {
        $actions .= html_writer::tag('a', $OUTPUT->pix_icon('t/down', get_string('movedown')),
                ['href' => $downurl->out(false)]);
    }

    $row = new html_table_row([
        $logohtml, $link->label, $sourceinfo, s($urlinfo), $link->sortorder, $visibletoggle, $actions
    ]);

    // Dim the row if the link is hidden.
    if (!$link->visible) {
        $row->attributes['class'] = 'dimmed_text';
    }

    $table->data[] = $row;
}

    echo html_writer::table($table);
} else {
	// Show an info notification if no links exist yet.
    echo $OUTPUT->notification(get_string('nolinks', 'block_quicklinks'), 'info');
}

echo $OUTPUT->footer();
