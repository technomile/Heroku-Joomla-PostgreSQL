<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_installer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Extension Manager Abstract Extension Model.
 *
 * @since  1.5
 */
class InstallerModel extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name',
				'client_id',
				'enabled',
				'type',
				'folder',
				'extension_id',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Returns an object list
	 *
	 * @param   string  $query       The query
	 * @param   int     $limitstart  Offset
	 * @param   int     $limit       The number of records
	 *
	 * @return  array
	 */
	protected function _getList($query, $limitstart = 0, $limit = 0)
	{
		$ordering = $this->getState('list.ordering');
		$search   = $this->getState('filter.search');

		// Replace slashes so preg_match will work
		$search = str_replace('/', ' ', $search);
		$db     = $this->getDbo();

		if ($ordering == 'name' || (!empty($search) && stripos($search, 'id:') !== 0))
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
			$this->translate($result);

			if (!empty($search))
			{
				$escapedSearchString = $this->refineSearchStringToRegex($search, '/');

				foreach ($result as $i => $item)
				{
					if (!preg_match("/$escapedSearchString/i", $item->name))
					{
						unset($result[$i]);
					}
				}
			}

			JArrayHelper::sortObjects($result, $this->getState('list.ordering'), $this->getState('list.direction') == 'desc' ? -1 : 1, true, true);
			$total = count($result);
			$this->cache[$this->getStoreId('getTotal')] = $total;

			if ($total < $limitstart)
			{
				$limitstart = 0;
				$this->setState('list.start', 0);
			}

			return array_slice($result, $limitstart, $limit ? $limit : null);
		}

		$query->order($db->quoteName($ordering) . ' ' . $this->getState('list.direction'));
		$result = parent::_getList($query, $limitstart, $limit);
		$this->translate($result);

		return $result;
	}

	/**
	 * Translate a list of objects
	 *
	 * @param   array  &$items  The array of objects
	 *
	 * @return  array The array of translated objects
	 */
	protected function translate(&$items)
	{
		$lang = JFactory::getLanguage();

		foreach ($items as &$item)
		{
			if (strlen($item->manifest_cache) && $data = json_decode($item->manifest_cache))
			{
				foreach ($data as $key => $value)
				{
					if ($key == 'type')
					{
						// Ignore the type field
						continue;
					}

					$item->$key = $value;
				}
			}

			$item->author_info = @$item->authorEmail . '<br />' . @$item->authorUrl;
			$item->client = $item->client_id ? JText::_('JADMINISTRATOR') : JText::_('JSITE');
			$path = $item->client_id ? JPATH_ADMINISTRATOR : JPATH_SITE;

			switch ($item->type)
			{
				case 'component':
					$extension = $item->element;
					$source = JPATH_ADMINISTRATOR . '/components/' . $extension;
						$lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, true)
					||	$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'file':
					$extension = 'files_' . $item->element;
						$lang->load("$extension.sys", JPATH_SITE, null, false, true);
				break;
				case 'library':
					$extension = 'lib_' . $item->element;
						$lang->load("$extension.sys", JPATH_SITE, null, false, true);
				break;
				case 'module':
					$extension = $item->element;
					$source = $path . '/modules/' . $extension;
						$lang->load("$extension.sys", $path, null, false, true)
					||	$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'plugin':
					$extension = 'plg_' . $item->folder . '_' . $item->element;
					$source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
						$lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, true)
					||	$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'template':
					$extension = 'tpl_' . $item->element;
					$source = $path . '/templates/' . $item->element;
						$lang->load("$extension.sys", $path, null, false, true)
					||	$lang->load("$extension.sys", $source, null, false, true);
				break;
				case 'package':
				default:
					$extension = $item->element;
						$lang->load("$extension.sys", JPATH_SITE, null, false, true);
				break;
			}

			if (!in_array($item->type, array('language', 'template', 'library')))
			{
				$item->name = JText::_($item->name);
			}

			settype($item->description, 'string');

			if (!in_array($item->type, array('language')))
			{
				$item->description = JText::_($item->description);
			}
		}
	}
}
