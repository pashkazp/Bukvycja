<?php

if (!class_exists("DpdBukvycjaProc")) {

    class DpdBukvycjaProc {

        private $options;

        use DPDBukvycjaOptionsTrait;

        public function __construct() {
            $this->getOptions();

            add_action('wp_enqueue_scripts', [$this, 'dpd_bukvycja_processor_head']);
            add_filter('the_content', [$this, 'content_filter']);
            add_filter('the_excerpt', [$this, 'excerpt_filter']);
            add_filter('comment_text', [$this, 'comment_filter']);
        }

        public function dpd_bukvycja_processor_head() {
            if ($this->options['bukvycja_css'] == false) {
                wp_enqueue_style('dpd-bukvicja-style', site_url() . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__DIR__)) . '/css/bukvycja.css');
                return;
            }

            if ($this->options['font'] == "DPD-Bukvycja") {
                $dpd_bukvycja_font_face_style = ""
                        . "@font-face {\n"
                        . "    font-family: DPD-Bukvycja;\n"
                        . "    src: url('" . $this->options['font_url'] . "');\n"
                        . "}";
                wp_register_style('dpd-bukvycja-font-face-style', false);
                wp_enqueue_style('dpd-bukvycja-font-face-style');
                wp_add_inline_style('dpd-bukvycja-font-face-style', $dpd_bukvycja_font_face_style);
            }

            $firstP = ($this->options['only_first_p']) ? ':first-of-type' : '';
            $capDPD = $this->options['capitalize'] != "(Do not change)" ? "    text-transform: " . $this->options['capitalize'] . ";\n" : "";
            $fontDPD = $this->options['font'] != "(Match Current Font)" ? "    font-family:'" . $this->options['font'] . "';\n" : "";
            $colorDPD = $this->options['use_color'] ? "    color: " . $this->options['color'] . ";\n" : "";
            $fontweightDPD = $this->options['font_weight'] != "(Do not change)" ? "    font-weight:" . $this->options['font_weight'] . ";\n" : "";

            $dpd_bukvycja_letter_style = ""
                    . ".bukvycja-letter p" . $firstP . ":first-letter,\n"
                    . ".bukvycja-letter h1" . $firstP . ":first-letter,\n"
                    . ".bukvycja-letter h2" . $firstP . ":first-letter,\n"
                    . ".bukvycja-letter h3" . $firstP . ":first-letter,\n"
                    . ".bukvycja-letter h4" . $firstP . ":first-letter {\n"
                    . $capDPD
                    . $fontDPD
                    . $colorDPD
                    . $fontweightDPD
                    . "    font-size:" . $this->options['size'] . "px;\n"
                    . "    float:left;\n"
                    . "    line-height:" . $this->options['line_height'] . "px;\n"
                    . "    padding-right:" . $this->options['right_padding'] . "px;\n"
                    . "}";
            wp_register_style('dpd_bukvycja_letter_style', false);
            wp_enqueue_style('dpd_bukvycja_letter_style');
            wp_add_inline_style('dpd_bukvycja_letter_style', $dpd_bukvycja_letter_style);
        }

        function content_filter($content = '') {
            if ($this->options['content'] && $this->is_applicable() && $this->is_admitted()) {
                $content = $this->process_text($content);
            }
            return $content;
        }

        function comment_filter($comment = '') {
            if ($this->options['comments'] && $this->is_admitted()) {
                $comment = $this->process_text($comment);
            }
            return $comment;
        }

        function excerpt_filter($excerpt = '') {
            if ($this->options['excerpts'] && $this->is_applicable() && $this->is_admitted()) {
                $excerpt = $this->process_text($excerpt);
            }
            return $excerpt;
        }

        function is_admitted() { // is admitted Categories, ID`s, Titles, Names, etc.
            global $post;

            $enable = get_post_meta($post->ID, 'dpd_bukvycja_enable', TRUE);
            if (!((int) $enable === 1 || empty($enable) && (int) $this->options['default_on_new'] === 1)) {
                return false;
            }

            $forbidden_ids = $this->options['forbidden_ids'];
            $id = $post->ID;
            if (in_array($id, $forbidden_ids)) {
                return false;
            }
            $title = $post->post_title;
            if (in_array($title, $forbidden_ids)) {
                return false;
            }
            $name = $post->post_name;
            if (in_array($name, $forbidden_ids)) {
                return false;
            }
            $forbidden_cats = $this->options['forbidden_cats'];
            $categories = get_the_category();
            foreach ($categories as $category) {
                $cat_id = $category->term_id;
                if (in_array($cat_id, $forbidden_cats)) {
                    return false;
                }
                $cat_slug = $category->slug;
                if (in_array($cat_slug, $forbidden_cats)) {
                    return false;
                }
                $cat_name = $category->name;
                if (in_array($cat_name, $forbidden_cats)) {
                    return false;
                }
            }
            return true;
        }

        function is_applicable() { // is appicable places of filtering
            return (is_home() && $this->options['mainblog']) ||
                    (is_single() && $this->options['single']) ||
                    (is_page() && $this->options['page']) ||
                    (is_archive() && $this->options['archive']) ||
                    (is_search() && $this->options['search']) ||
                    (is_feed() && $this->options['feed']) ||
                    (is_attachment() && $this->options['attachment']) ||
                    (is_tag() && $this->options['tag']) ||
                    (is_category() && $this->options['category']) ||
                    (is_date() && $this->options['date']) ||
                    (is_author() && $this->options['author']);
        }

        function process_text($rawtext = '') {
            $xml = new DOMDocument();
            $xml->preserveWhiteSpace = false;
            $xml->loadHTML(mb_convert_encoding($rawtext, 'HTML-ENTITIES', 'UTF-8'));
            $xml->strictErrorChecking = false;

            $xpath = new DOMXPath($xml);
            $elements = $xpath->query('//p | //h1 | //h2 | //h3 | //h4');
            if ($elements->length > 0) {
                $bukvycja_div = $xml->createElement('div');
                $bukvycja_div->setAttribute('class', 'bukvycja-letter');
                foreach ($elements as $element) {
                    $bukvycja_div_clone = $bukvycja_div->cloneNode();
                    $element->parentNode->replaceChild($bukvycja_div_clone, $element);
                    $bukvycja_div_clone->appendChild($element);
                    if ($this->options['only_first_p']) {
                        break;
                    }
                }
            }
            return substr($xml->saveXML($xml->getElementsByTagName('body')->item(0)), 6, -7);
        }

    }

}
