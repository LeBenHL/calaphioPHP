<?php
/*
 * $RCSfile: WebCamSiteAdmin.inc,v $
 *
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2005 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */
/**
 * @version $Revision: 1.7 $ $Date: 2005/08/23 03:49:57 $
 * @package WebCam
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * @package WebCam
 * @subpackage UserInterface
 */
class WebCamSiteAdminController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	/* Make sure we have adequate permissions */
	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = array();
	$error = array();
	if (isset($form['action']['save'])) {
	    if (isset($form['duration'])) {
		/* Accept input for locales that use comma as fraction separator */
		$form['duration'] = str_replace(',', '.', $form['duration']);
	    }
	    if (!isset($form['duration']) || !is_numeric($form['duration'])) {
		$error[] = 'form[error][duration]';
	    } else {
		$ret = GalleryCoreApi::setPluginParameter(
		    'module', 'webcam', 'duration', round($form['duration'] * 60));
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		$status['saved'] = 1;
	    }
	}

	$method = empty($error) ? 'redirect' : 'delegate';
	$results[$method]['view'] = 'core.SiteAdmin';
	$results[$method]['subView'] = 'webcam.WebCamSiteAdmin';
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package WebCam
 * @subpackage UserInterface
 */
class WebCamSiteAdminView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	if ($form['formName'] != 'WebCamSiteAdmin') {
	    $form['formName'] = 'WebCamSiteAdmin';
	    list ($ret, $duration) =
		GalleryCoreApi::getPluginParameter('module', 'webcam', 'duration');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    /* String conversion here may use comma as fraction separator, based on locale */
	    $form['duration'] = (string)round($duration / 60, 2);
	}

	$template->setVariable('controller', 'webcam.WebCamSiteAdmin');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/webcam/templates/WebCamSiteAdmin.tpl'));
    }
}
?>
