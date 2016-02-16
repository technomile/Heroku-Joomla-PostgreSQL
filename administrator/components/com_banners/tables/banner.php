<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Banner table
 *
 * @since  1.5
 */
class BannersTableBanner extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$_db  Database connector object
	 *
	 * @since   1.5
	 */
	public function __construct(&$_db)
	{
		parent::__construct('#__banners', 'id', $_db);

		JTableObserverContenthistory::createObserver($this, array('typeAlias' => 'com_banners.banner'));

		$date = JFactory::getDate();
		$this->created = $date->toSql();
		$this->setColumnAlias('published', 'state');
	}

	/**
	 * Increase click count
	 *
	 * @return  void
	 */
	public function clicks()
	{
		$query = 'UPDATE #__banners'
			. ' SET clicks = (clicks + 1)'
			. ' WHERE id = ' . (int) $this->id;

		$this->_db->setQuery($query);
		$this->_db->execute();
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean
	 *
	 * @see     JTable::check
	 * @since   1.5
	 */
	public function check()
	{
		// Set name
		$this->name = htmlspecialchars_decode($this->name, ENT_QUOTES);

		// Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);

		if (empty($this->alias))
		{
			$this->alias = JApplication::stringURLSafe($this->name);
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));

			return false;
		}

		// Set ordering
		if ($this->state < 0)
		{
			// Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		}
		elseif (empty($this->ordering))
		{
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder($this->_db->quoteName('catid') . '=' . $this->_db->quote($this->catid) . ' AND state>=0');
		}

		return true;
	}

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array to bind
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error
	 *
	 * @since   1.5
	 */
	public function bind($array, $ignore = array())
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$registry = new Registry;
			$registry->loadArray($array['params']);

			if ((int) $registry->get('width', 0) < 0)
			{
				$this->setError(JText::sprintf('JLIB_DATABASE_ERROR_NEGATIVE_NOT_PERMITTED', JText::_('COM_BANNERS_FIELD_WIDTH_LABEL')));

				return false;
			}

			if ((int) $registry->get('height', 0) < 0)
			{
				$this->setError(JText::sprintf('JLIB_DATABASE_ERROR_NEGATIVE_NOT_PERMITTED', JText::_('COM_BANNERS_FIELD_HEIGHT_LABEL')));

				return false;
			}

			// Converts the width and height to an absolute numeric value:
			$width = abs((int) $registry->get('width', 0));
			$height = abs((int) $registry->get('height', 0));

			// Sets the width and height to an empty string if = 0
			$registry->set('width', ($width ? $width : ''));
			$registry->set('height', ($height ? $height : ''));

			$array['params'] = (string) $registry;
		}

		if (isset($array['imptotal']))
		{
			$array['imptotal'] = abs((int) $array['imptotal']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Method to store a row
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success, false on failure.
	 */
	public function store($updateNulls = false)
	{
		if (empty($this->id))
		{
			$purchase_type = $this->purchase_type;

			if ($purchase_type < 0 && $this->cid)
			{
				$client = JTable::getInstance('Client', 'BannersTable');
				$client->load($this->cid);
				$purchase_type = $client->purchase_type;
			}

			if ($purchase_type < 0)
			{
				$params = JComponentHelper::getParams('com_banners');
				$purchase_type = $params->get('purchase_type');
			}

			switch ($purchase_type)
			{
				case 1:
					$this->reset = $this->_db->getNullDate();
					break;
				case 2:
					$date = JFactory::getDate('+1 year ' . date('Y-m-d', strtotime('now')));
					$this->reset = $this->_db->quote($date->toSql());
					break;
				case 3:
					$date = JFactory::getDate('+1 month ' . date('Y-m-d', strtotime('now')));
					$this->reset = $this->_db->quote($date->toSql());
					break;
				case 4:
					$date = JFactory::getDate('+7 day ' . date('Y-m-d', strtotime('now')));
					$this->reset = $this->_db->quote($date->toSql());
					break;
				case 5:
					$date = JFactory::getDate('+1 day ' . date('Y-m-d', strtotime('now')));
					$this->reset = $this->_db->quote($date->toSql());
					break;
			}
			// Store the row
			parent::store($updateNulls);
		}
		else
		{
			// Get the old row
			$oldrow = JTable::getInstance('Banner', 'BannersTable');

			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}

			// Verify that the alias is unique
			$table = JTable::getInstance('Banner', 'BannersTable');

			if ($table->load(array('alias' => $this->alias, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
			{
				$this->setError(JText::_('COM_BANNERS_ERROR_UNIQUE_ALIAS'));

				return false;
			}

			// Store the new row
			parent::store($updateNulls);

			// Need to reorder ?
			if ($oldrow->state >= 0 && ($this->state < 0 || $oldrow->catid != $this->catid))
			{
				// Reorder the oldrow
				$this->reorder($this->_db->quoteName('catid') . '=' . $this->_db->quote($oldrow->catid) . ' AND state>=0');
			}
		}

		return count($this->getErrors()) == 0;
	}

	/**
	 * Method to set the sticky state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
	 * @param   integer  $state   The sticky state. eg. [0 = unsticked, 1 = sticked]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function stick($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));

				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Banner', 'BannersTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if (!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Verify checkout
			if ($table->checked_out == 0 || $table->checked_out == $userId)
			{
				// Change the state
				$table->sticky = $state;
				$table->checked_out = 0;
				$table->checked_out_time = $this->_db->getNullDate();

				// Check the row
				$table->check();

				// Store the row
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			}
		}

		return count($this->getErrors()) == 0;
	}
}
