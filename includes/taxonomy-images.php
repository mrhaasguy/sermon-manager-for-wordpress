<?php

//include the main class file
require_once("tax-meta-class/Tax-meta-class.php");
if (is_admin()){
	/* 
	 * prefix of meta keys, optional
	 * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
	 *  you also can make prefix empty to disable it
	 * 
	 */
	//$prefix = 'wpfc_';
	/* 
	 * configure your meta box
	 */
	$config = array(
		'id' => 'preacher_meta_box',					// meta box id, unique per meta box
		'title' => 'Preacher Meta Box',					// meta box title
		'pages' => array('wpfc_preacher'),			// taxonomy name, accept categories, post_tag and custom taxonomies
		'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
		'fields' => array(),						// list of meta fields (can be added by field arrays)
		'local_images' => true,				 	    // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => false					//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	
	/*
	 * Initiate your meta box
	 */
	$my_meta =  new Tax_Meta_Class($config);
	
	/*
	 * Add fields to your meta box
	 */
	
	//Image field
	$my_meta->addImage('wpfc_preacher_image',array('name'=> 'Photo '));
	
	/*
	 * Don't Forget to Close up the meta box decleration
	 */
	//Finish Meta Box Decleration
	$my_meta->Finish();
	
	$seriesconfig = array(
		'id' => 'series_meta_box',					// meta box id, unique per meta box
		'title' => 'Series Meta Box',					// meta box title
		'pages' => array('wpfc_sermon_series'),			// taxonomy name, accept categories, post_tag and custom taxonomies
		'context' => 'normal',						// where the meta box appear: normal (default), advanced, side; optional
		'fields' => array(),						// list of meta fields (can be added by field arrays)
		'local_images' => true,				 	    // Use local or hosted images (meta box images for add/remove)
		'use_with_theme' => true					//change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
	);
	/*
	 * Initiate your meta box
	 */
	$series_meta =  new Tax_Meta_Class($seriesconfig);
	
	/*
	 * Add fields to your meta box
	 */
	
	//Image field
	$series_meta->addImage('wpfc_series_image',array('name'=> 'Series Graphic '));
	
	/*
	 * Don't Forget to Close up the meta box decleration
	 */
	//Finish Meta Box Decleration
	$series_meta->Finish();

}