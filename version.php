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
 * Version information for block quicklinks
 * Open README.md and CHANGES.md for mor details
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at/tlc}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$plugin->component = 'block_quicklinks';
$plugin->version = 2026070701;
$plugin->requires = 2024100700; // Moodle 4.5 Minimum
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '1.0.1';
$plugin->supported = [405, 502];   // compatible with Moodle 4.5 - 5.2.
