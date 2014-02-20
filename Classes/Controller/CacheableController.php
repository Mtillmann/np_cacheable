<?php
namespace NetworkPublishingGmbH\NpCacheable\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Martin Tillmann <tillmann@network-publishing.de>, network.publishing GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */

class CacheableController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * must be the same key as specified in ext_localconf
	 */
	protected $cacheKey = 'npcacheable';

	/**
	 * There are three supported annotations for your controller actions:
	 * * cacheable
	 *   if this is set the action's output will be cached
	 * * cacheTTL <int>
	 *   Time to live (lifetime) of the cache entry
	 * * cacheTags <tag> <tag> ...
	 *   space-separated list of tags for the cache entry
	 *
	 * @return mixed
	 */
	protected function callActionMethod() {
		$annotations = $this->reflectionService->getMethodTagsValues(get_class($this), $this->actionMethodName);

		if( !isset($annotations['cacheable'])) {
			parent::callActionMethod();
			return;
		}

		$cacheTags = isset($annotations['cacheTags'],$annotations['cacheTags'][0])?preg_split('/\s+/',$annotations['cacheTags'][0]):null;
		$cacheTTL = isset($annotations['cacheTTL'],$annotations['cacheTTL'][0])?(int)$annotations['cacheTTL'][0]:null;

		$preparedArguments = array();
		foreach ($this->arguments as $argument) {
			$preparedArguments[] = $argument->getValue();
		}

		$key = md5(get_class($this).$this->actionMethodName.var_export($preparedArguments,true).var_export($_REQUEST,true));
		if( $GLOBALS['typo3CacheManager']->getCache($this->cacheKey)->has($key) ){
			return $this->response->appendContent(
				$GLOBALS['typo3CacheManager']->getCache($this->cacheKey)->get($key)
			);
		}

		$contentStart = mb_strlen($this->response);
		parent::callActionMethod();

		//cut only what is newly generated from response and
		//discard possibly existing content!
		$content = mb_substr($this->response,$contentStart);

		$GLOBALS['typo3CacheManager']->getCache($this->cacheKey)->set($key, $content,$cacheTags,$cacheTTL);
		return true;
	}

	/**
	 * This function may be called by the extending controller once, when you
	 * are using older TYPO3 versions that do not use caching framework
	 * by default and you find that the caching framework is not
	 * available. (Be sure to compare database in in install tool if you
	 * use Typo3DatabaseBackend!)
	 *
	 * @return void
	 */
	protected function initializeCaching(){
  		\TYPO3\CMS\Core\Cache\Cache::initializeCachingFramework();
   		try{
   			$cache = $GLOBALS['typo3CacheManager']->getCache($this->cacheKey);
  		}
  		catch( \TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException $e ){
   			$cache = $GLOBALS['typo3CacheFactory']->create(
    			$this->cacheKey,
    			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->cacheKey]['frontend'],
    			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->cacheKey]['backend'],
    			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$this->cacheKey]['options']
   			);
        }
	}

}
