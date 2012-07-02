<?php

define("CACHE_TYPE_SUBSCRIBER_IDENTITY", "subscriber_identity");

function clearCache($type, $id) {
    $cacheName = $type . "_" . $id;
    _d("CLEARCACHE: $cacheName");
    Doctrine_Manager::getInstance()->getCurrentConnection()->getResultCacheDriver()->delete($cacheName);
}

function cacheName($type, $id) {
    $cacheName = $type . "_" . $id;
    _d("CACHENAME: $cacheName");
    return $cacheName;
}

function cachePut($type, $id, $data) {
    Doctrine_Manager::getInstance()->getCurrentConnection()->getResultCacheDriver()->save(cacheName($type, $id), $data, 3600);
}

function cacheGet($type, $id) {
    return Doctrine_Manager::getInstance()->getCurrentConnection()->getResultCacheDriver()->fetch(cacheName($type, $id));
}