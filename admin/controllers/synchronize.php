<?php
/**
* Staticcontent Deluxe Component for Joomla 3
* @package Staticcontent Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');
 
jimport('joomla.application.component.controlleradmin');
 
/**
 * Synchronize Controller
 */
class StaticcontentControllerSynchronize extends JControllerAdmin
{
        /**
         * Proxy for getModel.
         * @since       1.6
         */
        public function getModel($name = 'Synchronize', $prefix = 'StaticcontentModel', $config = array('ignore_request' => true))
        {
                $model = parent::getModel($name, $prefix, $config);
                return $model;
        }
		
		public function start()
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * FROM #__staticcontent_file_index");
			$file_index = $db->loadObjectList();
			
			jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );
		
			ignore_user_abort(false); // STOP script if User press 'STOP' button
			@set_time_limit(0);
			@ob_end_clean();
			@ob_start();
			echo "<script>function getObj_frame(name) {"
				. " if (parent.document.getElementById) { return parent.document.getElementById(name); }"
				. "	else if (parent.document.all) { return parent.document.all[name]; }"
				. "	else if (parent.document.layers) { return parent.document.layers[name]; }}</script>";
			@flush();
			@ob_flush();
			sleep(1);
			
			$files = JFolder::files(JPATH_SITE.'/static_content/', '.', true, true, array('.htaccess'));
			
			if(count($files)){
				$article_cat_id = $this->checkCategories($files);
				foreach($files as $i => $file){
					
					$notice = '';
					$temp_index = array();
					$remove = JPATH_SITE. DIRECTORY_SEPARATOR . 'static_content'. DIRECTORY_SEPARATOR; 
					$file = str_replace($remove, "", $file);
					$content = JFile::read(JPATH_SITE. DIRECTORY_SEPARATOR .'static_content'. $file);
					if(preg_match_all('/<script[^>]*>(.*?)<\/script>/', $content, $match)){
						
						$code = '';
						$r = 0;
						if(isset($match[1]) && is_array($match[1])){
							for($n=0; $n <= count($match[1]) - 1; $n++){
								if(preg_match('/file_data/', $match[1][$n], $tmp)){
									$r = $n;
									break;
								}
							}
							
							$code = (!empty($tmp)) ? $match[1][$r] : '';
							$content = ($code != '') ? str_replace($match[0][$r], "", $content) : $content;
							
						} elseif(isset($match[1]) && is_string($match[1])){
							
							if(preg_match('/file_data/', $match[1], $tmp)){
								$code = $match[1];
							}
							
							$content = ($code != '') ? str_replace($match[0], "", $content) : $content;
							
						}
						
						if($code != '' && preg_match('/(\{[^\}]*\})/', $code, $json)){
								$temp_index = json_decode($json[0]);
						}
					}
					
					$status = '';
					if(empty($temp_index)){
						$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/failed.png" />';
						$notice = '<font color="red">'.JText::_('COM_STATICCONTENT_INDEX_OF_FILE_WAS_DELETED').'</font>';
					} else {

						if(count($file_index)){
							$file_found = false;
							
							foreach($file_index as $ii => $index){
								if(basename($file) == $index->filename_full.'.html'){
									
									$is_replace = $this->checkDirectory($file, $index, $article_cat_id);
									$mdate = filemtime(JPATH_SITE. DIRECTORY_SEPARATOR .'static_content'. $file);
									
									//The file was changed
									if( $mdate != $index->modified_date ){
																
										if($temp_index->article_id == $index->article_id){
											
											$params = array();
											$params['content'] = $content;
											$params['mdate'] = $mdate;
											$params['index'] = $index;
											$params['temp_index'] = $temp_index;
											$params['file'] = $file;
											
											$this->changeContent($params);
											
											$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/success.png" />';
											$notice = '<font color="green">'.JText::_('COM_STATICCONTENT_THE_ARTICLE_HAS_MODIFIED').'</font>';
											
											$file_found = true;
											break;
										} else {
											$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/failed.png" />';
											$notice = '<font color="red">'.JText::_('COM_STATICCONTENT_ARTICLE_ID_MISMATCHED').'</font>';
											$file_found = true;
											break;
										}
										
									} else {
										if($is_replace){
											$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/success.png" />';
											$notice = '<font color="green">'.JText::_('COM_STATICCONTENT_THE_FILE_WAS_REPLACED').'</font>';
											$file_found = true;
											break;
										} else {
											$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/success.png" />';
											$notice = '<font color="green">'.JText::_('COM_STATICCONTENT_THE_FILE_WAS_NOT_MODIFIED').'</font>';
											$file_found = true;
											break;
										}
									}
									
								}
							}
							
							if(!$file_found){
								
								$is_new_file = true;
								foreach($file_index as $index){
									
									// file was Renamed
									if($temp_index->article_id == $index->article_id){
										
										$filename = basename($file);
										$title = ucfirst(str_replace("-", " " , str_replace(".html", "", $filename)));
										$alias = str_replace(".html", "", $filename);
										
										$db->setQuery("UPDATE #__content SET `title` = ".$db->quote($title).", `alias` = ".$db->quote($alias)." WHERE `id` = '".$index->article_id."'");
										$db->query();
										
										$db->setQuery("UPDATE #__staticcontent_file_index SET filename_full = ".$db->quote($alias)." WHERE article_id = '".$index->article_id."'");
										$db->query();
										
										$is_replace = $this->checkDirectory($file, $index, $article_cat_id);
										$mdate = filemtime(JPATH_SITE. DIRECTORY_SEPARATOR .'static_content'. $file);
										
										if($mdate != $index->modified_date){
											
											$params = array();
											$params['content'] = $content;
											$params['mdate'] = $mdate;
											$params['index'] = $index;
											$params['temp_index'] = $temp_index;
											$params['file'] = $file;
											
											$this->changeContent($params);
										}
										
										if($is_replace){
											$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/success.png" />';
											$notice = '<font color="green">'.JText::_('COM_STATICCONTENT_THE_FILE_WAS_RENAMED_AND_REPLACED').'</font>';
											$is_new_file = false;
											break;
										} else {
											$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/success.png" />';
											$notice = '<font color="green">'.JText::_('COM_STATICCONTENT_THE_FILE_WAS_RENAMED').'</font>';
											$is_new_file = false;
											break;
										}
									}
								}
								
								//The file was created
								if($is_new_file){
									
									$mdate = filemtime(JPATH_SITE. DIRECTORY_SEPARATOR .'static_content'. $file);	
									$params = array();
									$params['content'] = $content;
									$params['mdate'] = $mdate;
									$params['index'] = $index;
									$params['temp_index'] = $temp_index;
									$params['file'] = $file;
									$params['article_cat_id'] = $article_cat_id;
											
									$article_id = $this->newArticle($params);
									
									$tmp_file = str_replace(basename($file), "", $file);
									$path = substr($tmp_file, 1, strlen($tmp_file));
			
									$filename = str_replace(".html", "", basename($file));
									$db->setQuery("INSERT INTO #__staticcontent_file_index (`id`, `filename_full`, `modified_date`, `article_id`, `path`) VALUES ('', ".$db->quote($filename).", '".$mdate."', '".$article_id."', ".$db->quote($path).")");
									$db->query();
									
									$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/success.png" />';
									$notice = '<font color="green">'.JText::_('COM_STATICCONTENT_ARTICLE_WAS_CREATED').'</font>';
								}
								
							}
							
						} else {
							$status = '<img src="'.JURI::base().'components/com_staticcontent/assets/images/failed.png" />';
							$notice = '<font color="red">'.JText::_('COM_STATICCONTENT_INDEXATION_WAS_MISSED').'</font>';
						}
					}
					
					$filename = basename($file);
					$tmp_file = str_replace(basename($file), "", $file);
					$path = substr($tmp_file, 1, strlen($tmp_file));
					$path = addslashes($path);
					
					echo '<script type="text/javascript">
							  
							  var log_text = getObj_frame("log_text");
							  var log_bar = getObj_frame("log_bar");
							  var tbl_elem = getObj_frame("log_table");
							  var install_progress = getObj_frame("install_progress");
							  
							  var row = tbl_elem.insertRow(tbl_elem.rows.length);
							  var cell1 = document.createElement("td");
							  var cell2 = document.createElement("td");
							  var cell3 = document.createElement("td");
							  var cell4 = document.createElement("td");
							  var cell5 = document.createElement("td");
							  var cell6 = document.createElement("td");
							  
							  var number = 	document.createElement("span");
							  number.innerHTML = "'.($i+1).'";
							  var filename = 	document.createElement("span");
							  filename.innerHTML = "'.$filename.'";
							  var path = 	document.createElement("span");
							  path.innerHTML = \''.$path.'\';
							  var date = 	document.createElement("span");
							  date.innerHTML = "'.date('d-m-Y H:i:s', time()).'";
							  var status = document.createElement("span");
							  status.innerHTML = \''.$status.'\';
							  var notice = document.createElement("span");
							  notice.innerHTML = \''.$notice.'\';
							  
							  cell1.appendChild(number);
							  cell2.appendChild(filename);
							  cell3.appendChild(path);
							  cell4.appendChild(date);
							  cell5.appendChild(status);
							  cell6.appendChild(notice);
							  
							  row.appendChild(cell1);
							  row.appendChild(cell2);
							  row.appendChild(cell3);
							  row.appendChild(cell4);
							  row.appendChild(cell5);
							  row.appendChild(cell6);
							  
							  var max_width = install_progress.clientWidth;
							  var count_files = '.count($files).';
							  log_bar.style.width = ('.($i+1).' * (max_width/count_files)) + "px";
							  
							  var dtop = (log_text.scrollHeight - 500) + 35;
							  log_text.scrollTop += dtop;
							  
						  </script>
					';
					@flush();
					@ob_flush();
					sleep(1);
				}
				
				echo '<script type="text/javascript">
						
						var tbl_elem = getObj_frame("log_table");
						var row = tbl_elem.insertRow(tbl_elem.rows.length);
						var cell1 = document.createElement("td");
						
						cell1.setAttribute("colspan", "6");
						var message = 	document.createElement("span");
						message.style.color = "green";
						message.innerHTML = "'.JText::_('COM_STATICCONTENT_PROCESS_COMPLETED').'";
						cell1.appendChild(message);
						row.appendChild(cell1);
						
						var install_progress = getObj_frame("install_progress");
						install_progress.className = "progress";
						
						var log_bar = getObj_frame("log_bar");
						log_bar.className = "bar-success bar";
						
						var start_button = getObj_frame("start_button");
						start_button.disabled = false;
					  </script>';
				
			} else {
				echo '<script type="text/javascript">
					
					var log_bar = getObj_frame("log_bar");
					log_bar.style.width = "100%";
					
					var start_button = getObj_frame("start_button");
					start_button.disabled = false;
					
					var tbl_elem = getObj_frame("log_table");
				    var row = tbl_elem.insertRow(tbl_elem.rows.length);
				    var cell1 = document.createElement("td");
					
					cell1.setAttribute("colspan", "6");
					var message = 	document.createElement("span");
					message.style.color = "red";
				    message.innerHTML = "'.JText::_('COM_STATICCONTENT_THERE_ARE_NO_FILES').'";
					cell1.appendChild(message);
					row.appendChild(cell1);
					
					var install_progress = getObj_frame("install_progress");
					install_progress.className = "progress";
					var log_bar = getObj_frame("log_bar");
					log_bar.className = "bar-success bar";
					
				  </script>';
				@flush();
				@ob_flush();
			}
			
			die();
		}
		
		public function changeContent($params)
		{
			$db = JFactory::getDBO();
						
			$db->setQuery("UPDATE `#__content` SET `introtext` = ".$db->quote($params['content']).", `fulltext` = '', `modified` = '".date('Y-m-d H:i:s', $params['mdate'])."' WHERE `id` = '".$params['index']->article_id."'");
			$db->query();
			
			$db->setQuery("UPDATE #__staticcontent_file_index SET `modified_date` = '".$params['mdate']."' WHERE `article_id` = '".$params['index']->article_id."'");
			$db->query();
			
			return true;											
		}
		
		public function checkDirectory($file, $index, $article_cat_id)
		{
			$tmp_file = str_replace(basename($file), "", $file);
			$path = substr($tmp_file, 1, strlen($tmp_file));
			
			$is_replace = true;
			$db = JFactory::getDBO();
			$user = JFactory::getUser();
			if($index->path == $path){
				$path = $index->path;
				$is_replace = false;
			}
			
			$db->setQuery("UPDATE #__staticcontent_file_index SET `path` = ".$db->quote($path)." WHERE `article_id` = '".$index->article_id."'");
			$db->query();
			
			if(isset($article_cat_id[$file])){
				$db->setQuery("UPDATE #__content SET `catid` = '".$article_cat_id[$file]."' WHERE `id` = '".$index->article_id."'");
				$db->query();
			}
			
			return $is_replace;
		}
		
		public function checkCategories($files){
			
			$article_cat_id = array();
			$remove = JPATH_SITE. DIRECTORY_SEPARATOR . 'static_content'. DIRECTORY_SEPARATOR;
			$user = JFactory::getUser();
			$db = JFactory::getDBO();
			if(count($files)){
				foreach($files as $file){
				 
					$file = str_replace($remove, "", $file);
					$tmp_file = str_replace(basename($file), "", $file);
					$path = substr($tmp_file, 1, strlen($tmp_file));
					
					if($path != ''){
						
						$directories = array();
						$directories = explode(DIRECTORY_SEPARATOR, $path);
						
						if(!empty($directories)){
							$c = 0;
							for($n = 0; $n < count($directories); $n++){
								
								if($directories[$c] != ''){
									
									$title = array();
									$words = explode("_", $directories[$c]);
									if(count($words)){
										foreach($words as $word){
											$title[] = ucfirst($word);
										}
									}
									
									$title = (count($title)) ? implode(" ", $title) : ucfirst($directories[$c]);
									$directories[$c] = str_replace("_", "-", $directories[$c]);
									$alias = $directories[$c];
									$db->setQuery("SELECT `id` FROM #__categories WHERE `title` LIKE '%".$title."%' AND `extension` = 'com_content'");
									$catid = $db->loadResult();
									
									if(!$catid){
										
										if(!$c){
											$parent_id = 1;
										} else {
											$parent_id = $insert_id;
										}
										
										$db->setQuery("SELECT MAX(asset_id) as asset_id, MAX(rgt) as rgt FROM #__categories");
										$assets = $db->loadAssoc();
										
										$data = new stdClass;
										$data->id = '';
										$data->asset_id = $assets['asset_id'] + 1;
										$data->parent_id = $parent_id;
										$data->lft = $assets['rgt'] + 1;
										$data->rgt = $assets['rgt'] + 2;
										$data->level = $c + 1;
										$data->path = $alias;
										$data->extension = 'com_content';
										$data->title = ucfirst(str_replace("-", " ", $alias));
										$data->alias = $alias;
										$data->published = '1';
										$data->checked_out = '0';
										$data->checked_out_time = '0000-00-00 00:00:00';
										$data->access = '1';
										$data->params = '{"target":"","image":""}';
										$data->metadata = '{"author":"","robots":""}';
										$data->created_user_id = $user->id;
										$data->created_time = date("Y-m-d H:i:s", time());
										$data->version = '1';
										
										$db->insertObject("#__categories", $data);
										$insert_id = $db->insertid();
										
										$article_cat_id[$file] = $insert_id;
										
									} else {
										$article_cat_id[$file] = $catid;
									}
									
									$c++;
								}
							}
						}
					}
				}
			}
			
			return $article_cat_id;
		}
		
		public function newArticle($params){
			
			jimport( 'joomla.filesystem.folder' );
			jimport( 'joomla.filesystem.file' );
			
			$user = JFactory::getUser();
			$db = JFactory::getDBO();
			$db->setQuery("SELECT MAX(asset_id) as asset_id FROM #__content");
			$assets = $db->loadAssoc();
			
			$alias = str_replace(".html", "", basename($params['file']));
			$title = $params['temp_index']->title;
			
			$data = new stdClass;
			$data->id = '';
			$data->asset_id = $assets['asset_id'] + 1;
			$data->title = $title;
			$data->alias = $alias;
			$data->introtext = $params['content'];
			$data->fulltext = '';
			$data->state = 1;
			$data->catid = $params['article_cat_id'][$params['file']];
			$data->created = date("Y-m-d H:i:s", time());
			$data->created_by = $user->id;
			$data->created_by_alias = $user->name;
			$data->modified = date("Y-m-d H:i:s", time());
			$data->modified_by = $user->id;
			$data->checked_out = 0;
			$data->checked_out_time = '0000-00-00 00:00:00';
			$data->publish_up = $params['temp_index']->publish_up;
			$data->publish_down = $params['temp_index']->publish_down;
			$data->images ='{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}';
			$data->urls = '{"urla":"","urlatext":"","targeta":"","urlb":"","urlbtext":"","targetb":"","urlc":"","urlctext":"","targetc":""}';
			$data->attribs = '{"show_title":"","link_titles":"","show_intro":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}';
			$data->version = 1;
			$data->ordering = 0;
			$data->metakey = $params['temp_index']->metakey;
			$data->metadesc = $params['temp_index']->metadesc;
			$data->access = 1;
			$data->hits = 0;
			$data->metadata = '{"robots":"","author":"","rights":"","xreference":""}';
			$data->featured = 0;
			$data->language = '*';
			
			$db->insertObject("#__content", $data);
			$article_id = $db->insertid();
			
			$javascript = '<script type="text/javascript">var file_data = {"article_id":"'.$article_id.'", "title":"'.$title.'", "publish_up":"'.$params['temp_index']->publish_up.'","publish_down":"'.$params['temp_index']->publish_down.'","metakey":"'.$params['temp_index']->metakey.'","metadesc":"'.$params['temp_index']->metadesc.'"}</script>';
			$content = $params['content']."\n".$javascript;
			
			JFile::write(JPATH_SITE. DIRECTORY_SEPARATOR ."static_content". DIRECTORY_SEPARATOR .$params['file'], $content);
			
			return $article_id;
		}
		
		public function log2html(){
			
			$html_log = '<style>.table-striped tbody tr:nth-child(2n+1) td, .table-striped tbody tr:nth-child(2n+1) th {background-color: #F9F9F9;}.table th, .table td {border-top: 1px solid #DDDDDD;line-height: 18px;padding: 8px;text-align: left;vertical-align: top;}</style>';
			$html_log .= $_REQUEST['html_log'];
			$html_log = rawurldecode($html_log);
			
			$UserBrowser = '';
			if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $_SERVER['HTTP_USER_AGENT'])) $UserBrowser = "IE";
			header("Content-Type: text/html");
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header("Content-Length: ".strlen($html_log)); 
			if ($UserBrowser == 'IE') {
				header("Content-Disposition: inline; filename=log_".date('d_m_Y').".html ");
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			} else {
				header("Content-Disposition: inline; filename=log_".date('d_m_Y').".html ");
				header('Pragma: no-cache');
			}
			
			echo $html_log;
			die();
		}
}