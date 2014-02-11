<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$cacheKey = 'npcacheable';
if( !is_array( $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ $cacheKey ] ) ){
	$cacheConfig =  $TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ $cacheKey ] = array();
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ $cacheKey ]['frontend'] = 'TYPO3\\CMS\\Core\\Cache\\Frontend\\VariableFrontend';
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ $cacheKey ]['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\Typo3DatabaseBackend';
	$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations'][ $cacheKey ]['options'] = array();
}

?>
