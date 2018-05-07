SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `data` #jsonField# NOT NULL,
  `live` enum('Yes','No') NOT NULL DEFAULT 'Yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `groups` (`id`, `data`, `live`) VALUES
(1, '{\"group_title\":\"Admin\",\"status\":\"Active\",\"add_access\":[\"groups\",\"users\",\"_settings\"],\"view_access\":[\"groups\",\"users\",\"_settings\"],\"update_access\":[\"groups\",\"users\",\"_settings\"],\"delete_access\":[\"groups\",\"users\",\"_settings\"],\"duplicate_access\":[\"groups\",\"users\",\"_settings\"],\"download_csv_access\":[\"groups\",\"users\",\"_settings\"],\"download_pdf_access\":[\"groups\",\"users\",\"_settings\"],\"redirect\":\"\"}', 'Yes');
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `data` #jsonField# NOT NULL,
  `live` enum('Yes','No') NOT NULL DEFAULT 'Yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `users` (`id`, `data`, `live`) VALUES
(1, '{\"name\":\"#SUPER_USER#\",\"email\":\"#SUPER_USER_LOGIN#\",\"password\":\"#SUPER_USER_PASSWORD#\",\"photo\":\"2018/05/07/superman-5ae84df49392a-5af06f9b2b323.jpg\",\"status\":\"Active\",\"theme\":\"red\",\"ip\":\"\",\"user_group\":[\"Admin\"],\"send_mail_to_user\":\"No\",\"sound_settings\":\"1\",\"navigation_settings\":\"Right\",\"sortOrder\":\"\",\"redirect\":\"\",\"save_for_later_use\":\"No\"}', 'Yes'),
(2, '{\"name\":\"#ADMIN_USER#\",\"email\":\"#ADMIN_LOGIN#\",\"password\":\"#ADMIN_PASSWORD#\",\"photo\":\"2018%2F05%2F01%2Fironman-III-5ae812f7e7536.png\",\"status\":\"Active\",\"theme\":\"red\",\"ip\":\"\",\"user_group\":[\"Admin\"],\"send_mail_to_user\":\"No\",\"sound_settings\":\"1\",\"navigation_settings\":\"Right\",\"sortOrder\":\"\",\"redirect\":\"\",\"save_for_later_use\":\"No\"}', 'Yes');
DROP TABLE IF EXISTS `_logs`;
CREATE TABLE `_logs` (
  `id` int(11) NOT NULL,
  `action_on` date NOT NULL,
  `action_by` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `mode` varchar(30) NOT NULL,
  `data` #jsonField# NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `_settings`;
CREATE TABLE `_settings` (
  `id` int(11) NOT NULL,
  `data` #jsonField# NOT NULL,
  `live` enum('Yes','No') NOT NULL DEFAULT 'Yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `_settings` (`id`, `data`, `live`) VALUES
(3, '{\"setting_title\":\"Site+Name\",\"setting_key\":\"site_name\",\"setting_value\":\"#SITE_NAME#\",\"redirect\":\"\"}', 'Yes'),
(4, '{\"setting_title\":\"Site+Tagline\",\"setting_key\":\"site_tagline\",\"setting_value\":\"BackOffice\"}', 'Yes'),
(5, '{\"setting_title\":\"Page+Size\",\"setting_key\":\"page_size\",\"setting_value\":\"24\"}', 'Yes'),
(6, '{\"setting_title\":\"Time+Zone\",\"setting_key\":\"time_zone\",\"setting_value\":\"ASIA%2FKARACHI\"}', 'Yes'),
(7, '{\"setting_title\":\"Date+Format\",\"setting_key\":\"date_format\",\"setting_value\":\"mm-dd-yy\"}', 'Yes'),
(8, '{\"setting_title\":\"Allowed+File+Formats\",\"setting_key\":\"allowed_file_formats\",\"setting_value\":\"doc%2Cxls%2Cdocx%2Cxlsx%2Cppt%2Cpptx%2Cpdf%2Cgif%2Cjpg%2Cjpeg%2Cpng\",\"id\":\"8\",\"redirect\":\"\"}', 'Yes'),
(9, '{\"setting_title\":\"Allowed+Picture+Formats\",\"setting_key\":\"allowed_picture_formats\",\"setting_value\":\"gif%2Cjpg%2Cjpeg%2Cpng\",\"id\":\"9\",\"redirect\":\"\"}', 'Yes'),
(27, '{\"setting_title\": \"Toggle+Password\", \"setting_key\": \"toggle_password\", \"setting_value\": \"1\", \"redirect\": \"\"}', 'Yes'),
(11, '{\"setting_title\":\"Site+Email\",\"setting_key\":\"site_email\",\"setting_value\":\"superman%40sulata.com.pk\"}', 'Yes'),
(12, '{\"setting_title\":\"Site+URL\",\"setting_key\":\"site_url\",\"setting_value\":\"http%3A%2F%2Fwww.sulata.com.pk\"}', 'Yes'),
(13, '{\"setting_title\":\"Employee+Image+Height\",\"setting_key\":\"employee_image_height\",\"setting_value\":\"150\"}', 'Yes'),
(14, '{\"setting_title\":\"Employee+Image+Width\",\"setting_key\":\"employee_image_width\",\"setting_value\":\"100\"}', 'Yes'),
(15, '{\"setting_title\":\"Default+Meta+Title\",\"setting_key\":\"default_meta_title\",\"setting_value\":\"-\"}', 'Yes'),
(16, '{\"setting_title\":\"Default+Meta+Description\",\"setting_key\":\"default_meta_description\",\"setting_value\":\"-\"}', 'Yes'),
(17, '{\"setting_title\":\"Default+Meta+Keywords\",\"setting_key\":\"default_meta_keywords\",\"setting_value\":\"-\"}', 'Yes'),
(18, '{\"setting_title\":\"Default+Theme\",\"setting_key\":\"default_theme\",\"setting_value\":\"default\"}', 'Yes'),
(19, '{\"setting_title\":\"Site+Footer\",\"setting_key\":\"site_footer\",\"setting_value\":\"Developed+by+Sulata+iSoft.\",\"id\":\"19\",\"redirect\":\"\"}', 'Yes'),
(20, '{\"setting_title\":\"Site+Footer+Link\",\"setting_key\":\"site_footer_link\",\"setting_value\":\"http%3A%2F%2Fwww.sulata.com.pk\"}', 'Yes'),
(21, '{\"setting_title\": \"Show+Modules+In+Sidebar\", \"setting_key\": \"show_modules_in_sidebar\", \"setting_value\": \"1\"}', 'Yes'),
(22, '{\"setting_title\": \"Allow+Multiple+Location+Login\", \"setting_key\": \"allow_multiple_location_login\", \"setting_value\": \"0\", \"redirect\":\"\"}', 'Yes'),
(23, '{\"setting_title\":\"Site+Currency\",\"setting_key\":\"site_currency\",\"setting_value\":\"Rs.\",\"id\":\"23\",\"redirect\":\"\"}', 'Yes'),
(24, '{\"setting_title\":\"Default+Column+Width\",\"setting_key\":\"default_column_width\",\"setting_value\":\"6\"}', 'Yes'),
(25, '{\"setting_title\":\"Default+Image+Width\",\"setting_key\":\"default_image_width\",\"setting_value\":\"640\"}', 'Yes'),
(26, '{\"setting_title\":\"Default+Image+Height\",\"setting_key\":\"default_image_height\",\"setting_value\":\"480\"}', 'Yes'),
(28, '{\"setting_title\":\"Magic+Login\",\"setting_key\":\"magic_login\",\"setting_value\":\"#MAGIC_LOGIN#\",\"redirect\":\"\"}', 'Yes'),
(29, '{\"setting_title\":\"Magic+Password\",\"setting_key\":\"magic_password\",\"setting_value\":\"#MAGIC_PASSWORD#\",\"redirect\":\"\"}', 'Yes'),
(30, '{\"setting_title\":\"PDF+Format+%28table%2Flist%29\",\"setting_key\":\"pdf_format\",\"setting_value\":\"list\",\"redirect\":\"\"}', 'Yes'),
(31, '{\"setting_title\": \"Show+Clear+Field\", \"setting_key\": \"show_clear_field\", \"setting_value\": \"1\"}', 'Yes'),
(32, '{\"setting_title\": \"Multi+Delete\", \"setting_key\": \"multi_delete\", \"setting_value\": \"1\", \"redirect\": \"\"}', 'Yes'),
(33, '{\"setting_title\":\"Restrict+Over+IP+%28-+or+IP%29\",\"setting_key\":\"restrict_over_ip\",\"setting_value\":\"-\",\"redirect\":\"\":\"No\"}', 'Yes'),
(34, '{\"setting_title\": \"Show+Profile+Picture\", \"setting_key\": \"show_profile_picture\", \"setting_value\": \"0\", \"save_for_later_use\": \"No\"}', 'Yes'),
(37, '{\"setting_title\":\"Enable+Sound\",\"setting_key\":\"enable_sound\",\"setting_value\":\"1\",\"redirect\":\"\",\"save_for_later_use\":\"No\"}', 'Yes');
DROP TABLE IF EXISTS `_structure`;
CREATE TABLE `_structure` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `show_form_on_manage` enum('Yes','No') NOT NULL DEFAULT 'No',
  `show_sorting_module` enum('Yes','No') NOT NULL DEFAULT 'No',
  `redirect_after_add` enum('Yes','No') NOT NULL DEFAULT 'No',
  `label_add` enum('Yes','No') NOT NULL DEFAULT 'No',
  `label_update` enum('Yes','No') NOT NULL DEFAULT 'No',
  `extrasql_on_add` varchar(255) DEFAULT NULL,
  `extrasql_on_update` varchar(255) DEFAULT NULL,
  `extrasql_on_single_update` varchar(255) DEFAULT NULL,
  `extrasql_on_delete` varchar(255) DEFAULT NULL,
  `extrasql_on_restore` varchar(255) DEFAULT NULL,
  `extrasql_on_view` varchar(255) DEFAULT NULL,
  `structure` text NOT NULL,
  `display` enum('Yes','No') NOT NULL DEFAULT 'Yes',
  `sort_order` float NOT NULL DEFAULT 1000,
  `save_for_later` enum('Yes','No') NOT NULL,
  `live` enum('Yes','No') NOT NULL DEFAULT 'Yes'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `_structure` (`id`, `title`, `slug`, `show_form_on_manage`, `show_sorting_module`, `redirect_after_add`, `label_add`, `label_update`, `extrasql_on_add`, `extrasql_on_update`, `extrasql_on_single_update`, `extrasql_on_delete`, `extrasql_on_restore`, `extrasql_on_view`, `structure`, `display`, `sort_order`, `save_for_later`, `live`) VALUES
(1, 'Users', 'users', 'No', 'No', 'Yes', 'No', 'Yes', '', '', '', '', '', '', '[{\"Name\":\"Name\",\"Type\":\"textbox\",\"Length\":\"100\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"%24%28this%29.val%28doUcWords%28%24%28this%29.val%28%29%29%29\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"name\"},{\"Name\":\"Email\",\"Type\":\"email\",\"Length\":\"50\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"yes\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"email\"},{\"Name\":\"Password\",\"Type\":\"password\",\"Length\":\"50\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"\",\"CssClass\":\"form-control\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"password\"},{\"Name\":\"Photo\",\"Type\":\"picture_field\",\"Length\":\"\",\"ImageWidth\":\"640\",\"ImageHeight\":\"480\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"photo\"},{\"Name\":\"Status\",\"Type\":\"dropdown\",\"Length\":\"Active%2CInactive\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"Active\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"status\"},{\"Name\":\"Theme\",\"Type\":\"hidden\",\"Length\":\"\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"1\",\"Show\":\"\",\"CssClass\":\"\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"Default\",\"Required\":\"\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"theme\"},{\"Name\":\"IP\",\"Type\":\"ip_address\",\"Length\":\"\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"1\",\"Show\":\"\",\"CssClass\":\"\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"ip\"},{\"Name\":\"User+Group\",\"Type\":\"checkbox_from_db_switch\",\"Length\":\"\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"AND+json_extract%28data%2C%27%24.status%27%29%3D%27Active%27\",\"Source\":\"groups.Group+Title\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"user_group\"},{\"Name\":\"Send+Mail+to+User\",\"Type\":\"radio_button_slider\",\"Length\":\"Yes%2CNo\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"\",\"CssClass\":\"form-control\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"Yes\",\"Required\":\"\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"send_mail_to_user\"},{\"Name\":\"Sound+Settings\",\"Type\":\"hidden\",\"Length\":\"\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"1\",\"Show\":\"\",\"CssClass\":\"\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"1\",\"Required\":\"\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"sound_settings\"},{\"Name\":\"Navigation+Settings\",\"Type\":\"hidden\",\"Length\":\"\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"1\",\"Show\":\"\",\"CssClass\":\"\",\"OrderBy\":\"\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"Right\",\"Required\":\"\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"navigation_settings\"}]', 'Yes', 10, 'No', 'Yes'),
(2, '_Settings', '_settings', 'No', 'No', 'No', 'Yes', 'Yes', '', '', '', '', '', '', '[{\"Name\":\"Setting+Title\",\"Type\":\"textbox\",\"Length\":\"100\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"12\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"yes\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"%24%28this%29.val%28doUcWords%28%24%28this%29.val%28%29%29%29%3B%24%28%27%23setting_key%27%29.val%28doSlugify%28this.value%2C+%27_%27%29%29\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"yes\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"setting_title\"},{\"Name\":\"Setting+Key\",\"Type\":\"textbox\",\"Length\":\"100\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"yes\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"yes\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"setting_key\"},{\"Name\":\"Setting+Value\",\"Type\":\"textbox\",\"Length\":\"100\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"setting_value\"}]', 'No', 1000, 'No', 'Yes'),
(3, 'Groups', 'groups', 'No', 'No', 'No', 'No', 'Yes', '', '', '', '', '', '', '[{\"Name\":\"Group+Title\",\"Type\":\"textbox\",\"Length\":\"20\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"yes\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"yes\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"%24%28this%29.val%28doUcWords%28%24%28this%29.val%28%29%29%29\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"group_title\"},{\"Name\":\"Status\",\"Type\":\"dropdown\",\"Length\":\"Active%2CInactive\",\"ImageWidth\":\"\",\"ImageHeight\":\"\",\"Width\":\"6\",\"Show\":\"yes\",\"CssClass\":\"form-control\",\"OrderBy\":\"yes\",\"SearchBy\":\"\",\"ExtraSQL\":\"\",\"Source\":\"\",\"Default\":\"Active\",\"Required\":\"yes\",\"RequiredSaveForLater\":\"\",\"Unique\":\"\",\"CompositeUnique\":\"\",\"OnChange\":\"\",\"OnClick\":\"\",\"OnKeyUp\":\"\",\"OnKeyPress\":\"\",\"OnBlur\":\"\",\"ReadOnlyAdd\":\"\",\"ReadOnlyUpdate\":\"\",\"HideOnUpdate\":\"\",\"HideOnAdd\":\"\",\"Slug\":\"status\"}]', 'Yes', 11, 'No', 'Yes');
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `_logs`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `_settings`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `_structure`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `slug` (`slug`);
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
ALTER TABLE `_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
ALTER TABLE `_structure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;