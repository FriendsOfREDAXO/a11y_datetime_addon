<?php

declare(strict_types=1);

class rex_yform_value_a11y_datetime_addon extends rex_yform_value_abstract
{
    /**
     * @var array<string, string>
     */
    private const PICKER_TYPES = [
        'date' => 'a11y_datetime_yform_type_date',
        'datetime' => 'a11y_datetime_yform_type_datetime',
        'time' => 'a11y_datetime_yform_type_time',
        'date_range' => 'a11y_datetime_yform_type_date_range',
    ];

    public function enterObject(): void
    {
        $this->setValue((string) $this->getValue());

        if ('' === $this->getValue() && !$this->params['send']) {
            $defaultValue = trim((string) $this->getElement('default'));
            if ('' !== $defaultValue) {
                $this->setValue($defaultValue);
            } elseif ('1' === (string) $this->getElement('current_value')) {
                $this->setValue($this->getCurrentDefaultValue());
            }
        }

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();

        if ($this->saveInDb()) {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }

        if (!$this->needsOutput() || !$this->isViewable()) {
            return;
        }

        if (!$this->isEditable()) {
            $this->params['form_output'][$this->getId()] = $this->parse(
                ['value.text-view.tpl.php', 'value.view.tpl.php'],
                ['type' => 'text', 'value' => $this->getValue()]
            );

            return;
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.a11y_datetime_addon.tpl.php', [
            'inputAttributes' => $this->buildInputAttributes(),
            'configSummary' => $this->buildConfigSummary(),
        ]);
    }

    public function getDescription(): string
    {
        return 'a11y_datetime_addon|name|label|type|default|[no_db]|[notice]';
    }

    public function getDefinitions(): array
    {
        return [
            'type' => 'value',
            'name' => 'a11y_datetime_addon',
            'values' => [
                'name' => ['type' => 'name', 'label' => rex_i18n::msg('yform_values_defaults_name')],
                'label' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_label')],
                'picker_type' => [
                    'type' => 'choice',
                    'label' => rex_i18n::msg('a11y_datetime_yform_picker_type'),
                    'choices' => array_map(static fn (string $key): string => rex_i18n::rawMsg($key), self::PICKER_TYPES),
                    'default' => 'date',
                    'notice' => rex_i18n::msg('a11y_datetime_yform_picker_type_notice') . ' ' . rex_i18n::msg('a11y_datetime_yform_db_type_notice'),
                ],
                'default' => ['type' => 'text', 'label' => rex_i18n::msg('a11y_datetime_yform_default')],
                'current_value' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_current_value')],
                'locale' => ['type' => 'text', 'label' => rex_i18n::msg('a11y_datetime_yform_locale'), 'default' => 'de'],
                'calendar_title' => ['type' => 'text', 'label' => rex_i18n::msg('a11y_datetime_yform_calendar_title'), 'notice' => rex_i18n::msg('a11y_datetime_yform_calendar_title_notice')],
                'alt_format' => ['type' => 'text', 'label' => rex_i18n::msg('a11y_datetime_yform_alt_format'), 'notice' => rex_i18n::msg('a11y_datetime_yform_alt_format_notice')],
                'minute_increment' => ['type' => 'text', 'label' => rex_i18n::msg('a11y_datetime_yform_minute_increment'), 'default' => '1'],
                'show_months' => [
                    'type' => 'text',
                    'label' => rex_i18n::msg('a11y_datetime_yform_show_months'),
                    'notice' => rex_i18n::msg('a11y_datetime_yform_show_months_notice'),
                ],
                'mobile_range_mode' => [
                    'type' => 'choice',
                    'label' => rex_i18n::msg('a11y_datetime_yform_mobile_range_mode'),
                    'choices' => [
                        'default' => rex_i18n::msg('a11y_datetime_yform_mobile_range_mode_default'),
                        'split' => rex_i18n::msg('a11y_datetime_yform_mobile_range_mode_split'),
                    ],
                    'default' => 'split',
                    'notice' => rex_i18n::msg('a11y_datetime_yform_mobile_range_mode_notice'),
                ],
                'year_range_past' => [
                    'type' => 'text',
                    'label' => rex_i18n::msg('a11y_datetime_yform_year_range_past'),
                    'notice' => rex_i18n::msg('a11y_datetime_yform_year_range_notice'),
                    'default' => '10',
                ],
                'year_range_future' => [
                    'type' => 'text',
                    'label' => rex_i18n::msg('a11y_datetime_yform_year_range_future'),
                    'default' => '10',
                ],
                'enable_seconds' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_enable_seconds')],
                'time_24hr' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_time_24hr'), 'default' => '1'],
                'allow_input' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_allow_input')],
                'focus_opens' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_focus_opens')],
                'inline' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_inline')],
                'month_year_wheel' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_month_year_wheel'), 'default' => '1'],
                'show_month_nav_arrows' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_show_month_nav_arrows')],
                'year_wheel_manual_input' => ['type' => 'boolean', 'label' => rex_i18n::msg('a11y_datetime_yform_year_wheel_manual_input'), 'default' => '1'],
                'disable_dates' => ['type' => 'textarea', 'label' => rex_i18n::msg('a11y_datetime_yform_disable_dates'), 'notice' => rex_i18n::msg('a11y_datetime_yform_disable_dates_notice')],
                'disable_callback' => ['type' => 'text', 'label' => rex_i18n::msg('a11y_datetime_yform_disable_callback'), 'notice' => rex_i18n::msg('a11y_datetime_yform_disable_callback_notice')],
                'expert_json' => ['type' => 'textarea', 'label' => rex_i18n::msg('a11y_datetime_yform_expert_json'), 'notice' => rex_i18n::msg('a11y_datetime_yform_expert_json_notice')],
                'attributes' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_attributes'), 'notice' => rex_i18n::msg('yform_values_defaults_attributes_notice')],
                'no_db' => ['type' => 'no_db', 'label' => rex_i18n::msg('yform_values_defaults_table'), 'default' => 0],
                'notice' => ['type' => 'text', 'label' => rex_i18n::msg('yform_values_defaults_notice')],
            ],
            'description' => rex_i18n::msg('a11y_datetime_yform_description'),
            'db_type' => [
                'varchar(191)',
                'text',
                'date',
                'datetime',
                'time',
            ],
            'famous' => false,
        ];
    }

    public static function getSearchField($params): void
    {
        self::getSearchHandlerClass($params)::getSearchField($params);
    }

    public static function getSearchFilter($params)
    {
        return self::getSearchHandlerClass($params)::getSearchFilter($params);
    }

    private static function getSearchHandlerClass(array $params): string
    {
        $pickerType = (string) ($params['field']->getElement('picker_type') ?? 'date');

        return match ($pickerType) {
            'datetime' => rex_yform_value_datetime::class,
            'time' => rex_yform_value_time::class,
            'date_range', 'date' => rex_yform_value_date::class,
            default => rex_yform_value_text::class,
        };
    }

    public static function getListValue($params): string
    {
        $value = trim((string) $params['subject']);
        if ('' === $value) {
            return '<span>-</span>';
        }

        $pickerType = (string) ($params['params']['field']['picker_type'] ?? 'date');
        $altFormat = trim((string) ($params['params']['field']['alt_format'] ?? ''));

        return '<span>' . rex_escape(self::formatListValue($value, $pickerType, $altFormat)) . '</span>';
    }

    private function getCurrentDefaultValue(): string
    {
        return match ((string) $this->getElement('picker_type')) {
            'datetime' => date('Y-m-d H:i'),
            'time' => date('H:i'),
            'date' => date('Y-m-d'),
            default => '',
        };
    }

    /**
     * @return array<string, string>
     */
    private function buildInputAttributes(): array
    {
        $pickerType = (string) $this->getElement('picker_type');
        $locale = trim((string) $this->getElement('locale'));
        $calendarTitle = trim((string) $this->getElement('calendar_title'));
        $dateFormat = $this->resolveStorageDateFormat($pickerType);
        $altFormat = $this->resolveAltFormat($pickerType);
        $disableDates = trim((string) $this->getElement('disable_dates'));
        $disableCallback = trim((string) $this->getElement('disable_callback'));
        $expertJson = trim((string) $this->getElement('expert_json'));

        $attributes = $this->decodeAttributes((string) $this->getElement('attributes'));
        $attributes['type'] = 'text';
        $attributes['name'] = $this->getFieldName();
        $attributes['id'] = $this->getFieldId();
        $attributes['value'] = $this->getValue();
        $attributes['class'] = trim(((string) ($attributes['class'] ?? '')) . ' form-control a11y_datetime a11y_datetime-yform-input');
        $attributes['data-locale'] = '' !== $locale ? $locale : 'de';
        $attributes['data-time_24hr'] = $this->boolAttribute('time_24hr', true);
        $attributes['data-allowInput'] = $this->boolAttribute('allow_input');
        $attributes['data-focusOpens'] = $this->boolAttribute('focus_opens');
        $attributes['data-inline'] = $this->boolAttribute('inline');
        $attributes['data-monthYearWheel'] = $this->boolAttribute('month_year_wheel', true);
        $attributes['data-showMonthNavArrows'] = $this->boolAttribute('show_month_nav_arrows');
        $attributes['data-yearWheelManualInput'] = $this->boolAttribute('year_wheel_manual_input', true);
        $attributes['data-enableSeconds'] = $this->boolAttribute('enable_seconds');

        if ('' !== $calendarTitle) {
            $attributes['data-calendarTitle'] = $calendarTitle;
        }

        $minuteIncrement = max(1, (int) $this->getElement('minute_increment'));
        $showMonths = max(1, (int) $this->getElement('show_months'));
        $attributes['data-minuteIncrement'] = (string) $minuteIncrement;
        $attributes['data-showMonths'] = (string) $showMonths;

        $attributes['data-yearRangePast'] = (string) max(0, (int) $this->getElement('year_range_past'));
        $attributes['data-yearRangeFuture'] = (string) max(0, (int) $this->getElement('year_range_future'));

        if ('datetime' === $pickerType) {
            $attributes['data-enableTime'] = 'true';
        } elseif ('time' === $pickerType) {
            $attributes['data-enableTime'] = 'true';
            $attributes['data-noCalendar'] = 'true';
        } elseif ('date_range' === $pickerType) {
            $attributes['data-mode'] = 'range';
            $mobileRangeMode = trim((string) $this->getElement('mobile_range_mode'));
            $attributes['data-mobileRangeMode'] = '' !== $mobileRangeMode ? $mobileRangeMode : 'default';
            $attributes['data-mobileRangeStartLabel'] = rex_i18n::msg('a11y_datetime_yform_mobile_range_start');
            $attributes['data-mobileRangeEndLabel'] = rex_i18n::msg('a11y_datetime_yform_mobile_range_end');
        }

        $attributes['data-dateFormat'] = $dateFormat;
        $attributes['data-altFormat'] = $altFormat;

        if ('' !== $disableDates) {
            $attributes['data-disabled'] = preg_replace('/\s+/', '', $disableDates) ?? $disableDates;
        }

        if ('' !== $disableCallback) {
            $attributes['data-a11y-datetime-disable-callback'] = $disableCallback;
        }

        if ('' !== $expertJson) {
            $attributes['data-a11y-datetime-expert-json'] = $expertJson;
        }

        if (!isset($attributes['placeholder']) || '' === trim((string) $attributes['placeholder'])) {
            $attributes['placeholder'] = $this->getDefaultPlaceholder($pickerType);
        }

        return array_map(static fn ($value): string => (string) $value, $attributes);
    }

    /**
     * @return array<string, string>
     */
    private function buildConfigSummary(): array
    {
        $pickerType = (string) $this->getElement('picker_type');

        return [
            'type' => rex_i18n::msg(self::PICKER_TYPES[$pickerType] ?? self::PICKER_TYPES['date']),
            'minuteIncrement' => (string) max(1, (int) $this->getElement('minute_increment')),
            'locale' => trim((string) $this->getElement('locale')) ?: 'de',
            'calendarTitle' => trim((string) $this->getElement('calendar_title')),
            'disableCallback' => trim((string) $this->getElement('disable_callback')),
            'expertJson' => trim((string) $this->getElement('expert_json')),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeAttributes(string $attributes): array
    {
        if ('' === trim($attributes)) {
            return [];
        }

        $decoded = json_decode($attributes, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function boolAttribute(string $elementName, bool $default = false): string
    {
        $value = (string) $this->getElement($elementName);
        if ('' === $value) {
            return $default ? 'true' : 'false';
        }

        return in_array($value, ['1', 'true'], true) ? 'true' : 'false';
    }

    private function resolveStorageDateFormat(string $pickerType): string
    {
        return match ($pickerType) {
            'datetime' => 'Y-m-d H:i',
            'time' => 'H:i',
            'date_range' => 'Y-m-d',
            default => 'Y-m-d',
        };
    }

    private function resolveAltFormat(string $pickerType): string
    {
        $altFormat = trim((string) $this->getElement('alt_format'));
        if ('' !== $altFormat) {
            return $altFormat;
        }

        return match ($pickerType) {
            'datetime' => 'd.m.Y H:i',
            'time' => 'H:i',
            'date_range' => 'd.m.Y',
            default => 'd.m.Y',
        };
    }

    private function getDefaultPlaceholder(string $pickerType): string
    {
        return match ($pickerType) {
            'datetime' => 'YYYY-MM-DD HH:mm',
            'time' => 'HH:mm',
            'date_range' => 'YYYY-MM-DD to YYYY-MM-DD',
            default => 'YYYY-MM-DD',
        };
    }

    private static function formatListValue(string $value, string $pickerType, string $altFormat): string
    {
        $displayFormat = '' !== $altFormat ? $altFormat : self::getDefaultListFormat($pickerType);

        if ('date_range' === $pickerType) {
            $parts = preg_split('/\s+to\s+/i', $value);
            if (is_array($parts) && 2 === count($parts)) {
                $formattedParts = array_map(
                    static fn (string $part): string => self::formatDateValue(trim($part), $displayFormat),
                    $parts
                );

                if ('' !== $formattedParts[0] && '' !== $formattedParts[1]) {
                    return implode(' to ', $formattedParts);
                }
            }

            return $value;
        }

        return self::formatDateValue($value, $displayFormat);
    }

    private static function getDefaultListFormat(string $pickerType): string
    {
        return match ($pickerType) {
            'datetime' => 'Y-m-d H:i',
            'time' => 'H:i',
            'date_range', 'date' => 'Y-m-d',
            default => 'Y-m-d',
        };
    }

    private static function formatDateValue(string $value, string $displayFormat): string
    {
        $candidateFormats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d',
            'H:i:s',
            'H:i',
        ];

        foreach ($candidateFormats as $candidateFormat) {
            $date = DateTime::createFromFormat($candidateFormat, $value);
            if ($date instanceof DateTime) {
                return $date->format($displayFormat);
            }
        }

        return $value;
    }
}
