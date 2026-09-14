<?php

use FriendsOfREDAXO\A11yDatetimeAddon\FrontendHelper;
use FriendsOfREDAXO\A11yDatetimeAddon\MFormFieldType;
use FriendsOfRedaxo\MForm;

$addon = rex_addon::get('a11y_datetime_addon');

if (rex_addon::get('yform')->isAvailable()) {
    rex_yform::addTemplatePath($addon->getPath('ytemplates'));
}

if (rex_addon::get('mform')->isAvailable() && method_exists(MForm::class, 'registerFieldType')) {
    MForm::registerFieldType('a11y_datetime', MFormFieldType::class);
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
