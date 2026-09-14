<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\A11yDatetimeAddon;

use rex_addon;
use rex_path;

final class FrontendHelper
{
    private static bool $assetsIncluded = false;

    /**
     * Reine Pfad-/Typ-Liste der benötigten Vendor-Assets, ohne HTML-Erzeugung.
     * Wird sowohl von getAssetsHtml() (Frontend) als auch von boot.php (Backend)
     * konsumiert, damit die Asset-Liste nur an einer Stelle gepflegt werden muss.
     *
     * @return list<array{type: 'css'|'js', path: string}>
     */
    public static function assetList(string $locale = 'de', bool $includeDarkTheme = true, bool $includeRangePlugin = true, bool $includeInitScript = true): array
    {
        $assets = [];
        $assets[] = ['type' => 'css', 'path' => 'vendor/a11y_datetime/dist/a11y_datetime.min.css'];

        if ($includeDarkTheme) {
            $assets[] = ['type' => 'css', 'path' => 'vendor/a11y_datetime/dist/themes/dark.css'];
        }

        $assets[] = ['type' => 'js', 'path' => 'vendor/a11y_datetime/dist/a11y_datetime.min.js'];

        if ('' !== $locale) {
            $assets[] = ['type' => 'js', 'path' => 'vendor/a11y_datetime/dist/l10n/' . $locale . '.js'];
        }

        if ($includeRangePlugin) {
            $assets[] = ['type' => 'js', 'path' => 'vendor/a11y_datetime/dist/plugins/rangePlugin.js'];
        }

        if ($includeInitScript) {
            $assets[] = ['type' => 'js', 'path' => 'a11y_datetime_init.js'];
        }

        return $assets;
    }

    public static function getAssetsHtml(string $locale = 'de', bool $includeDarkTheme = true, bool $includeRangePlugin = true, bool $includeInitScript = true): string
    {
        $addon = rex_addon::get('a11y_datetime_addon');

        $tags = [];
        foreach (self::assetList($locale, $includeDarkTheme, $includeRangePlugin, $includeInitScript) as $asset) {
            $tags[] = 'css' === $asset['type']
                ? self::cssTag(self::assetUrl($addon, $asset['path']))
                : self::jsTag(self::assetUrl($addon, $asset['path']));
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
        $path = rex_path::addonAssets('a11y_datetime_addon', $relativeAssetPath);
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
