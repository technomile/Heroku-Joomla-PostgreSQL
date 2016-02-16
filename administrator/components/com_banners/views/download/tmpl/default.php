<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<form
	action="<?php echo JRoute::_('index.php?option=com_banners&task=tracks.display&format=raw'); ?>"
	method="post"
	name="adminForm"
	id="download-form"
	class="form-validate">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_BANNERS_TRACKS_DOWNLOAD'); ?></legend>

		<?php foreach ($this->form->getFieldset() as $field) : ?>
			<?php if (!$field->hidden) : ?>
				<?php echo $field->label; ?>
			<?php endif; ?>
			<?php echo $field->input; ?>
		<?php endforeach; ?>
		<div class="clr"></div>
		<button type="button" class="btn" onclick="this.form.submit();window.top.setTimeout('window.parent.jModalClose()', 700);"><?php echo JText::_('COM_BANNERS_TRACKS_EXPORT'); ?></button>
		<button type="button" class="btn" onclick="window.parent.jModalClose();"><?php echo JText::_('COM_BANNERS_CANCEL'); ?></button>

	</fieldset>
</form>
