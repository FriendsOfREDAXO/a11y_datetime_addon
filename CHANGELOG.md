# Changelog

Alle wichtigen Änderungen an diesem Addon werden in dieser Datei dokumentiert.

## [2.2.8] - 2026-08-13

### Fixed
- Das YForm-Value `flatpickr` verhindert jetzt fehlerhafte manuelle Konfigurationen des internen Speicherformats. `date_format` ist nicht mehr konfigurierbar; das Speicherformat wird automatisch aus dem gewählten Feldtyp abgeleitet.
- Nur das sichtbare `alt_format` bleibt zur Anzeigeanpassung frei konfigurierbar.
- Die Backend-Initialisierung wurde auf den stabilen REDAXO-Pfad `rex:ready` festgelegt; Frontend bleibt auf `DOMContentLoaded`.
- Mehrfach-Initialisierung des Pickers in derselben Seite wird durch einen Guard unterbunden, um doppelte Starts zu vermeiden.

### Changed
- Addon-Version auf `2.2.8` angehoben.

## [2.2.7] - 2026-08-13

### Fixed
- Reproduzierter Regression im Initializer behoben: kombinierte DOMContentLoaded- und rex:ready-Starts wurden dedupliziert, damit das YForm-Widget nicht mehrfach initialisiert wird.
- Datetime-/Date-Value-Konfiguration im YForm-Value stabilisiert, um unklare Format-Mischungen bei interner Speicherung und Anzeige zu verhindern.

### Changed
- Laufzeit- und Konfigurationssicherheit für Flatpickr/YForm-Value verbessert.

## [2.2.6] - 2026-08-07

### Changed
- Entfernte den veralteten Vendor-Ordner `assets/vendor/flatpickr`, der nicht mehr für die aktive Integration benötigt wird.
- Addon-Version auf `2.2.6` angehoben.

## [2.2.5] - 2026-08-07

### Changed
- Frontend-Initialisierung von `flatpickr_init.js` auf robustes Hybrid-Verhalten umgestellt: Frontend via `DOMContentLoaded`, Backend weiterhin via jQuery-`rex:ready`.
- `FrontendHelper::includeAssets()` und `FrontendHelper::getAssetsHtml()` um einen vierten Parameter `includeInitScript` erweitert.
- Helper-Ausgabe um Cache-Busting pro Asset (`?v=<filemtime|addon-version>`) ergänzt, um veraltete Browser-Caches zu vermeiden.

### Documentation
- README.de und README zur Frontend-Einbindung überarbeitet.
- Manuelle Einbindung vollständig dokumentiert (inkl. `flatpickr_init.js`, optional ohne Range-Plugin).

## [2.2.4] - 2026-07-21

### Changed
- Vendor per Script `tools/update-a11y-datetime-vendor.sh` auf a11y_datetime `v5.2.7` aktualisiert.
- Flatpickr-Addon-Version auf `2.2.4` angehoben.

### Fixed
- Enthaltene Vendor-Artefakte übernehmen die aktuellen Time-only-Popup-Fixes und Responsive-Monatskorrekturen aus a11y_datetime `v5.2.7`.

## [2.2.3] - 2026-07-20

### Added
- Vendor-Stand auf a11y_datetime v5.2.6 aktualisiert.

### Changed
- Interne Dokumentation und Release-Hinweise auf den neuen a11y_datetime-Stand angehoben.

### Fixed
- Aktuelle a11y_datetime-Builds und Demo-Artefakte werden wieder mit dem Flatpickr-Vendor synchron ausgeliefert.

## [2.2.2] - 2026-07-20

### Added
- Flatpickr-Initializer reicht jetzt sämtliche relevanten Vendor-Optionen (a11y_datetime / flatpickr) als `data-*` Attribute an den Picker weiter. Neben den bisher gepflegten Optionen sind das u. a. `data-allowInput`, `data-allowInvalidPreload`, `data-animate`, `data-announceChanges`, `data-autoFillDefaultTime`, `data-clickOpens`, `data-closeOnSelect`, `data-disableMobile`, `data-enableSeconds`, `data-inline`, `data-shorthandCurrentMonth`, `data-showCloseButton`, `data-showTitleBar`, `data-static`, `data-weekNumbers`, `data-wrap`, `data-altInput`, `data-time_24hr`, `data-altInputClass`, `data-ariaDateFormat`, `data-calendarTitle`, `data-conjunction`, `data-dateFormat`, `data-defaultDate`, `data-initialDayFocus`, `data-maxDate`, `data-maxTime`, `data-minDate`, `data-minTime`, `data-mode`, `data-monthSelectorType`, `data-nextArrow`, `data-now`, `data-position`, `data-prevArrow`, `data-defaultHour`, `data-defaultMinute`, `data-defaultSeconds`, `data-hourIncrement`, `data-minuteIncrement` sowie `data-enable` (kommaseparierte Liste erlaubter Datumsangaben).
- Neue Doku-Sektion in `README.md` und `README.de.md`, die Addon-Defaults und alle durchgereichten Optionen dokumentiert.
- Neues YForm-Value-Feld `flatpickr` für Datum, Datum/Uhrzeit, Uhrzeit und Datumsbereich mit Standard-Settings, Expert-JSON und externer Disable-Callback-Unterstützung.

