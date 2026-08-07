<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\Flatpickr;

use rex_addon;
use rex_path;

final class FrontendHelper
{
    private static bool $assetsIncluded = false;

    public static function getAssetsHtml(string $locale = 'de', bool $includeDarkTheme = true, bool $includeRangePlugin = true, bool $includeInitScript = true): string
    {
        $addon = rex_addon::get('flatpickr');

        $tags = [];
        $tags[] = self::cssTag(self::assetUrl($addon, 'vendor/a11y_datetime/dist/a11y_datetime.min.css'));

        if ($includeDarkTheme) {
            $tags[] = self::cssTag(self::assetUrl($addon, 'vendor/a11y_datetime/dist/themes/dark.css'));
        }

        $tags[] = self::jsTag(self::assetUrl($addon, 'vendor/a11y_datetime/dist/a11y_datetime.min.js'));

        if ('' !== $locale) {
            $tags[] = self::jsTag(self::assetUrl($addon, 'vendor/a11y_datetime/dist/l10n/' . $locale . '.js'));
        }

        if ($includeRangePlugin) {
            $tags[] = self::jsTag(self::assetUrl($addon, 'vendor/a11y_datetime/dist/plugins/rangePlugin.js'));
        }

        if ($includeInitScript) {
            $tags[] = self::jsTag(self::assetUrl($addon, 'flatpickr_init.js'));
        }

        return implode(PHP_EOL, $tags) . PHP_EOL;
    }

    public static function includeAssets(string $locale = 'de', bool $includeDarkTheme = true, bool $includeRangePlugin = true, bool $includeInitScript = true): void
    {
        if (self::$assetsIncluded) {
            return;
        }

        echo self::getAssetsHtml($locale, $includeDarkTheme, $includeRangePlugin, $includeInitScript);
        self::$assetsIncluded = true;
    }

    private static function assetUrl(rex_addon $addon, string $relativeAssetPath): string
    {
        $url = $addon->getAssetsUrl($relativeAssetPath);
        $path = rex_path::addonAssets('flatpickr', $relativeAssetPath);
        $version = is_file($path) ? (string) filemtime($path) : (string) $addon->getVersion();

        return $url . '?v=' . rawurlencode($version);
    }

    private static function cssTag(string $url): string
    {
        return '<link rel="stylesheet" href="' . self::escapeHtml($url) . '">';
    }

    private static function jsTag(string $url): string
    {
        return '<script src="' . self::escapeHtml($url) . '"></script>';
    }

    private static function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
