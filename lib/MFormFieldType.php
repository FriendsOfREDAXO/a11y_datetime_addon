<?php

declare(strict_types=1);

namespace FriendsOfREDAXO\A11yDatetimeAddon;

use FriendsOfRedaxo\MForm\DTO\MFormItem;
use FriendsOfRedaxo\MForm\FieldType\FieldRenderContext;
use FriendsOfRedaxo\MForm\FieldType\FieldTypeInterface;

/**
 * Registriert den a11y_datetime-Picker als MForm-Feldtyp ('a11y_datetime'),
 * siehe MForm::registerFieldType(). Anders als das YForm-Value-Feld baut
 * dieser Renderer kein eigenes Manager-Formular für die Picker-Optionen --
 * Modul-Autoren übergeben sie direkt als data-* Attribute an addCustomField(),
 * genau wie bei der manuellen HTML-Einbindung (siehe README).
 *
 * Funktioniert im klassischen Formular und im Flex-Repeater: im Repeater
 * liefert FieldRenderContext::controlAttributes() data-mfr-field statt
 * name/id, das Init-Script (a11y_datetime_init.js) erkennt geklonte Felder
 * über rex:ready, das der Flex-Repeater nach jedem Klonen triggert.
 */
final class MFormFieldType implements FieldTypeInterface
{
    public function render(MFormItem $item, FieldRenderContext $context): string
    {
        $class = trim('form-control a11y_datetime a11y_datetime-yform-input ' . $context->class);

        $html = '<input type="text"';
        $html .= $context->controlAttributes();
        $html .= ' class="' . htmlspecialchars($class, ENT_QUOTES) . '"';

        if (!$context->isRepeater()) {
            $html .= ' value="' . htmlspecialchars($context->value, ENT_QUOTES) . '"';
        }

        $html .= $context->attributes;
        $html .= ' />';

        return $html;
    }
}
