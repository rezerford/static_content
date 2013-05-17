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

class StaticcontentViewSynchronize extends JViewLegacy
{
	function display($tpl = null) 
	{
		$submenu = 'synchronize';
		StaticcontentHelper::showTitle($submenu);
		
		StaticcontentHelper::addStaticcontentSubmenu('synchronize');
		$this->sidebar = JHtmlSidebar::render();
		
		parent::display($tpl);
	}
}