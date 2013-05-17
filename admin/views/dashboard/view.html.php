<?php
/**
* Staticcontent component for Joomla 3.0
* @package Staticcontent
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class StaticcontentViewDashboard extends JViewLegacy
{
	function display($tpl = null) 
	{
        $this->dashboardItems = $this->get('Items');
		$this->addToolBar();
		$this->setDocument();

		parent::display($tpl);
	}

	protected function addToolBar() 
	{
		JToolBarHelper::title(JText::_('COM_STATICCONTENT').': '.JText::_('COM_STATICCONTENT_MANAGER_DASHBOARD'), 'dashboard');
	}

	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_STATICCONTENT').': '.JText::_('COM_STATICCONTENT_MANAGER_DASHBOARD'));
		$document->addScript('components/com_staticcontent/assets/js/js.js');
	}
}