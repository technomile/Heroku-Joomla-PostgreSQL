<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cache
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Cache Controller
 *
 * @since  1.6
 */
class CacheController extends JControllerLegacy
{
	/**
	 * Display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/cache.php';

		// Get the document object.
		$document = JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName   = $this->input->get('view', 'cache');
		$vFormat = $document->getType();
		$lName   = $this->input->get('layout', 'default', 'string');

		// Get and render the view.
		if ($view = $this->getView($vName, $vFormat))
		{
			switch ($vName)
			{
				case 'purge':
					break;
				case 'cache':
				default:
					$model = $this->getModel($vName);
					$view->setModel($model, true);
					break;
			}

			$view->setLayout($lName);

			// Push document object into the view.
			$view->document = $document;

			// Load the submenu.
			CacheHelper::addSubmenu($this->input->get('view', 'cache'));

			$view->display();
		}
	}

	/**
	 * Method to delete a list of cache groups.
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		$cid = $this->input->post->get('cid', array(), 'array');

		$model = $this->getModel('cache');

		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else
		{
			$model->cleanlist($cid);
		}

		$this->setRedirect('index.php?option=com_cache&client=' . $model->getClient()->id);
	}

	/**
	 * Purge the cache.
	 *
	 * @return  void
	 */
	public function purge()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JInvalid_Token'));

		$model = $this->getModel('cache');
		$ret = $model->purge();

		$msg = JText::_('COM_CACHE_EXPIRED_ITEMS_HAVE_BEEN_PURGED');
		$msgType = 'message';

		if ($ret === false)
		{
			$msg = JText::_('COM_CACHE_EXPIRED_ITEMS_PURGING_ERROR');
			$msgType = 'error';
		}

		$this->setRedirect('index.php?option=com_cache&view=purge', $msg, $msgType);
	}
}
