<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Extended Utility class for batch processing widgets.
 *
 * @since  1.7
 */
abstract class JHtmlBatch
{
	/**
	 * Display a batch widget for the access level selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   1.7
	 */
	public static function access()
	{
		JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

		// Create the batch selector to change an access level on a selection list.
		return
			'<label id="batch-access-lbl" for="batch-access" class="modalTooltip" '
			. 'title="' . JHtml::tooltipText('JLIB_HTML_BATCH_ACCESS_LABEL', 'JLIB_HTML_BATCH_ACCESS_LABEL_DESC') . '">'
			. JText::_('JLIB_HTML_BATCH_ACCESS_LABEL')
			. '</label>'
			. JHtml::_(
				'access.assetgrouplist',
				'batch[assetgroup_id]', '',
				'class="inputbox"',
				array(
					'title' => JText::_('JLIB_HTML_BATCH_NOCHANGE'),
					'id' => 'batch-access'
				)
			);
	}

	/**
	 * Displays a batch widget for moving or copying items.
	 *
	 * @param   string  $extension  The extension that owns the category.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   1.7
	 */
	public static function item($extension)
	{
		// Create the copy/move options.
		$options = array(
			JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
			JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
		);

		// Create the batch selector to change select the category by which to move or copy.
		return
			'<label id="batch-choose-action-lbl" for="batch-choose-action">' . JText::_('JLIB_HTML_BATCH_MENU_LABEL') . '</label>'
			. '<div id="batch-choose-action" class="control-group">'
			. '<select name="batch[category_id]" class="inputbox" id="batch-category-id">'
			. '<option value="">' . JText::_('JLIB_HTML_BATCH_NO_CATEGORY') . '</option>'
			. JHtml::_('select.options', JHtml::_('category.options', $extension))
			. '</select>'
			. '</div>'
			. '<div id="batch-copy-move" class="control-group radio">'
			. JText::_('JLIB_HTML_BATCH_MOVE_QUESTION')
			. JHtml::_('select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm')
			. '</div>';
	}

	/**
	 * Display a batch widget for the language selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public static function language()
	{
		JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

		JFactory::getDocument()->addScriptDeclaration(
			'
		jQuery(document).ready(function($){
			if ($("#batch-category-id").length){var batchSelector = $("#batch-category-id");}
			if ($("#batch-menu-id").length){var batchSelector = $("#batch-menu-id");}
			if ($("#batch-position-id").length){var batchSelector = $("#batch-position-id");}
			if ($("#batch-copy-move").length) {
				$("#batch-copy-move").hide();
				batchSelector.on("change", function(){
					if (batchSelector.val() != 0 || batchSelector.val() != "") {
						$("#batch-copy-move").show();
					} else {
						$("#batch-copy-move").hide();
					}
				});
			}
		});
			'
		);

		// Create the batch selector to change the language on a selection list.
		return
			'<label id="batch-language-lbl" for="batch-language-id" class="modalTooltip"'
			. ' title="' . JHtml::tooltipText('JLIB_HTML_BATCH_LANGUAGE_LABEL', 'JLIB_HTML_BATCH_LANGUAGE_LABEL_DESC') . '">'
			. JText::_('JLIB_HTML_BATCH_LANGUAGE_LABEL')
			. '</label>'
			. '<select name="batch[language_id]" class="inputbox" id="batch-language-id">'
			. '<option value="">' . JText::_('JLIB_HTML_BATCH_LANGUAGE_NOCHANGE') . '</option>'
			. JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text')
			. '</select>';
	}

	/**
	 * Display a batch widget for the user selector.
	 *
	 * @param   boolean  $noUser  Choose to display a "no user" option
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   2.5
	 */
	public static function user($noUser = true)
	{
		JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

		$optionNo = '';

		if ($noUser)
		{
			$optionNo = '<option value="0">' . JText::_('JLIB_HTML_BATCH_USER_NOUSER') . '</option>';
		}

		// Create the batch selector to select a user on a selection list.
		return
			'<label id="batch-user-lbl" for="batch-user" class="modalTooltip"'
			. ' title="' . JHtml::tooltipText('JLIB_HTML_BATCH_USER_LABEL', 'JLIB_HTML_BATCH_USER_LABEL_DESC') . '">'
			. JText::_('JLIB_HTML_BATCH_USER_LABEL')
			. '</label>'
			. '<select name="batch[user_id]" class="inputbox" id="batch-user-id">'
			. '<option value="">' . JText::_('JLIB_HTML_BATCH_USER_NOCHANGE') . '</option>'
			. $optionNo
			. JHtml::_('select.options', JHtml::_('user.userlist'), 'value', 'text')
			. '</select>';
	}

	/**
	 * Display a batch widget for the tag selector.
	 *
	 * @return  string  The necessary HTML for the widget.
	 *
	 * @since   3.1
	 */
	public static function tag()
	{
		JHtml::_('bootstrap.tooltip', '.modalTooltip', array('container' => '.modal-body'));

		// Create the batch selector to tag items on a selection list.
		return
			'<label id="batch-tag-lbl" for="batch-tag-id" class="modalTooltip"'
			. ' title="' . JHtml::tooltipText('JLIB_HTML_BATCH_TAG_LABEL', 'JLIB_HTML_BATCH_TAG_LABEL_DESC') . '">'
			. JText::_('JLIB_HTML_BATCH_TAG_LABEL')
			. '</label>'
			. '<select name="batch[tag]" class="inputbox" id="batch-tag-id">'
			. '<option value="">' . JText::_('JLIB_HTML_BATCH_TAG_NOCHANGE') . '</option>'
			. JHtml::_('select.options', JHtml::_('tag.tags', array('filter.published' => array(1))), 'value', 'text')
			. '</select>';
	}
}
