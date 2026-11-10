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
 * Provides a block that renders a list of external links in course pages.
 * Links are managed globally by administrators and can reference either
 * static URLs or dynamic values from custom course fields.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at/tlc}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_quicklinks extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_quicklinks');
    }

	// Apply instance-specific configuration after initialisation.
     	public function specialization() {
		$customtitle = get_config('block_quicklinks', 'blocktitle');
		if (!empty($customtitle)) {
			$this->title = $customtitle;
		}
	}
	
	// Define where this block can be used.
    public function applicable_formats() {
        return [
            'course-view' => true,
            'site' => false,
            'my' => false,
        ];
    }

    // Indicate that this block has global configuration settings.
    public function has_config() {
        return true;
    }

	/**
     * Generate and return the block content.
     *
     * Fetches all visible links from the database, resolves their URLs
     * (either from manual input or custom course fields), and renders
     * each link with its associated logo or a default fallback icon.
     *
     * Links that cannot be resolved to a valid URL are silently skipped.
     * The block displays nothing if no visible links exist or if the
     * block is placed outside a course context.
     *
     * @return stdClass The block content object with 'text' and 'footer' properties.
     */
    public function get_content() {
        global $DB;

        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        $course = $this->page->course;
        if (!$course || $course->id == SITEID) {
            return $this->content;
        }
        $links = $DB->get_records('block_quicklinks_links', ['visible' => 1], 'sortorder ASC');
        if (empty($links)) {
            return $this->content;
        }
        $context = \context_system::instance();
        $fs = get_file_storage();
        $output = '';
        $hasvisiblelinks = false;
        foreach ($links as $link) {
            $url = $this->resolve_url($link, $course->id);
            if (empty($url)) {
                continue;
            }
            $hasvisiblelinks = true;
            // find logo
            $logohtml = '';
            $files = $fs->get_area_files($context->id, 'block_quicklinks', 'logo', $link->id, 'sortorder', false);
            if ($files) {
                $file = reset($files);
                $logourl = \moodle_url::make_pluginfile_url(
                    $file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                $logohtml = html_writer::img($logourl->out(), s($link->label), [
                    'class' => 'block_quicklinks-logo',
                ]);
            } else {
                // Fallback: Default Logo (Fontawesome Icon fa-link)
                $defaulturl = new \moodle_url('/blocks/quicklinks/pix/link-solid-full.svg');
                $logohtml = html_writer::img($defaulturl->out(), s($link->label), [
                    'class' => 'block_quicklinks-logo',
                ]);
            }

            $output .= html_writer::div(
                $logohtml .
                html_writer::tag('a', s($link->label), [
                    'href' => $url,
                    'target' => '_blank',
                    'rel' => 'noopener noreferrer',
                    'class' => 'block_quicklinks-btn',
                ]),
                'block_quicklinks-item'
            );
        }

        if ($hasvisiblelinks) {
            $this->content->text = html_writer::div($output, 'block_quicklinks-wrapper');
        }

        return $this->content;
    }

    // Resolve the target URL for a given link record.
    private function resolve_url(object $link, int $courseid): ?string {
        if ($link->source_type === 'manual') {
            $url = trim($link->manual_url ?? '');
            return (!empty($url) && filter_var($url, FILTER_VALIDATE_URL)) ? $url : null;
        }

        if ($link->source_type === 'customfield') {
            return $this->get_custom_field_value($courseid, $link->customfield_shortname);
        }

        return null;
    }

    // Retrieve a URL from a custom course field.
    private function get_custom_field_value(int $courseid, string $shortname): ?string {
        $handler = \core_customfield\handler::get_handler('core_course', 'course');
        $datas = $handler->get_instance_data($courseid, true);

        foreach ($datas as $data) {
            $field = $data->get_field();
            if ($field->get('shortname') === $shortname) {
                $value = $data->get_value();
                if (is_string($value) && !empty(trim($value))) {
                    $trimmed = trim($value);
                    return filter_var($trimmed, FILTER_VALIDATE_URL) ? $trimmed : null;
                }
                $exportvalue = $data->export_value();
                if (is_string($exportvalue)) {
                    if (preg_match('/href=["\']([^"\']+)["\']/', $exportvalue, $matches)) {
                        return trim($matches[1]);
                    }
                    $trimmed = trim($exportvalue);
                    return filter_var($trimmed, FILTER_VALIDATE_URL) ? $trimmed : null;
                }
                return null;
            }
        }
        return null;
    }

	// Determine whether the block header should be hidden.
    public function hide_header() {
        return false;
    }

    // Determine whether multiple instances of this block are allowed per page.
    public function instance_allow_multiple() {
        return false;
    }
}
