<?php

/*
  Plugin Name: Bukvycja
  Plugin URI:
  Description: Generation of different drop caps in the text.
  Version: 1.0.1
  Author: Pavlo Degtyaryov
  Author URI:
  License: GPLv2
  Text Domain: bukvycja
  Copyright (C) 2017 Pavlo Degtyaryov

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

trait DPDBukvycjaOptionsTrait {

    function getOptions() {
        $this->options = get_option('dpd_bukvycja_plugin');
        if (!is_array($this->options)) {
            $options = [
                'content' => true,
                'excerpts' => true,
                'comments' => false,
                //
                'mainblog' => true,
                'single' => true,
                'page' => true,
                'search' => true,
                'feed' => true,
                'attachment' => true,
                //
                'archive' => true,
                //
                'tag' => true,
                'category' => true,
                'date' => true,
                'author' => true,
                //
                'only_first_p' => true,
                'bukvycja_css' => true,
                //
                'capitalize' => '(Do not change)',
                'right_padding' => 1,
                'line_height' => 38,
                //
                'size' => 40,
                'use_color' => true,
                'color' => '#FF0000',
                'font' => '(Match Current Font)',
                'font_weight' => '(Do not change)',
                'font_url' => '',
                //
                'default_on_new' => 1,
                //
                'forbidden_cats' => array(),
                'forbidden_ids' => array()
            ];
            update_option('dpd_bukvycja_plugin', $options);
            $this->options = get_option('dpd_bukvycja_plugin');
        }
    }

}

if (is_admin()) {
    require_once 'includes/dpd-bukvycja-config.php';
} else {
    require_once 'includes/dpd-bukvycja-processor.php';
}


if (class_exists("DpdBukvycjaConfig")) {
    $dpd_bukvycja_plugin = new DpdBukvycjaConfig();
}

if (class_exists("DpdBukvycjaProc")) {
    $dpd_bukvycja_processor = new DpdBukvycjaProc();
}