### Changed
- Vendor-Stand auf a11y_datetime 5.2.4 aktualisiert.
- Interne Refaktorisierung: gemeinsame Options-Zusammenstellung für Einzel- und Range-Picker.

### Fixed
- Flatpickr-Initializer unterstützt jetzt `data-noCalendar`, sodass bei aktivierter Uhrzeit (`data-enableTime="true"`) ein reines Time-Picker-Feld ohne Kalender möglich ist. Bei Kombination beider Optionen wird der Default für `dateFormat`/`altFormat` auf `H:i` gesetzt.
- Range-Picker verwenden jetzt denselben Options-Builder wie Einzelfelder und unterstützen damit alle Vendor-Optionen konsistent.
- Time-only Picker übernimmt jetzt den neuen Minuten-Default (`minuteIncrement = 1`) aus dem Vendor.
- Wheel-Controls profitieren vom Vendor-Fix gegen globale Framework-Styles im Host-Umfeld.
- a11y_datetime (Vendor): Responsive Mehrmonats-Layout stabilisiert. Bei Wechsel der Viewportbreite werden Kalender-, Monats- und Wochentags-Container jetzt konsistent synchronisiert; verbleibende Multi-Month-Breiten und überlappende Monatsüberschriften (insbesondere in Safari) werden zuverlässig zurückgesetzt.
- a11y_datetime (Vendor): Bei reduzierter Monatsanzahl werden überzählige Monats- und Weekday-Gruppen gezielt ausgeblendet, sodass kein „leeres“ oder überlagertes zweites/drittes Blatt sichtbar bleibt.

### Changed
- YForm-Value `flatpickr`: Suchfeld und Suchfilter orientieren sich jetzt am konfigurierten `picker_type` statt pauschal am Text-Filter.
  - `date`/`date_range` nutzen die YForm-`date`-Suchlogik
  - `datetime` nutzt die YForm-`datetime`-Suchlogik
  - `time` nutzt die YForm-`time`-Suchlogik

## [2.1.0]

### Changed
- Flatpickr-Initializer unterstützt jetzt `data-timeRules` (JSON) und reicht die Option als `timeRules` an a11y_datetime weiter (normale und Range-Felder).
- Flatpickr-Initializer unterstützt jetzt außerdem `data-monthYearWheel`, `data-yearRange` und `data-yearWheelManualInput` und reicht diese Optionen an a11y_datetime weiter.
- Flatpickr-Initializer unterstützt jetzt `data-showMonthNavArrows` (Reaktivierung der Month-Arrows, Standard bleibt aus).
- Flatpickr-Initializer unterstützt jetzt `data-showMonths` für mehrere sichtbare Kalenderblätter (z. B. 2 oder 3 Monate bei Range-Workflows).
- Vendor-Stand und Doku auf a11y_datetime v5.2.0-Funktionsumfang angeglichen.

### Fixed
- Time-only-Popover: Tab und Shift+Tab bleiben jetzt im Popover-Zyklus und springen nicht mehr in die Browser-Adresszeile.
- Time-Wheel-Beschriftungen werden jetzt über die gesetzte Locale lokalisiert (Deutsch: „Zeit“, „Fertig“ inklusive ARIA-Labels).
- Mehrmonats-Range mit Month/Year-Wheel: Der Month/Year-Chooser wird nur noch im ersten sichtbaren Monatsblatt verwendet; zusätzliche Trigger in weiteren Blättern entfallen.
- Month/Year-Wheel-Tastatursteuerung stabilisiert: Pfeilsteuerung für die Jahres-Spalte funktioniert jetzt in beide Richtungen und wird nicht mehr vom globalen Kalender-Keydown überlagert.

### Documentation
- README.de und README.md um `data-showMonthNavArrows` und `data-showMonths` inkl. Beispiel für 3-Monats-Range erweitert.

### QA
- Keyboard-Hilfe-Übersetzungen für EN/DE/FR/ES/IT/SL/JA/ZH synchronisiert und geprüft (Build erfolgreich; Dist-Locale-Dateien für ES/IT/SL/JA/ZH/ZH-TW vorhanden; Browser-Laufzeittest in den vorhandenen Demo-Locale-Beispielen für DE/FR/JA durchgeführt).

## [2.0.1] - 2026-07-01

### Changed
- Vendor-Update auf a11y_datetime Release v5.1.5 durchgeführt.
- Neuer optionaler Schalter data-focusOpens wird im Initializer an a11y_datetime durchgereicht (für normale und Range-Felder).
- Update-Skript für Vendor-Refresh verbessert:
  - klarere Fehlermeldung bei fehlendem dist.zip
  - Anzeige vorhandener Release-Assets
  - optionales Tag-Target für gezielte Updates

### Documentation
- README.de ergänzt um Hinweise und Beispiele für das optionale Öffnen bei Fokus.
- README.md analog ergänzt.
