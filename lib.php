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
 * Serve files for the Quick Links block.
 * This callback is invoked by Moodle's pluginfile.php when a file
 * belonging to the block_quicklinks component is requested. It handles
 * only the 'logo' file area and delivers the stored logo image.
 * @param stdClass       $course           The course object (unused but required by API).
 * @param stdClass       $birecord_or_cm   The block instance record or course module (unused).
 * @param context        $context          The context from which the file is requested.
 * @param string         $filearea         The file area name (must be 'logo').
 * @param array          $args             The remaining URL path segments (itemid, filepath, filename).
 * @param bool           $forcedownload    Whether to force the browser to download the file.
 * @param array          $options          Additional options for file delivery.
 * @return bool|void     False if the file area is invalid or the file is not found.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at/tlc}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function block_quicklinks_pluginfile($course, $birecord_or_cm, $context, $filearea,
        $args, $forcedownload, array $options = []) {

    // Only serve files from the 'logo' file area.
    if ($filearea !== 'logo') {
        return false;
    }

    // Extract item ID, filename and filepath from the URL arguments.
    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    // Retrieve the file from Moodle's file storage.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'block_quicklinks', $filearea, $itemid, $filepath, $filename);

    if (!$file) {
        return false;
    }

    // Send the file to the browser.
    send_stored_file($file, 0, 0, $forcedownload, $options);
}
