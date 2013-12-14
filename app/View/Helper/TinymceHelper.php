<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       app.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Helper to output TinyMCE TextArea and Input fields.
 */
class TinymceHelper extends AppHelper {

/**
 * List of helpers this helper uses.
 * @var array
 */
	public $helpers = array('Js', 'Html', 'Form');

/**
 * If this is true, then the script has been loaded and we don't need to load it again.
 * @var boolean
 */
	private $__script = false;

/** 
 * Adds the tiny_mce.js file and constructs the options 
 * 
 * @param string $fieldName Name of a field, like this "Modelname.fieldname" 
 * @param array $tinyoptions Array of TinyMCE attributes for this textarea 
 * @return string JavaScript code to initialise the TinyMCE area 
 */
	private function __build($fieldName, $tinyoptions = array()) {
		if (!$this->__script) {
			// We don't want to add this every time, it's only needed once
			$this->__script = true;
			$this->Html->script('tiny_mce/tiny_mce', array('inline' => false));
		}

		// Ties the options to the field
		$tinyoptions['mode'] = 'exact';
		$tinyoptions['elements'] = $this->domId($fieldName);

		// List the keys having a function
		$valueArr = array();
		$replaceKeys = array();

		foreach ($tinyoptions as $key => &$value) {
			if (strpos($value, 'function(') === 0) {
				$valueArr[] = $value;
				$value = '%' . $key . '%';
				$replaceKeys[] = '"' . $value . '"';
			}
		}

		// Encode the array in json
		$json = $this->Js->object($tinyoptions);

		// Replace the functions
		$json = str_replace($replaceKeys, $valueArr, $json);
		$this->Html->scriptStart(array('inline' => false));
		echo 'tinyMCE.init(' . $json . ');';
		$this->Html->scriptEnd();
	}

/**
 * Creates a TinyMCE textarea.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @param array $tinyoptions Array of TinyMCE attributes for this textarea.
 * @param string $preset The preset options to use.
 * @return string An HTML textarea element with TinyMCE
 */
	public function textarea($fieldName, $options = array(), $tinyoptions = array(), $preset = null) {
		// If a preset is defined
		if (!empty($preset)) {
			$presetOptions = $this->__preset($preset);

			// If $presetOptions && $tinyoptions are an array
			if (is_array($presetOptions) && is_array($tinyoptions)) {
				$tinyoptions = array_merge($presetOptions, $tinyoptions);
			} else {
				$tinyoptions = $presetOptions;
			}
		}
		return $this->Form->textarea($fieldName, $options) . $this->__build($fieldName, $tinyoptions);
	}

/**
 * Creates a TinyMCE input.
 *
 * @param string $fieldName Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @param array $tinyoptions Array of TinyMCE attributes for this input.
 * @param string $preset The preset of options to user.
 * @return string An HTML input element with TinyMCE
 */
	public function input($fieldName, $options = array(), $tinyoptions = array(), $preset = null) {
		// If a preset is defined
		if (!empty($preset)) {
			$presetOptions = $this->__preset($preset);
			// If $presetOptions && $tinyoptions are an array
			if (is_array($presetOptions) && is_array($tinyoptions)) {
				$tinyoptions = array_merge($presetOptions, $tinyoptions);
			} else {
				$tinyoptions = $presetOptions;
			}
		}
		$options['type'] = 'textarea';
		return $this->Form->input($fieldName, $options) . $this->__build($fieldName, $tinyoptions);
	}

/**
 * Creates a preset for TinyOptions
 *
 * @param string $name Name of the preset to use.
 * @return array Array of preset data.
 */
	private function __preset($name) {
		// Full Feature
		if ($name == 'full') {
			return array(
				'theme' => 'advanced',
				'plugins' => 'safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template',
				'theme_advanced_buttons1' => 'save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect',
				'theme_advanced_buttons2' => 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor',
				'theme_advanced_buttons3' => 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen',
				'theme_advanced_buttons4' => 'insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak',
				'theme_advanced_toolbar_location' => 'top',
				'theme_advanced_toolbar_align' => 'left',
				'theme_advanced_statusbar_location' => 'bottom',
				'theme_advanced_resizing' => true,
				'theme_advanced_resize_horizontal' => false,
				'convert_fonts_to_spans' => true,
				'file_browser_callback' => 'ckfinder_for_tiny_mce'
			);
		}

		// Basic
		if ($name == 'basic') {
			return array(
				'theme' => 'advanced',
				'plugins' => 'safari,advlink,paste',
				'theme_advanced_buttons1' => 'code,|,copy,pastetext,|,bold,italic,underline,|,link,unlink,|,bullist,numlist',
				'theme_advanced_buttons2' => '',
				'theme_advanced_buttons3' => '',
				'theme_advanced_toolbar_location' => 'top',
				'theme_advanced_toolbar_align' => 'center',
				'theme_advanced_statusbar_location' => 'none',
				'theme_advanced_resizing' => false,
				'theme_advanced_resize_horizontal' => false,
				'convert_fonts_to_spans' => false
			);
		}

		// Simple
		if ($name == 'simple') {
			return array(
				'theme' => 'simple',
			);
		}

		// BBCode
		if ($name == 'bbcode') {
			return array(
				'theme' => 'advanced',
				'plugins' => 'bbcode',
				'theme_advanced_buttons1' => 'bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code',
				'theme_advanced_buttons2' => '',
				'theme_advanced_buttons3' => '',
				'theme_advanced_toolbar_location' => 'top',
				'theme_advanced_toolbar_align' => 'left',
				'theme_advanced_styles' => 'Code=codeStyle;Quote=quoteStyle',
				'theme_advanced_statusbar_location' => 'bottom',
				'theme_advanced_resizing' => true,
				'theme_advanced_resize_horizontal' => false,
				'entity_encoding' => 'raw',
				'add_unload_trigger' => false,
				'remove_linebreaks' => false,
				'inline_styles' => false
			);
		}
		return null;
	}
}