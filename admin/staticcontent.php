<?php
/**
* Staticcontent Component for Joomla 3
* @package Staticcontent Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JLoader::register('StaticcontentHelper', JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_staticcontent'. DIRECTORY_SEPARATOR .'helpers' . DIRECTORY_SEPARATOR . 'staticcontent.php');

$controller = JControllerLegacy::getInstance('Staticcontent');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();