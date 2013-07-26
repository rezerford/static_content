<?php
/**
* Staticcontent component for Joomla 3.0
* @package Staticcontent
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
?>
<style>
	#log_text
	{
		overflow-y:scroll;
		overflow-x:hidden;
		height:500px;
		border:1px solid #ccc;
		border-radius:10px;
	}
	
	.table-striped thead tr,th
	{
		border-bottom:1px solid #ddd;
	}
	
	.button-toolbar
	{
		margin:15px;
		dispay:block;
		float:right;
	}
</style>
<script type="text/javascript">
Joomla.submitbutton = function(task)
	{
		var form = document.adminForm;
		// do field validation
		if (task == 'synchronize.log2html'){
			var log_text = document.getElementById("log_text");
			var log = log_text.innerHTML;
						
			var log_win = window.open("", "log_win");
			log_win.document.write("<form action='index.php?option=com_staticcontent&task=synchronize.log2html' method='post' name='adminForm' id='adminForm'><textarea name='html_log' id='html_log' style='visibility:hidden;'></textarea></form>");
			
			var html_log = log_win.document.getElementById("html_log");
			html_log.value = encodeURIComponent(log);
			
			log_win.document.adminForm.submit();
		} else {
			Joomla.submitform(task);
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_staticcontent&view=synchronize'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif;?>
	
	<div class="progress progress-striped active" id="install_progress">
		<div class="bar" style="width: 0%;" id="log_bar"></div>
	</div>
	<div class="log-container" id="log_text">
		<table class="table table-striped" id="log_table">
				<tr>
					<th width="1%" class="nowrap center">#</th>
					<th class="nowrap"><?php echo JText::_('COM_STATICCONTENT_FILENAME')?></th>
					<th class="nowrap"><?php echo JText::_('COM_STATICCONTENT_PATH')?></th>
					<th class="nowrap"><?php echo JText::_('COM_STATICCONTENT_DATE')?></th>
					<th class="nowrap"><?php echo JText::_('COM_STATICCONTENT_STATUS')?></th>
					<th class="nowrap"><?php echo JText::_('COM_STATICCONTENT_NOTICE')?></th>
				</tr>
		</table>
	</div>
	<div style="clear:both;"></div>
	<div class="button-toolbar">
		<input type="button" class="btn" id="start_button" value="<?php echo JText::_('COM_STATICCONTENT_SYNCHRONIZE')?>" onclick="javascript:startSynchronize();" />
		<input type="button" class="btn" value="<?php echo JText::_('COM_STATICCONTENT_STOP')?>" onclick="javascript:stopSynchronize();" />
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
	
    </div>
	
</form>
<iframe src="" style="display:none " id="synch_frame">
</iframe>
<script language="javascript" type="text/javascript">
		<!--
		function getObj_frame(name) {
			if (parent.document.getElementById) { return parent.document.getElementById(name); }
			else if (parent.document.all) { return parent.document.all[name]; }
			else if (parent.document.layers) { return parent.document.layers[name]; }
		}
		
		function startSynchronize() {
		
			var log_bar = getObj_frame("log_bar");
			log_bar.style.width = "0px";
			var install_progress = getObj_frame("install_progress");
			install_progress.className = "progress progress-striped active";
			log_bar.className = "bar";
			
			var tbl_elem = getObj_frame("log_table");
			var row = tbl_elem.insertRow(tbl_elem.rows.length);
			var cell1 = document.createElement("td");
			
			cell1.setAttribute("colspan", "6");
			var message = 	document.createElement("span");
			message.style.color = "green";
			message.innerHTML = "<?php echo JText::_('COM_STATICCONTENT_PROCESS_HAS_STARTED')?>";
			cell1.appendChild(message);
			row.appendChild(cell1);
						
			var form = document.adminForm;
			document.getElementById('start_button').disabled = true;
			var synch_frame = document.getElementById('synch_frame');
			synch_frame.src = 'index.php?option=com_staticcontent&tmpl=component&task=synchronize.start';
		}
		
		function StopSynchronize() {
			if (!document.all)
				for (var i=0;i<top.frames.length;i++)
				  top.frames[i].stop()
			else
				for (var i=0;i<top.frames.length;i++)
				  top.frames[i].document.execCommand('Stop')
		}
		//-->
</script>
