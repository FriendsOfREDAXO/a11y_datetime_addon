<?php

use FriendsOfREDAXO\A11yDatetimeAddon\FrontendHelper;

$addon = rex_addon::get('a11y_datetime_addon');

if (rex_addon::get('yform')->isAvailable()) {
    rex_yform::addTemplatePath($addon->getPath('ytemplates'));
}

if (rex::isBackend() && is_object(rex::getUser())) {
    // Backend fährt bewusst kein Dark-Theme-CSS (folgt dem Backend-Theme selbst).
    foreach (FrontendHelper::assetList('de', false, true, true) as $asset) {
        if ('css' === $asset['type']) {
            rex_view::addCssFile($addon->getAssetsUrl($asset['path']));
        } else {
            rex_view::addJsFile($addon->getAssetsUrl($asset['path']));
        }
    }
}
