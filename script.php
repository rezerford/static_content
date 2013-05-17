<?php
/**
* Staticcontent component for Joomla
* @version $Id: install.staticcontent.php 2009-11-16 17:30:15
* @package Staticcontent
* @subpackage install.staticcontent.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// Don't allow access
defined( '_JEXEC' ) or die( 'Restricted access' );

class com_staticcontentInstallerScript
{
	function install() {
		$installer = new com_staticcontentInstallerScript();
		$installer->generateStatic();
		
		$db = JFactory::getDBO();
		$db->setQuery("INSERT INTO #__staticcontent_dashboard_items (`id`, `title`, `url`, `icon`, `published`) VALUES ('', 'Synchronize', '".JURI::root()."administrator/index.php?option=com_staticcontent&view=synchronize', '".JURI::root()."administrator/components/com_staticcontent/assets/images/synñhronize.png', 1)");
		$db->query();
		
		$db->setQuery("INSERT INTO #__staticcontent_dashboard_items (`id`, `title`, `url`, `icon`, `published`) VALUES ('', 'Configuration', '".JURI::root()."administrator/index.php?option=com_staticcontent&view=configuration', '".JURI::root()."administrator/components/com_staticcontent/assets/images/configuration.png', 1)");
		$db->query();
	}
	
	function uninstall($parent)
    {
	    echo '<p>' . JText::_('COM_STATICCONTENT_UNINSTALL_TEXT') . '</p>';
    }

    function update($parent)
    {
		$installer = new com_staticcontentInstallerScript();
		$installer->syncStatic();
    }

	function preflight($type, $parent) 
	{
		
	}
	
	function postflight($type, $parent)
    {
	
	}
	
	function syncStatic()
	{
		$app = JFactory::getApplication();
		$app->redirect(JURI::root().'administrator/index.php?option=com_staticcontent&view=synchronize');
	}
	
	function generateStatic(){
		$db = JFactory::getDBO();
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		
		if(!defined('DS')) define('DS', '/');
		$adminDir = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_staticcontent';
		
		if (!JFolder::exists(JPATH_ROOT . DS . 'static_content') ) {
			JFolder::create( JPATH_ROOT . DS . 'static_content');
		}
		
		require_once JPATH_BASE . '/components/com_content/models/articles.php';
		$model = new ContentModelArticles();
		$items = $model->getItems();
		
		if(count($items)){
			foreach($items as $item){
				if($item->category_title == 'Uncategorised'){
					$file = JPATH_SITE.DS.'static_content'.DS.$item->alias.'.html';
				} else {
					$category_dir = strtolower(str_replace(" ", "_", $item->category_title));
					if(!JFolder::exists(JPATH_SITE.DS.'static_content'.DS.$category_dir)){
						JFolder::create(JPATH_SITE.DS.'static_content'.DS.$category_dir);
					}
					
					$file = JPATH_SITE.DS.'static_content'.DS.$category_dir.DS.$item->alias.'.html';
				}
				
				$file_data = array();
				$file_data['article_id'] = $item->id;
				$file_data['publish_up'] = $item->publish_up;
				$file_data['publish_down'] = $item->publish_down;
				$file_data['metakey'] = $item->metakey;
				$file_data['metadesc'] = $item->metadesc;
				$javascript = '<script type="text/javascript">var file_data = '.json_encode($file_data).'</script>';
				
				$db->setQuery("SELECT `introtext`, `fulltext` FROM #__content WHERE `id` = '".$item->id."'");
				$article = $db->loadAssoc();
				
				$buffer = $article['introtext'].$article['fulltext']."\n".$javascript;
				JFile::write($file, $buffer);
				
				if(!$this->addFileIndex($item)){
					JError::raiseError(500, "Problem with file indexing!");
					return false;
				}
			}
		}
		
		if(!$this->writeHtaccess()){
			JError::raiseError(500, ".htaccess have not saved in static_content directory!");
			return false;
		}
	}
	
	function writeHtaccess(){
		
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );
		
		$content = '<Files ~ ".(php)$">';
		$content .= "\n". 'Deny from all';
		$content .= "\n". '</Files>';
		
		if(!JFile::write(JPATH_SITE.'/static_content/.htaccess', $content)) return false;
		
		return true;
	}
	
	function addFileIndex($item){
	
		$db = JFactory::getDBO();
				
		$path = ($item->category_title != 'Uncategorised') ? strtolower(str_replace(" ", "_", $item->category_title))."/" : "";
		$query = $db->getQuery(true);
		$query->insert("#__staticcontent_file_index")->set("`id` = '', `filename_full`=".$db->quote($item->alias).", `modified_date` = '".time()."', `article_id` = '".$item->id."', `path` = '".$path."'");		
		if(!$db->setQuery($query)->query()) return false;
		
		return true;
	}
	
	function greetingText($is_upgrade){
		?>
		
		<?php
	}
}
?>