<?php

/**
 * @var rex_yform_value_a11y_datetime_addon $this
 * @psalm-scope-this rex_yform_value_a11y_datetime_addon
 */

$inputAttributes ??= [];
$configSummary ??= [];

$notice = [];
$noticeElement = (string) $this->getElement('notice');
if ('' !== $noticeElement) {
    $notice[] = rex_i18n::translate($noticeElement, false);
}
if (isset($this->params['warning_messages'][$this->getId()]) && !$this->params['hide_field_warning_messages']) {
    $notice[] = '<span class="text-warning">' . rex_i18n::translate($this->params['warning_messages'][$this->getId()]) . '</span>';
}

// Die Konfigurationszusammenfassung (Typ, Minuten-Schritt, Locale …) ist eine Hilfe beim Einrichten
// des Feldes – nur für Admins im Backend; Redakteure sehen nur den Hinweistext des Feldes.
if (rex::isBackend() && rex::getUser()?->isAdmin()) {
    $summaryBits = [];
    if ('' !== (string) ($configSummary['type'] ?? '')) {
        $summaryBits[] = rex_i18n::msg('a11y_datetime_yform_summary_type', rex_escape((string) $configSummary['type']));
    }
    if ('' !== (string) ($configSummary['minuteIncrement'] ?? '')) {
        $summaryBits[] = rex_i18n::msg('a11y_datetime_yform_summary_minute_increment', rex_escape((string) $configSummary['minuteIncrement']));
    }
    if ('' !== (string) ($configSummary['locale'] ?? '')) {
        $summaryBits[] = rex_i18n::msg('a11y_datetime_yform_summary_locale', rex_escape((string) $configSummary['locale']));
    }
    if ('' !== (string) ($configSummary['calendarTitle'] ?? '')) {
        $summaryBits[] = rex_i18n::msg('a11y_datetime_yform_summary_calendar_title', rex_escape((string) $configSummary['calendarTitle']));
    }
    if ('' !== (string) ($configSummary['disableCallback'] ?? '')) {
        $summaryBits[] = rex_i18n::msg('a11y_datetime_yform_summary_disable_callback', rex_escape((string) $configSummary['disableCallback']));
    }
    if ('' !== (string) ($configSummary['expertJson'] ?? '')) {
        $summaryBits[] = rex_i18n::msg('a11y_datetime_yform_summary_expert_json');
    }

    if ([] !== $summaryBits) {
        $notice[] = implode(' | ', $summaryBits);
    }
}

// Hinweis mit dem Feld verknüpfen (aria-describedby): der Picker übernimmt das für das sichtbare
// Eingabefeld (altInput), Screenreader lesen den Hinweis dann mit vor.
$noticeHtml = '';
if ([] !== $notice) {
    $noticeId = ((string) ($inputAttributes['id'] ?? $this->getFieldId())) . '-notice';
    $describedBy = trim(((string) ($inputAttributes['aria-describedby'] ?? '')) . ' ' . $noticeId);
    $inputAttributes['aria-describedby'] = $describedBy;
    $noticeHtml = '<p class="help-block small" id="' . rex_escape($noticeId) . '">' . implode('<br />', $notice) . '</p>';
}
$classGroup = trim('form-group ' . $this->getHTMLClass() . ' ' . $this->getWarningClass());
?>
<div class="<?= $classGroup ?>" id="<?= $this->getHTMLId() ?>">
    <label class="control-label" for="<?= rex_escape((string) ($inputAttributes['id'] ?? '')) ?>"><?= $this->getLabel() ?></label>
    <input<?= rex_string::buildAttributes($inputAttributes) ?> />
    <?= $noticeHtml ?>
</div>