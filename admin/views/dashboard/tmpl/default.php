<?php
/**
* Staticcontent component for Joomla 3.0
* @package Staticcontent
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_staticcontent/assets/css/staticcontent.css');

$imgpath = JURI::root().'/administrator/components/com_staticcontent/assets/images/';
JHtml::_('behavior.tooltip');
?>
<div id="pgm_dashboard">
    <?php
	if(count($this->dashboardItems)){
		foreach($this->dashboardItems as $ditem) { ?>
			<div onclick="window.location ='<?php echo $ditem->url; ?>'" class="pgm-dashboard_button">
				<?php if ($ditem->icon) { ?>
					<img src="<?php echo $ditem->icon ?>" class="pmg-dashboard_item_icon"/>
				<?php } ?>
				<?php echo '<div class="pgm-dashboard_button_text">'.$ditem->title.'</div>'?>
			</div>
   <?php } 
   }?>
</div>
<div id="pgm_collapse">
<div class="accordion" id="accordion2">
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                About Static Content
            </a>
        </div>
        <div id="collapseOne" class="accordion-body collapse in">
            <div class="accordion-inner">
                <table border="1" width="100%" class="about_table" >
                    <tr>
                        <th colspan="2" class="a_comptitle">
                            <strong><?php echo JText::_('COM_STATICCONTENT'); ?></strong> component for Joomla! 3.0 Developed by
                            <a href="http://www.JoomPlace.com">JoomPlace</a>.
                        </th>
                    </tr>
                    <tr>
                        <td width="13%"  align="left">Installed version:</td>
                        <td align="left">&nbsp;<b>0.1</b>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">Latest version:</td>
                        <td>
                            <div id="jq_LatestVersion">
                                <script type="text/javascript">
                                    jq_CheckVersion();
                                </script>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td valign="top" align="left">About:</td>
                        <td align="left"><?php echo JText::_('COM_STATICCONTENT_ABOUT_TEXT'); ?></td>
                    </tr>
                    <tr>
                        <td align="left">Community Forum:</td>
                        <td align="left"><a target="_blank" href="http://www.joomplace.com/forum/joomla-components/joomlaquiz-deluxe.html">http://www.joomplace.com/forum/joomla-components/joomlaquiz-deluxe.html</a></td>
                    </tr>
                    <tr>
                        <td align="left">Support Helpdesk:</td>
                        <td align="left"><a target="_blank" href="http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create">http://www.joomplace.com/support/helpdesk/post-purchase-questions/ticket/create</a></td>
                    </tr>
                    </table>
            </div>
        </div>
    </div>
    <div class="accordion-group">
        <div class="accordion-heading">
            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                <?php echo JText::_("COM_STATICCONTENT_ABOUT_SAYTHANKSTITLE"); ?>
            </a>
        </div>
        <div id="collapseTwo" class="accordion-body collapse">
            <div class="accordion-inner">
                <div class="thank_fdiv" style="font-size:12px;">
                    <?php echo JText::_("COM_STATICCONTENT_ABOUT_SAYTHANKS1"); ?>
                    <a href="http://extensions.joomla.org/extensions/vertical-markets/education-a-culture/quiz/11302" target="_blank">http://extensions.joomla.org/</a>
                    <?php echo JText::_("COM_STATICCONTENT_ABOUT_SAYTHANKS2"); ?>
                </div>
                <div style="float:right; margin:3px 5px 5px 5px;">
                    <a href="http://extensions.joomla.org/extensions/vertical-markets/education-a-culture/quiz/11302" target="_blank">
                        <img src="http://www.joomplace.com/components/com_jparea/assets/images/rate-2.png" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>