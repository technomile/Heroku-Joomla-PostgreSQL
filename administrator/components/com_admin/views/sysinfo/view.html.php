<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Sysinfo View class for the Admin component
 *
 * @since  1.6
 */
class AdminViewSysinfo extends JViewLegacy
{
	/**
	 * @var array some php settings
	 */
	protected $php_settings = null;

	/**
	 * @var array config values
	 */
	protected $config = null;

	/**
	 * @var array somme system values
	 */
	protected $info = null;

	/**
	 * @var string php info
	 */
	protected $php_info = null;

	/**
	 * @var array informations about writable state of directories
	 */
	protected $directory = null;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		// Access check.
		if (!JFactory::getUser()->authorise('core.admin'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$this->php_settings = $this->get('PhpSettings');
		$this->config       = $this->get('config');
		$this->info         = $this->get('info');
		$this->php_info     = $this->get('PhpInfo');
		$this->directory    = $this->get('directory');

		$this->addToolbar();
		$this->_setSubMenu();
		parent::display($tpl);
	}

	/**
	 * Setup the SubMenu
	 *
	 * @return  void
	 *
	 * @since   1.6
	 * @note    Necessary for Hathor compatibility
	 */
	protected function _setSubMenu()
	{
		try
		{
			$contents = $this->loadTemplate('navigation');
			$document = JFactory::getDocument();
			$document->setBuffer($contents, 'modules', 'submenu');
		}
		catch (Exception $e)
		{
		}
	}

	/**
	 * Setup the Toolbar
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		JToolbarHelper::title(JText::_('COM_ADMIN_SYSTEM_INFORMATION'), 'info-2 systeminfo');
		JToolbarHelper::help('JHELP_SITE_SYSTEM_INFORMATION');
	}
}
