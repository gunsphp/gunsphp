<?php

Configure::set('debug.enabled', true);

/**
 * Can be set to either 'development' or 'production'
 */
Configure::set('debug.level', 'development');

Configure::set('log.enabled', true);

Configure::set('log.threshold', 1);

Configure::set('view.extention', '.html.twig');

Configure::set('view.defaultLayout', 'base');

Configure::set('view.defaultAjaxLayout', 'ajax');

Configure::set('view.layoutDir', COMMON_DIR . DS . 'layouts');

Configure::set('view.cacheDir', 'twig');

// Storage Engine for Cache. Valid are auto, files, sqlite, xcache, memcache, apc, memcached, wincache
Configure::set('cache.engine', 'files');

// Directory to write Cache. Cache will be put in the 'cache' directory of the chosen directory
Configure::set('cache.dir', 'tmp');

// A security key used to encrypt Cache : usually used
Configure::set('security.applicationKey', '318847771476764934967097896782');

// A security salt used to encrypt sessions
Configure::set('security.salt', '5pnL5c8Pq44ZZDFYgBe4cv0wEtn6BrwJKnRGKAsxPoVL4p48ImmnzI3nACxcCw');

// Cache Application fallback driver
// Example, in your code, you use memcached, apc..etc, but when you moved your web hosting
// The new hosting don't have memcached, or apc. What you do? Set fallback that driver to other driver.
Configure::set('cache.fallbackDriver', 'files');

// Default Memcache Server for all Cache
Configure::set('cache.server', array(
    "127.0.0.1",
    11211,
    1));

// Default Cache refresh time in seconds
Configure::set('cache.refreshTime', 3600);

Configure::set('database.appUsesDatabase', false);

Configure::set('router.enabled', true);

Configure::set('url.allowedExtensions', array('.jpg', '.png', '.pdf'));

Configure::set('security.encryption.passwords', 'MD5');

Configure::set('auth.user.model', 'User');

App::import('JQuery', 'config');
App::import('ValidationErrors', 'config');
