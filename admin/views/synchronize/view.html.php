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
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::custom('synchronize.log2html', 'featured.png', 'featured_f2.png', 'COM_STATICCONTENT_HTML_LOG', false);
		JToolBarHelper::custom('synchronize.log2pdf', 'featured.png', 'featured_f2.png', 'COM_STATICCONTENT_HTML_PDF', false);
		JToolBarHelper::custom('synchronize.print_log', 'print.png', 'print_f2.png', 'COM_STATICCONTENT_PRINT_LOG', false);
		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');
	}
}