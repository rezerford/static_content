<?php
/**
* Staticcontent Deluxe Component for Joomla 3
* @package Staticcontent Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die;
 
/**
 * Staticcontent Deluxe component helper.
 */
class StaticcontentHelper
{
                
        public static function showTitle($submenu)  
        {       
         	$document = JFactory::getDocument();
			$title = JText::_('COM_STATICCONTENT_ADMINISTRATION_'.strtoupper($submenu));
            $document->setTitle($title);
            JToolBarHelper::title($title, $submenu);                	               	              
        }
		
		public static function addStaticcontentSubmenu($vName)
		{
			JHtmlSidebar::addEntry(
				JText::_('COM_STATICCONTENT_SUBMENU_SYNCHRONIZE'),
				'index.php?option=com_staticcontent&view=synchronize',
				$vName == 'synchronize'
			);
			JHtmlSidebar::addEntry(
				JText::_('COM_STATICCONTENT_SUBMENU_CONFIGURATION'),
				'index.php?option=com_staticcontent&view=configuration',
				$vName == 'configuration');
			
		}
}