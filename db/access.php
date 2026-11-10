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
 * Capability definitions for the Quick Links block.
 * Defines three capabilities:
 * - addinstance:    Allows adding the block to a course page.
 * - myaddinstance:  Controls adding the block to the My Moodle page (disabled).
 * - managelinks:    Allows managing the global link list via the admin interface.
 *
 * @package   block_quicklinks
 * @author    Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria
 * @copyright 2026 Teaching and Learning Center, FH Upper Austria {@link http://www.fh-ooe.at}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    // Capability to add a Quick Links block instance to a course page.
    'block/quicklinks:addinstance' => [
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
        'clonepermissionsfrom' => 'moodle/site:manageblocks',
    ],
    // Capability to add the block to the My Moodle dashboard (intentionally empty).
	'block/quicklinks:myaddinstance' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [],
    ],
    // Capability to manage the global list of quick links (add, edit, delete, reorder).   
   'block/quicklinks:managelinks' => [
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ],
    ],
];
