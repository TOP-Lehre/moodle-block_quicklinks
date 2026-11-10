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
 * Form for adding and editing Quick Links entries.
 * Provides fields for the link label, source type (manual URL or custom
 * course field), an optional logo upload and the sort order. Form fields
 * are shown or hidden dynamically based on the selected source type.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quicklinks\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

// Moodle form for creating and editing a single quick link.
class edit_link extends \moodleform {

    // Define the form elements.
    protected function definition() {
        $mform = $this->_form;

        // Hidden field for the link record ID (0 for new entries).
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Link label displayed in the block.
        $mform->addElement('text', 'label', get_string('linklabel', 'block_quicklinks'));
        $mform->setType('label', PARAM_TEXT);
        $mform->addRule('label', null, 'required', null, 'client');

        // Source type: determines whether the URL is entered manually or read from a custom field.
        $sourceoptions = [
            'manual' => get_string('source_manual', 'block_quicklinks'),
            'customfield' => get_string('source_customfield', 'block_quicklinks'),
        ];
        $mform->addElement('select', 'source_type', get_string('sourcetype', 'block_quicklinks'), $sourceoptions);
        $mform->setDefault('source_type', 'manual');

        // Manual URL input (only visible when source type is 'manual').
        $mform->addElement('text', 'manual_url', get_string('manualurl', 'block_quicklinks'), ['size' => 80]);
        $mform->setType('manual_url', PARAM_URL);
        $mform->hideIf('manual_url', 'source_type', 'neq', 'manual');

        // Custom course field selector (only visible when source type is 'customfield').
        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        $fields = $handler->get_fields();
        $fieldoptions = ['' => get_string('choosefield', 'block_quicklinks')];
        foreach ($fields as $field) {
            $fieldoptions[$field->get('shortname')] = $field->get('name') . ' (' . $field->get('shortname') . ')';
        }
        $mform->addElement('select', 'customfield_shortname',
            get_string('customfield', 'block_quicklinks'), $fieldoptions);
        $mform->hideIf('customfield_shortname', 'source_type', 'neq', 'customfield');

        // Optional logo upload (max 1 MB, common image formats).
        $mform->addElement('filepicker', 'logo',
            get_string('logo', 'block_quicklinks'), null,
            [
                'maxbytes' => 1048576, // 1 MB
                'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.gif'],
            ]
        );
        $mform->addHelpButton('logo', 'logo', 'block_quicklinks');

        // Sort order for controlling the display position in the block.
        $mform->addElement('text', 'sortorder', get_string('sortorder', 'block_quicklinks'));
        $mform->setType('sortorder', PARAM_INT);
        $mform->setDefault('sortorder', 0);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

     // Validate the submitted form data.
     // Ensures that a manual URL is provided when the source type is 'manual',
     // and that a custom field is selected when the source type is 'customfield'.
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['source_type'] === 'manual' && empty(trim($data['manual_url'] ?? ''))) {
            $errors['manual_url'] = get_string('error_manualurl_required', 'block_quicklinks');
        }

        if ($data['source_type'] === 'customfield' && empty($data['customfield_shortname'])) {
            $errors['customfield_shortname'] = get_string('error_customfield_required', 'block_quicklinks');
        }

        return $errors;
    }
}
