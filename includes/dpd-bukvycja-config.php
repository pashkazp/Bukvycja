<?php
if (!class_exists("DpdBukvycjaConfig")) {

    class DpdBukvycjaConfig {

        private $options;

        use DPDBukvycjaOptionsTrait;

        public function __construct() {
            $this->getOptions();

            add_action('admin_menu', [$this, 'add_support_page']);
            add_action('admin_init', [$this, 'adminInit']);
        }

        function add_support_page() {
            add_options_page(__('Bukvycja', 'bukvycja'), __('Bukvycja', 'bukvycja'), 'manage_options', 'dpd-bukvycja', [$this, 'optionsPageContent']);
        }

        function optionsPageContent() {
            $this->printAdminHeader();
            ?>
            <form method="post" action="options.php">
                <?php
                settings_fields('dpd_bukvycja_plugin');
                do_settings_sections('dpd_bukvycja_plugin');
                submit_button();
                ?>
            </form>
            <?php
        }

        function printAdminHeader() {

            echo '<h2>' . esc_html__('Bukvycja Plugin', 'bukvycja') . '</h2>';
            echo '<h3>' . esc_html__('Options', 'bukvycja') . '</h3>';
            echo '<p>' . esc_html__('Set the options which suit your blog. You can choose whether Bukvycja (drop caps, up caps) will appear in the content, the excerpt or in comments. You can also choose which pages Bukvycja will appear on.', 'bukvycja') . '</p>';
            echo '<p>' . esc_html__('The plugin comes with a dynamic stylesheet that applies to Bukvycja. This can be turned off if you want to use the plugin style file.', 'bukvycja') . '</p>';
        }

        function adminInit() {
            add_action('admin_enqueue_scripts', [$this, 'adminScripts']);

            add_action('add_meta_boxes', [$this, 'metaBox'], 1, 2);
            add_action('save_post', [$this, 'savePost']);

            register_setting('dpd_bukvycja_plugin', 'dpd_bukvycja_plugin', [$this, 'sanitize']);

            add_settings_section('dpd_bukvycja_plugin_options', __('Enable Bukvycja in', 'bukvycja'), [$this, 'optionsPageInSectionText'], 'dpd_bukvycja_plugin');
            add_settings_field('dpd_bukvycja_plugin_content', __('Content', 'bukvycja'), [$this, 'enableForContent'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options', [__('Check to enable Bukvycja for content.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_excerpts', __('Excerpts', 'bukvycja'), [$this, 'enableForExcerpts'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options', [__('Check to enable Bukvycja for excerpts.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_comments', __('Comments', 'bukvycja'), [$this, 'enableForComments'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options', [__('Check to enable Bukvycja for comments.', 'bukvycja')]);

            add_settings_section('dpd_bukvycja_plugin_options2', __('Enable Bukvycja on', 'bukvycja'), [$this, 'optionsPageOnSectionText'], 'dpd_bukvycja_plugin');
            add_settings_field('dpd_bukvycja_plugin_mainblog', __('Main page', 'bukvycja'), [$this, 'enableForMainBlog'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for main page.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_single', __('Main single post pages', 'bukvycja'), [$this, 'enableForSingle'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for single post pages.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_page', __('PAGE pages', 'bukvycja'), [$this, 'enableForPAGE'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for PAGE pages.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_search', __('Search results', 'bukvycja'), [$this, 'enableForSearch'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for search results.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_feed', __('Feeds', 'bukvycja'), [$this, 'enableForFeeds'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for feeds.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_attachment', __('Attachment', 'bukvycja'), [$this, 'enableForAttacment'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for attachments','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_allarc', __('All archives', 'bukvycja'), [$this, 'enableForAllArc'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for all archives','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_arctag', __('Tag archives', 'bukvycja'), [$this, 'enableForArcTag'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for tag archives','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_arccat', __('Category archives', 'bukvycja'), [$this, 'enableForArcCat'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for category archives','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_arcdate', __('Date archives', 'bukvycja'), [$this, 'enableForArcDate'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for date archives','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_arcauth', __('Author archives', 'bukvycja'), [$this, 'enableForArcAuth'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options2', [__('Check to enable Bukvycja for author archives','bukvycja')]);

            add_settings_section('dpd_bukvycja_plugin_options3', __('Exclude', 'bukvycja'), [$this, 'optionsExcludeSectionText'], 'dpd_bukvycja_plugin');
            add_settings_field('dpd_bukvycja_plugin_forbidden_ids', __('Excluded posts', 'bukvycja'), [$this, 'forbiddenIds'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options3', [__('These IDs will be excluded from processing.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_forbidden_cats', __('Excluded categories', 'bukvycja'), [$this, 'forbiddenCats'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options3', [__('These Categories will be excluded from processing.','bukvycja')]);

            add_settings_section('dpd_bukvycja_plugin_options4', __('Additional options', 'bukvycja'), [$this, 'additionalOptions'], 'dpd_bukvycja_plugin');
            add_settings_field('dpd_bukvycja_plugin_default_on_new', __('Default on New Posts', 'bukvycja'), [$this, 'defaultOnNew'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options4', [__('Choose whether Bukvycja is required by default in new messages. You can change this for each post in the post editor.','bukvycja')]);

            add_settings_section('dpd_bukvycja_plugin_options5', __('Bukvycja Style', 'bukvycja'), [$this, 'optionsPageStyleSectionText'], 'dpd_bukvycja_plugin');
            add_settings_field('dpd_bukvycja_plugin_only_first', __('Only first', 'bukvycja'), [$this, 'onlyFirstParagraph'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('If checked, only the first corresponding character of the message will be changed to Bukvycja.','bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_css_styling', __('CSS Styling', 'bukvycja'), [$this, 'enableCSS'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('The style of the Bukvycja is determined by the style that is configured here. Uncheck to activate the plugin style file. The required content will be tagged by','bukvycja') . ' <strong><code>&lt;div class="bukvycja-letter"&gt;&lt;/div&gt;</code></strong>.']);
            add_settings_field('dpd_bukvycja_plugin_capitalize', __('Capitalize', 'bukvycja'), [$this, 'capitalizeFirst'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('Specify how the character case will be changed.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_right_padding', __('Right Padding', 'bukvycja'), [$this, 'fontPaddingSelect'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('If the Bukvycja is placed far or close, try changing the size of the space to the right of it.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_line_height', __('Vertical Alignment', 'bukvycja'), [$this, 'fontLineHeightSelect'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('If the Bukvycja looks too high, try increasing the value of this option to make it a bit lower.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_fontsize', __('Font Size', 'bukvycja'), [$this, 'fontSizeSelect'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('Select the font size you want.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_use_color', __('Use Color', 'bukvycja'), [$this, 'useColor'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('If checked, the color will be changed.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_color', __('Color', 'bukvycja'), [$this, 'fontColorSelect'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('Choose the color to your liking.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_font_weight', __('Font Weight', 'bukvycja'), [$this, 'fontWeight'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('Select the font weight.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_font', __('Font Family', 'bukvycja'), [$this, 'fontFamilySelect'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('Select a font family to display Bukvycja.', 'bukvycja')]);
            add_settings_field('dpd_bukvycja_plugin_font_file', __('Font URL', 'bukvycja'), [$this, 'fontURL'], 'dpd_bukvycja_plugin', 'dpd_bukvycja_plugin_options5', [__('This font file will be used as a font for the DPD-Bukvicja family.', 'bukvycja')]);


            // Add settings link on plugin page
            add_filter('plugin_action_links_' . plugin_dir_path(plugin_basename(__DIR__)) . 'dpd-bukvycja.php', function ($links) {
                $links[] = '<a href="' . get_admin_url() . 'options-general.php?page=dpd-bukvycja">'.esc_html__('Settings', 'bukvycja').'</a>';
                return $links;
            });
        }

        public function adminScripts() {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('dpd_bukvycja_js', plugins_url('js/dpd-bukvycja-scripts.js', plugin_dir_path(__FILE__)), ['wp-color-picker'], FALSE, TRUE);
        }

        public function metaBox($post_type, $post) {
            add_meta_box('dpd-bukvycja-meta-box', __('Bukvycja','bukvycja'), [$this, 'addMetaBox']);
        }

        public function addMetaBox($post) {
            $enable = get_post_meta($post->ID, 'dpd_bukvycja_enable', TRUE);

            if (empty($enable) && ((int) $this->options['default_on_new'] === 1 || empty($this->options['default_on_new']))) {
                $enable = 1;
            }

            wp_nonce_field('dpd_bukvycja_nonce', 'dpd_bukvycja_nonce');
            ?>
            <select name="dpd_bukvycja_enable">
                <option value="1" <?php echo (int) $enable === 1 ? 'selected="selected"' : ''; ?>><?php _e('Yes','bukvycja'); ?></option>
                <option value="0" <?php echo (int) $enable === 0 ? 'selected="selected"' : ''; ?>><?php _e('No','bukvycja'); ?></option>
            </select>
            <p><a href="<?php echo get_admin_url(); ?>options-general.php?page=dpd-bukvycja" target="_blank"><?php _e('Adjust global settings','bukvycja'); ?></a></p>
            <?php
        }

        public function savePost($post_id) {
            if (wp_verify_nonce($_POST['dpd_bukvycja_nonce'], 'dpd_bukvycja_nonce')) {
                update_post_meta($post_id, 'dpd_bukvycja_enable', $_POST['dpd_bukvycja_enable']);
            }
        }

        public function sanitize($input) {
            $inputs = array_map('strip_tags', $input);

            $inputs['content'] = $inputs['content'] === 'true';
            $inputs['excerpts'] = $inputs['excerpts'] === 'true';
            $inputs['comments'] = $inputs['comments'] === 'true';
            $inputs['mainblog'] = $inputs['mainblog'] === 'true';
            $inputs['single'] = $inputs['single'] === 'true';
            $inputs['page'] = $inputs['page'] === 'true';
            $inputs['search'] = $inputs['search'] === 'true';
            $inputs['feed'] = $inputs['feed'] === 'true';
            $inputs['attachment'] = $inputs['attachment'] === 'true';
            $inputs['archive'] = $inputs['tag'] && $inputs['category'] && $inputs['date'] && $inputs['author'];
            $inputs['tag'] = $inputs['tag'] === 'true';
            $inputs['category'] = $inputs['category'] === 'true';
            $inputs['date'] = $inputs['date'] === 'true';
            $inputs['author'] = $inputs['author'] === 'true';
            $inputs['bukvycja_css'] = $inputs['bukvycja_css'] === 'true';
            $inputs['only_first_p'] = $inputs['only_first_p'] === 'true';

            $inputs['capitalize'] = $inputs['capitalize'];
            $inputs['right_padding'] = (int) $inputs['right_padding'];
            $inputs['line_height'] = (int) $inputs['line_height'];

            $inputs['font'] = $inputs['font'];
            $inputs['size'] = (int) $inputs['size'];
            $inputs['use_color'] = $inputs['use_color'] === 'true';
            $inputs['font_weight'] = $inputs['font_weight'];
            $inputs['font_url'] = $inputs['font_url'];

            $inputs['default_on_new'] = (int) $inputs['default_on_new'];

            $inputs['forbidden_cats'] = explode(',', $inputs['forbidden_cats']);
            $inputs['forbidden_ids'] = explode(',', $inputs['forbidden_ids']);

            return $inputs;
        }

        public function optionsPageInSectionText() {
            echo '<p>' . esc_html__('You can choose whether Bukvycja will appear in the content, the excerpt or in comments.', 'bukvycja') . '</p>';
        }

        public function enableForContent($args) {
            ?>
            <label for="content_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[content]" id="content_enable" value="true" <?php echo $this->options['content'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForExcerpts($args) {
            ?>
            <label for="excerpts_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[excerpts]" id="excerpts_enable" value="true" <?php echo $this->options['excerpts'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForComments($args) {
            ?>
            <label for="comments_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[comments]" id="comments_enable" value="true" <?php echo $this->options['comments'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function optionsPageOnSectionText() {
            echo '<p>' . esc_html__('You can also choose which pages Bukvycja will appear on.', 'bukvycja') . '</p>';
        }

        public function enableForMainBlog($args) {
            ?>
            <label for="mainblog_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[mainblog]" id="mainblog_enable" value="true" <?php echo $this->options['mainblog'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForSingle($args) {
            ?>
            <label for="single_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[single]" id="single_enable" value="true" <?php echo $this->options['single'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForPAGE($args) {
            ?>
            <label for="page_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[page]" id="page_enable" value="true" <?php echo $this->options['page'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForSearch($args) {
            ?>
            <label for="search_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[search]" id="search_enable" value="true" <?php echo $this->options['search'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForFeeds($args) {
            ?>
            <label for="feed_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[feed]" id="feed_enable" value="true" <?php echo $this->options['feed'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForAttacment($args) {
            ?>
            <label for="attachment_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[attachment]" id="attachment_enable" value="true" <?php echo $this->options['attachment'] ? 'checked="checked"' : ''; ?>/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForAllArc($args) {
            ?>
            <label for="allarc_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[archive]" id="allarc_enable" value="true" <?php echo $this->options['archive'] ? 'checked="checked"' : ''; ?> onchange="checkAllArcElements(this.value)" />
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableForArcTag($args) {
            ?>
            <div style="margin-left:2em; ">
                <label for="arctag_enable">
                    <input type="checkbox" name="dpd_bukvycja_plugin[tag]" id="arctag_enable" value="true" <?php echo $this->options['tag'] ? 'checked="checked"' : ''; ?> onchange="allArcCheck()" />
                    <?php echo $args[0] ?>
                </label>
            </div>
            <?php
        }

        public function enableForArcCat($args) {
            ?>
            <div style="margin-left:2em;">
                <label for="arccat_enable">
                    <input type="checkbox" name="dpd_bukvycja_plugin[category]" id="arccat_enable" value="true" <?php echo $this->options['category'] ? 'checked="checked"' : ''; ?> onchange="allArcCheck()" />
                    <?php echo $args[0] ?>
                </label>
            </div>
            <?php
        }

        public function enableForArcDate($args) {
            ?>
            <div style="margin-left:2em;">
                <label for="arcdate_enable">
                    <input type="checkbox" name="dpd_bukvycja_plugin[date]" id="arcdate_enable" value="true" <?php echo $this->options['date'] ? 'checked="checked"' : ''; ?> onchange="allArcCheck()" />
                    <?php echo $args[0] ?>
                </label>
            </div>
            <?php
        }

        public function enableForArcAuth($args) {
            ?>
            <div style="margin-left:2em;">
                <label for="arcauth_enable">
                    <input type="checkbox" name="dpd_bukvycja_plugin[author]" id="arcauth_enable" value="true" <?php echo $this->options['author'] ? 'checked="checked"' : ''; ?> onchange="allArcCheck()" />
                    <?php echo $args[0] ?>
                </label>
            </div>
            <?php
        }

        public function optionsExcludeSectionText() {
            echo '<p>' . esc_html__('Type the comma seperated names, slugs or IDs of the categories or posts that you want to exclude from having Bukvycja.', 'bukvycja') . '</p>';
            echo '<p>' . esc_html__('If you have Bukvycja enabled on comments, these will show up wether or not the post is excluded.', 'bukvycja') . '</p>';
        }

        public function forbiddenIds($args) {
            ?>
            <label for="forbidden_ids">
                <input type="text" name="dpd_bukvycja_plugin[forbidden_ids]" id="forbidden_ids" style="width:600px;" value="<?php
                if ($this->options['forbidden_ids']) {
                    echo implode(',', $this->options['forbidden_ids']);
                }
                ?>"/>
                       <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function forbiddenCats($args) {
            ?>
            <label for="forbidden_cats">
                <input type="text" name="dpd_bukvycja_plugin[forbidden_cats]" id="forbidden_cats" style="width:600px;" value="<?php
                if ($this->options['forbidden_cats']) {
                    echo implode(',', $this->options['forbidden_cats']);
                }
                ?>"/>
                       <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function additionalOptions() {
            echo '<p>' . esc_html__('Select whether you want Bukvycja to default on new posts.', 'bukvycja') . '</p>';
        }

        public function defaultOnNew($args) {
            ?>
            <label for="dpd_bukvycja_plugin[default_on_new]">
                <select name="dpd_bukvycja_plugin[default_on_new]" id="dpd_bukvycja_plugin[default_on_new]">
                    <option value="1" <?php echo (int) $this->options['default_on_new'] === 1 ? 'selected="selected"' : ''; ?>><?php _e('Yes','bukvycja'); ?></option>
                    <option value="0" <?php echo (int) $this->options['default_on_new'] === 0 ? 'selected="selected"' : ''; ?>><?php _e('No','bukvycja'); ?></option>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function optionsPageStyleSectionText() {
            echo '<p>' . esc_html__('You can customize Bukvycja Style to your liking.', 'bukvycja') . '</p>';
        }

        public function onlyFirstParagraph($args) {
            ?>
            <label for="only_first_p">
                <input type="checkbox" name="dpd_bukvycja_plugin[only_first_p]" id="only_first_p" value="true" <?php echo $this->options['only_first_p'] ? 'checked="checked"' : '';
            ?>/><?php echo $args[0] ?>
            </label>
            <?php
        }

        public function enableCSS($args) {
            ?>
            <label for="css_enable">
                <input type="checkbox" name="dpd_bukvycja_plugin[bukvycja_css]" id="css_enable" value="true" <?php echo $this->options['bukvycja_css'] ? 'checked="checked"' : ''; ?> />
                <?php echo $args[0] ?>
            </label>
            <?php
        }

        public function capitalizeFirst($args) {
            $variants = ['(Do not change)', 'capitalize', 'lowercase'];
            ?>
            <label for="dpd_bukvycja_plugin[capitalize]">
                <select name="dpd_bukvycja_plugin[capitalize]" id="dpd_bukvycja_plugin[capitalize]">
                    <?php foreach ($variants as $variant): ?>
                        <option value="<?php echo $variant; ?>" <?php echo $variant == $this->options['capitalize'] ? 'selected="selected"' : ''; ?>><?php echo $variant; ?></option>
                    <?php endforeach ?>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function fontPaddingSelect($args) {
            $values = range(0, 120);
            ?>
            <label for="dpd_bukvycja_plugin[right_padding]">
                <select name="dpd_bukvycja_plugin[right_padding]" id="dpd_bukvycja_plugin[right_padding]">
                    <?php foreach ($values as $value): ?>
                        <option value="<?php echo $value; ?>" <?php echo $value == $this->options['right_padding'] ? 'selected="selected"' : ''; ?>><?php echo $value; ?>px</option>
                    <?php endforeach; ?>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function fontLineHeightSelect($args) {
            $values = range(0, 120);
            ?>
            <label for="dpd_bukvycja_plugin[line_height]">
                <select name="dpd_bukvycja_plugin[line_height]" id="dpd_bukvycja_plugin[line_height]">
                    <?php foreach ($values as $value): ?>
                        <option value="<?php echo $value; ?>" <?php echo $value == $this->options['line_height'] ? 'selected="selected"' : ''; ?>><?php echo $value; ?>px</option>
                    <?php endforeach; ?>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function fontSizeSelect($args) {
            $values = range(1, 120);
            ?>
            <label for="dpd_bukvycja_plugin[size]">
                <select name="dpd_bukvycja_plugin[size]" id="dpd_bukvycja_plugin[size]" >
                    <?php foreach ($values as $value): ?>
                        <option value="<?php echo $value; ?>" <?php echo $value == $this->options['size'] ? 'selected="selected"' : ''; ?>><?php echo $value; ?>px</option>
                    <?php endforeach; ?>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function useColor($args) {
            ?>
            <label for="use_color">
                <input type="checkbox" name="dpd_bukvycja_plugin[use_color]" id="use_color" value="true" <?php echo $this->options['use_color'] ? 'checked="checked"' : '';
            ?>/><?php echo $args[0] ?>
            </label>
            <?php
        }

        public function fontColorSelect($args) {
            ?>
            <label for="dpd_bukvycja_plugin_color_picker">
                <input type='text' name="dpd_bukvycja_plugin[color]" value='<?php echo $this->options['color']; ?>' id='dpd_bukvycja_plugin_color_picker' <?php echo ($this->options['bukvycja_css']) ? '' : 'disabled="disabled"'; ?>/>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function fontWeight($args) {
            $fonts = ['(Do not change)', 'normal', 'bold', 'bolder', 'lighter', '100', '200', '300', '400', '500', '600', '700', '800', '900', 'initial', 'inherit'];
            ?>
            <label for="dpd_bukvycja_plugin[font_weight]">
                <select name="dpd_bukvycja_plugin[font_weight]" id="dpd_bukvycja_plugin[font_weight]">
                    <?php foreach ($fonts as $font): ?>
                        <option value="<?php echo $font; ?>" <?php echo $font == $this->options['font_weight'] ? 'selected="selected"' : ''; ?>><?php echo $font; ?></option>
                    <?php endforeach ?>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function fontFamilySelect($args) {
            $fonts = ['(Match Current Font)', 'Arial', 'Georgia', 'Impact', 'Tahoma', 'Times New Roman', 'Trebuchet MS', 'Verdana', 'DPD-Bukvycja'];
            ?>
            <label for="dpd_bukvycja_plugin[font]">
                <select name="dpd_bukvycja_plugin[font]" id="dpd_bukvycja_plugin[font]">
                    <?php foreach ($fonts as $font): ?>
                        <option value="<?php echo $font; ?>" <?php echo $font == $this->options['font'] ? 'selected="selected"' : ''; ?>><?php echo $font; ?></option>
                    <?php endforeach ?>
                </select>
                <?php echo $args[0]; ?>
            </label>
            <?php
        }

        public function fontURL($args) {
            ?>
            <label for="font_url">
                <input type="text" name="dpd_bukvycja_plugin[font_url]" id="font_url" style="width:600px;" value="<?php echo $this->options['font_url'] ?>"/>
                <?php echo $args[0] ?>
            </label>
            <?php
        }

    }

}
