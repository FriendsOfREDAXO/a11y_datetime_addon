# a11y_datetime for REDAXO

a11y_datetime is an accessibility-focused fork of flatpickr.

> **Breaking change in 3.0.0**: this addon was renamed from `flatpickr` to `a11y_datetime_addon` (package name, PHP namespace, YForm field type, asset file names). The CSS marker class is now `a11y_datetime` / `a11y_datetime_range`. See CHANGELOG.md for details and the automatic migration for existing YForm fields.

## Differences from original flatpickr

This addon uses the `a11y_datetime` fork and is no longer a strict visual/behavioral 1:1 clone of original flatpickr in every detail.

Key differences:

- Accessibility-first behavior (ARIA, keyboard flow, live announcements).
- Additional fork options are passed through via `data-*` attributes.
- Some defaults intentionally differ from original flatpickr.

Examples of changed/new defaults:

- `focusOpens`: default `false`
- `announceChanges`: default `true`
- `monthYearWheel`: default `true`
- `showMonthNavArrows`: default `false` (arrows are optional)

## a11y_datetime Links

Website: https://friendsofredaxo.github.io/a11y_datetime/

GitHub: https://github.com/FriendsOfREDAXO/a11y_datetime

## Form addon integration

| Addon | Support |
|---|---|
| YForm | Dedicated Manager field `a11y_datetime_addon` with a full options form, see [Howto use in YForm](#howto-use-in-yform) |
| MForm | Dedicated field type `a11y_datetime` from MForm 10.0 (`addCustomField()`), see [Howto use in MForm](#howto-use-in-mform-mform-100) |
| Direct HTML/Modules | `class="a11y_datetime"` + `data-*` attributes, see [Howto use in Modules](#howto-use-in-modules) |

## Howto install

Just install it from the REDAXO installer

## Frontend usage

The addon auto-loads its assets only in the REDAXO backend. For frontend pages, use one of these approaches:

1. Include assets directly in your template.
2. Use the helper method from this addon.

### Recommended helper method

```php
<?php
use FriendsOfREDAXO\A11yDatetimeAddon\FrontendHelper;

// Default: de locale, dark theme enabled, range plugin enabled, init script enabled
FrontendHelper::includeAssets();

// Optional: locale, dark theme, range plugin, init script
// FrontendHelper::includeAssets('de', true, true, true);
```

Signature:

```php
FrontendHelper::includeAssets(string $locale = 'de', bool $includeDarkTheme = true, bool $includeRangePlugin = true, bool $includeInitScript = true);
```

Notes:
- The init script uses `DOMContentLoaded` in frontend contexts and `rex:ready` in REDAXO backend contexts.
- Helper output includes cache-busting (`?v=...`) for each asset.

### Manual inclusion without helper

```php
<?php
$addon = rex_addon::get('a11y_datetime_addon');
$v = static function (string $asset) use ($addon): string {
	$path = rex_path::addonAssets('a11y_datetime_addon', $asset);
	$version = is_file($path) ? (string) filemtime($path) : (string) $addon->getVersion();

	return $addon->getAssetsUrl($asset) . '?v=' . rawurlencode($version);
};
?>
<link rel="stylesheet" href="<?= rex_escape($v('vendor/a11y_datetime/dist/a11y_datetime.min.css')) ?>">
<link rel="stylesheet" href="<?= rex_escape($v('vendor/a11y_datetime/dist/themes/dark.css')) ?>">

<script src="<?= rex_escape($v('vendor/a11y_datetime/dist/a11y_datetime.min.js')) ?>"></script>
<script src="<?= rex_escape($v('vendor/a11y_datetime/dist/l10n/de.js')) ?>"></script>
<script src="<?= rex_escape($v('vendor/a11y_datetime/dist/plugins/rangePlugin.js')) ?>"></script>
<script src="<?= rex_escape($v('a11y_datetime_init.js')) ?>"></script>
```

Manual inclusion without range plugin:

```php
<script src="<?= rex_escape($v('vendor/a11y_datetime/dist/a11y_datetime.min.js')) ?>"></script>
<script src="<?= rex_escape($v('vendor/a11y_datetime/dist/l10n/de.js')) ?>"></script>
<script src="<?= rex_escape($v('a11y_datetime_init.js')) ?>"></script>
```

## Howto use in YForm

```json
{"class": "a11y_datetime","data-locale":"de","data-enableTime":"true"}
```

### Dedicated YForm field `a11y_datetime_addon`

The addon ships with its own YForm value field `a11y_datetime_addon` for common picker setups directly in YForm Manager.

Covered standard modes:

- Date
- Date & time
- Time
- Date range

Covered standard settings:

- Locale
- `dateFormat` / `altFormat`
- `minuteIncrement`
- `enableSeconds`
- `time_24hr`
- `allowInput`
- `focusOpens`
- `inline`
- `monthYearWheel`
- `showMonthNavArrows`
- `showMonths` - maximum number of calendar panels shown at the same time; on narrow widths it is automatically reduced to 1 or 2
- `yearWheelManualInput`
- Year range relative to today, set via two plain number fields ("Years back" / "Years ahead") — no JSON typing required in the Manager form
- fixed disabled dates

For special cases, the field also provides an expert JSON textarea. That JSON is merged after the common settings.

`dateFormat` can often stay empty. In that case, the storage format is chosen automatically based on the selected field type.
`altFormat` controls the visible display, including the list view. If `altFormat` is filled, that format string is used for list output.

External disabled-date logic can be attached via a global JavaScript callback path such as `window.MyApp.a11yDatetimeDisabledDates`. The callback may return:

- an array of disable values
- a picker disable callback function

Notes:

- For date ranges, the database column should be `varchar` or `text`.
- Set the database field type manually to match the selected field type: Date = `date`, Date & Time = `datetime`, Time = `time`, Date range = `varchar` or `text`.
- Inline editing in YForm list view is intentionally not part of this first version; the list view currently provides a compact preview instead of fragile direct editing.

## Howto use in MForm (MForm 10.0+)

If MForm is installed, the addon automatically registers its own field type `a11y_datetime` via `MForm::registerFieldType()`. Options are passed directly as `data-*` attributes (no dedicated Manager form like the YForm field):

```php
<?php
use FriendsOfRedaxo\MForm;

$form = MForm::factory();
$form->addCustomField('a11y_datetime', 1, [
    'label' => 'Date',
    'data-enableTime' => 'true',
    'data-locale' => 'de',
]);
echo $form->show();
```

Also works inside the MForm Flex Repeater (`addRepeaterElement()`/`addFlexRepeaterElement()`) — cloned repeater items initialize the picker automatically via the `rex:ready` event the Flex Repeater triggers after cloning.

Note: the visual MForm form builder (drag & drop) currently has no registration API for custom field types from third-party addons, so `a11y_datetime` does not show up in its palette — only programmatic use via `addCustomField()` in module code is supported.

## Howto use in Modules

```html
<input type="date" class="form-control a11y_datetime" data-locale="de" data-enableTime="true" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]">
```

Optional: enable opening on focus for a field:

```html
<input type="text" class="form-control a11y_datetime" data-focusOpens="true" name="event_start">
```

## Howto RangeField over 2 input fields

```json
{"class": "a11y_datetime_range","data-locale":"de","data-enableTime":"true", "data-rangefield":"#id"}
```

## Set the view just for date fields. 

If you don't want to see the time in a date-field, don't use the timepicker and set an alternative View.

Just set the data-altFormat. 😀

```json 
{"class":"a11y_datetime","data-altFormat":"j. F, Y"}
```

## Only time picker (no calendar)

If you only want a time picker without a calendar, set `data-enableTime="true"` and `data-noCalendar="true"`.
When both are set, the default `dateFormat` and `altFormat` fall back to `H:i`.

```json
{"class":"a11y_datetime","data-locale":"de","data-enableTime":"true","data-noCalendar":"true"}
```

## Open on focus (optional)

By default, the picker does not open automatically when the input receives focus via Tab.
If you want the legacy behavior for a specific field, enable it explicitly:

```json
{"class":"a11y_datetime","data-focusOpens":"true"}
```

## Weekday time windows (data-timeRules)

You can pass the new `timeRules` option as JSON via `data-timeRules`.

Example: Monday-Friday 08:00-17:00, Saturday 10:00-14:00.

```json
{"class":"a11y_datetime","data-enableTime":"true","data-timeRules":"[{\"days\":[1,2,3,4,5],\"from\":\"08:00\",\"to\":\"17:00\"},{\"days\":[6],\"from\":\"10:00\",\"to\":\"14:00\"}]"}
```

Note: weekday indices follow JavaScript (`0`=Sunday, `1`=Monday, ..., `6`=Saturday).

Important: If `data-timeRules` is set and a weekday has no matching rule, that weekday becomes not selectable.
In the example above, Sunday (`0`) is disabled.

## Month/year wheel (data-monthYearWheel)

You can enable the header month/year wheel per field.

```json
{"class":"a11y_datetime","data-monthYearWheel":"true","data-yearRangePast":"10","data-yearRangeFuture":"10","data-yearWheelManualInput":"true"}
```

`data-yearRangePast` and `data-yearRangeFuture` are plain number attributes (no JSON needed). A `data-yearRange` JSON object (`{"past":N,"future":N}`) is still supported as a fallback, e.g. for generated code that supplies the whole range as a single attribute.

You can re-enable header arrows explicitly:

```json
{"class":"a11y_datetime","data-monthYearWheel":"true","data-showMonthNavArrows":"true"}
```

Show multiple calendar pages side-by-side (useful for ranges):

```json
{"class":"a11y_datetime_range","data-showMonths":"3","data-rangefield":"#id"}
```

## Supported `data-*` attributes

All relevant options of the vendor (a11y_datetime / flatpickr) can be set per field via `data-*` attributes.
The attribute name mirrors the option name (e.g. `data-enableTime`, `data-time_24hr`).

### Addon defaults (differ from vendor defaults)

These options are always applied by the addon and can be overridden by the corresponding `data-*` attribute:

| Attribute | Option | Type | Default |
|---|---|---|---|
| `data-locale` | `locale` | Locale key (e.g. `de`, `en`) | `de` |
| `data-altInput` | `altInput` | `true`/`false` | `true` |
| `data-altFormat` | `altFormat` | Format string | `j. F, Y H:i` (or `H:i` for time-only) |
| `data-time_24hr` | `time_24hr` | `true`/`false` | `true` |
| `data-focusOpens` | `focusOpens` | `true`/`false` | `false` |
| `data-monthYearWheel` | `monthYearWheel` | `true`/`false` | `true` |
| `data-showMonthNavArrows` | `showMonthNavArrows` | `true`/`false` | `false` |
| `data-showMonths` | `showMonths` | Integer (`1`, `2`, `3`, ...) | `1` |
| `data-yearWheelManualInput` | `yearWheelManualInput` | `true`/`false` | `true` |
| `data-yearRangePast` | `yearRange.past` | Integer | `10` |
| `data-yearRangeFuture` | `yearRange.future` | Integer | `10` |
| `data-yearRange` (fallback) | `yearRange` | JSON object `{"past":N,"future":N}` | – |
| `data-enableTime` | `enableTime` | `true`/`false` | `false` |
| `data-noCalendar` | `noCalendar` | `true`/`false` | `false` |
| `data-timeRules` | `timeRules` | JSON array | `[]` |
| `data-disabled` | `disable` | Comma-separated list of dates | `[]` |

### Pass-through options (only applied when the attribute is present)

For every other supported vendor option, add a `data-<option>` attribute and the value is forwarded to the picker. If the attribute is omitted, the vendor default applies.

**Boolean options** (`true`/`false`):

`data-allowInput`, `data-allowInvalidPreload`, `data-animate`, `data-announceChanges`, `data-autoFillDefaultTime`, `data-clickOpens`, `data-closeOnSelect`, `data-disableMobile`, `data-enableSeconds`, `data-inline`, `data-shorthandCurrentMonth`, `data-showCloseButton`, `data-showTitleBar`, `data-static`, `data-weekNumbers`, `data-wrap`

**String options**:

| Attribute | Option | Allowed values / format |
|---|---|---|
| `data-altInputClass` | `altInputClass` | CSS class name |
| `data-ariaDateFormat` | `ariaDateFormat` | Format string |
| `data-calendarTitle` | `calendarTitle` | Text |
| `data-conjunction` | `conjunction` | Text between multiple dates |
| `data-dateFormat` | `dateFormat` | Format string |
| `data-defaultDate` | `defaultDate` | Date string |
| `data-initialDayFocus` | `initialDayFocus` | `today` / `selected` / `firstAvailable` |
| `data-maxDate` | `maxDate` | Date string |
| `data-maxTime` | `maxTime` | Time string (`HH:MM`) |
| `data-minDate` | `minDate` | Date string |
| `data-minTime` | `minTime` | Time string (`HH:MM`) |
| `data-mode` | `mode` | `single` / `multiple` / `range` / `time` |
| `data-monthSelectorType` | `monthSelectorType` | `dropdown` / `static` |
| `data-nextArrow` | `nextArrow` | HTML/text for next arrow |
| `data-now` | `now` | Date string |
| `data-position` | `position` | `auto`, `above`, `below`, `auto left`, ..., `below right` |
| `data-prevArrow` | `prevArrow` | HTML/text for previous arrow |

**Integer/number options**:

`data-defaultHour`, `data-defaultMinute`, `data-defaultSeconds`, `data-hourIncrement`, `data-minuteIncrement`

**List options** (comma-separated):

| Attribute | Option |
|---|---|
| `data-disabled` | `disable` |
| `data-enable` | `enable` |

Range pickers (`.a11y_datetime_range`) accept the same attributes plus `data-rangefield` (CSS selector of the second input).

Hooks (`onChange`, `onOpen`, ...) cannot be configured via `data-*` attributes.

## Disable dates

You can disable dates via a comma seprated list. 
The attribute ist data-disabled. 

Example: 

```json
{"class": "a11y_datetime","data-locale":"de","data-enableTime":"true", "data-disabled":"2022-12-11,2022-12-24,2022-12-25"}
```


## Author

**Friends Of REDAXO**

* http://www.redaxo.org
* https://github.com/FriendsOfREDAXO

**Project lead**
[Thomas Skerbis](https://github.com/skerbis)

**Vendor**
https://github.com/FriendsOfREDAXO/a11y_datetime
