<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$params = JComponentHelper::getParams('com_media');
$path = 'file_path';
?>
<form target="_parent" action="index.php?option=com_media&amp;tmpl=index&amp;folder=<?php echo $this->state->folder; ?>" method="post" id="mediamanager-form" name="mediamanager-form">
	<div class="muted">
		<p>
			<span class="icon-folder"></span>
			<?php if ($this->state->folder != '') : ?>
				<?php echo JText::_('JGLOBAL_ROOT') . ': ' . $params->get($path, 'images') . '/' . $this->state->folder; ?>
			<?php else : ?>
				<?php echo JText::_('JGLOBAL_ROOT') . ': ' . $params->get($path, 'images'); ?>
			<?php endif; ?>
		</p>
	</div>

	<ul class="manager thumbnails">
		<?php
		echo $this->loadTemplate('up');
		?>

		<?php for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->documents); $i < $n; $i++) :
			$this->setDoc($i);
			echo $this->loadTemplate('doc');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->images); $i < $n; $i++) :
			$this->setImage($i);
			echo $this->loadTemplate('img');
		endfor; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="username" value="" />
		<input type="hidden" name="password" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</ul>
</form>
