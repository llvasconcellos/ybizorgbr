<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
 
class PhocaDownloadCpViewPhocaDownloadLinkFile extends JViewLegacy
{
	var $_context 	= 'com_phocadownload.phocadownloadlinkfile';

	function display($tpl = null) {
		$app = JFactory::getApplication();
		$uri		=& JFactory::getURI();
		$document	=& JFactory::getDocument();
		$db		    =& JFactory::getDBO();
		
		//Frontend Changes
		$tUri = '';
		if (!$app->isAdmin()) {
			$tUri = JURI::base();
			require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_phocadownload'.DS.'helpers'.DS.'phocadownload.php' );
		}
		
		JHTML::stylesheet( 'administrator/components/com_phocadownload/assets/phocadownload.css' );
		
		$eName				= JRequest::getVar('e_name');
		$tmpl['ename']		= preg_replace( '#[^A-Z0-9\-\_\[\]]#i', '', $eName );
		$tmpl['type']		= JRequest::getVar( 'type', 1, '', 'int' );
		$tmpl['backlink']	= $tUri.'index.php?option=com_phocadownload&amp;view=phocadownloadlinks&amp;tmpl=component&amp;e_name='.$tmpl['ename'];
		
		
		$params = JComponentHelper::getParams('com_phocadownload') ;

		//Filter
		$context			= 'com_phocadownload.phocadownload.list.';
		//$sectionid			= JRequest::getVar( 'sectionid', -1, '', 'int' );
		//$redirect			= $sectionid;
		$option				= JRequest::getCmd( 'option' );
		
		$filter_state		= $app->getUserStateFromRequest( $this->_context.'.filter_state',	'filter_state', '',	'word' );
		$filter_catid		= $app->getUserStateFromRequest( $this->_context.'.filter_catid',	'filter_catid', 0,	'int' );
		$catid				= $app->getUserStateFromRequest( $this->_context.'.catid',	'catid', 0,	'int');
	//	$filter_sectionid	= $app->getUserStateFromRequest( $this->_context.'.filter_sectionid','filter_sectionid',	-1,	'int');
		$filter_order		= $app->getUserStateFromRequest( $this->_context.'.filter_order',	'filter_order',		'a.ordering', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $this->_context.'.filter_order_Dir',	'filter_order_Dir',	'', 'word' );
		$search				= $app->getUserStateFromRequest( $this->_context.'.search','search', '', 'string' );
		$search				= JString::strtolower( $search );

		// Get data from the model
		$items		= & $this->get( 'Data');
		$total		= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );
		
		// build list of categories
	
		if ($tmpl['type'] != 4) {
			$javascript = 'class="inputbox" size="1" onchange="submitform( );"';
		} else {
			$javascript	= '';
		}
		// get list of categories for dropdown filter	
		$filter = '';
		
		//if ($filter_sectionid > 0) {
		//	$filter = ' WHERE cc.section = '.$db->Quote($filter_sectionid);
		//}

		// get list of categories for dropdown filter
		$query = 'SELECT cc.id AS value, cc.title AS text' .
				' FROM #__phocadownload_categories AS cc' .
				$filter .
				' ORDER BY cc.ordering';
				
		if ($tmpl['type'] != 4) {
             $lists['catid'] = PhocaDownloadCategory::filterCategory($query, $catid, null, true, true);
        } else {
             $lists['catid'] = PhocaDownloadCategory::filterCategory($query, $catid, null, false, true);
        }
		/*
		if ($tmpl['type'] != 4) {
			$lists['catid'] = PhocaDownloadCategory::filterCategory($query, $catid, null, true);
		} else {
			$lists['catid'] = PhocaDownloadCategory::filterCategory($query, $catid, null, false);
		}*/
		
		// sectionid
		/*$query = 'SELECT s.title AS text, s.id AS value'
		. ' FROM #__phocadownload_sections AS s'
		. ' WHERE s.published = 1'
		. ' ORDER BY s.ordering';
		
		$lists['sectionid'] = PhocaDownloadCategory::filterSection($query, $filter_sectionid);*/
		
		// state filter
		$lists['state']	= JHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['order'] 	= $filter_order;

		// search filter
		$lists['search']= $search;
		

		$this->assignRef('user',		JFactory::getUser());
		$this->assignRef('lists',		$lists);
		$this->assignRef('tmpl',		$tmpl);
		$this->assignRef('items',		$items);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('request_url',	$uri->toString());
		
		parent::display($tpl);
	}
}
?>