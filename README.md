np_cacheable
============

np_cacheable - cacheable extbase controller actions

By using `\NetworkPublishingGmbH\NpCacheable\Controller\CacheableController` as your
base controller, your actions' output can be cached - _regardless of the TYPO3 cache settings_ -
and help you achieve faster load times by avoiding slow and redundant database and fluid operations.

np_cacheable uses the TYPO3 caching framework so you can expect a considerable speedup of slow
"uncached" extbase controller actions even if you only have MySQL available as CF backend.

It works by overriding `\TYPO3\CMS\Extbase\Mvc\Controller\ActionController::callActionMethod`
and check whether the action that is about to be called has the `@cacheable` annotation.

###Usage
To enable the caching you have to install the extension, then make `\NetworkPublishingGmbH\NpCacheable\Controller\CacheableController`
your controllers base class:

```
class MyController extends \NetworkPublishingGmbH\NpCacheable\Controller\CacheableController
```

Then you can add the annotiation `@cacheable` to your action docblocks:
```
class MyController extends \NetworkPublishingGmbH\NpCacheable\Controller\CacheableController{
	/**
	 * action list
	 *
	 * @cacheable
	 * 
	 * @return void
	 */
	public function listAction() {
		...
	}
}

``` 

For even more control you can specify the **time to live** (lifetime in TYPO3 CF terms)
and **cache tags** for each action by adding the respective annotations `@cacheTTL` and
`@cacheTags` to the action's docblock:

```
	/**
	 * action list
	 *
	 * @cacheable
	 * @cacheTTL 100
	 * @cacheTags space separated tags
	 * 
	 * @return void
	 */
	public function listAction() {
		...
	}
```
`@cacheTTL` takes an **int value of seconds** that the item should stay in cache. Expiration handling
and purge behaviour is up to the TYPO3 CF and its respective backend!  
`@cacheTags` takes a **list of tags separated by a single space**. Be sensible about the tags you use
and only use lowercase alphabetical chars and numbers.

###Prerequisites
The **Caching Framework** should be enabled and a recent extbase version available.
By default np_cacheable uses `TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend` as
its cache backend. This means that during installation two tables are created: `cf_npcacheable` and
`cf_npcacheable_tags` even though there is no `ext_tables.sql` present! If the tables are missing
after installing the plugin, check your install tool and compare the database!

If you are using another cache backend or plan on using another, you can either modify 
`ext_localconf.php` and swap the backend there or define another configuration for `$TYPO3_CONF_VARS['SYS']['caching']['cacheConfigurations']['npcacheable']` in some configuration that is loaded **before** np_cacheable's own `ext_localconf.php` is included.

If you are curious about what gets cached and for how long, `Typo3DatabaseBackend` is a good
start as it lets you easily confirm the caching behaviour in your (installations) local MySQL database.

###Problems
If your controller can't find the cache key and you keep getting error pages, try to call
```
$this->initializeCaching();
``` 
in your controller's `initializeAction` once to force an instance of the caching framework into
existence.  
If you are using `Typo3DatabaseBackend`: check database compare in the install tool to see if the 
tables were created properly.  
If you are using another backend make sure that your backend's server/service is running and
responding to requests.

