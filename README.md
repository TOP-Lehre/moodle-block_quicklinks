# Quicklinks Block

## Requirements and supported Moodle versions

* Moodle 4.5 (LTS)
* Moodle 5.0
* Moodle 5.1
* Moodle 5.2

## Version info

* Open README.md for more details

## Description

This plugin offers the possibility to show quick links as a block in courses that could be relevant for students (e.g. link to grading system, link to timetable, link to MS-Teams, etc.).

* quick links are managed in the backend by the Moodle administrators
* provide links manually as well as links which are stored in custom course fields
* upload individual link icons or use the standard link icon of the plugin
* links can be sorted and hidden
* by default, links are marked as visible & hidden
* normally, uncorrect links and empty custom course fields of a course are not shown in the block of a course. 
* links must start with "https"
* links in custom course fields types can be set as "short text" and also must start with "https"
* Moodle administrators can change the name of the block title in the course (standard is "Quick Links")
* Teachers can add/remove the quick links block in each course

## Installation

Download the ZIP-File. Maybe you have to rename the root folder from moodle-block_quicklinks to quicklinks.
Install the plugin to folder /blocks/quicklinks.
See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins.


## Usage & Settings

After installing the plugin, first Moodle administrators have to set the links in the backend. After that teachers can add this block to their courses. When a block is added, the (visible and correct) links are shown.
If there is a link which refers to an empty custom course field, this link will not shown.

## Plugin repositories, bugs and problem reports

We have reviewed the plugin. However, bugs or errors can still occur. You can report bugs here: https://github.com/tlcfhooe/moodle-block_quicklinks/issues. However, please note that due to limited resources, we cannot respond immediately to requests and we cannot consider all feature requests.

Have a look at CHANGES.md to see the release notes.

## Author infos

The plugin "block_quicklinks" was created with the help of Claude Opus 4.6. in July/August 2026 by Teaching and Learning Center (TLC, tlc@fh-ooe.at), FH Upper Austria

* Copyright: 2026 Teaching and Learning Center, FH Upper Austria (http://www.fh-ooe.at)
* License: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
