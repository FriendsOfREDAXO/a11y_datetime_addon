# Changelog

Alle wichtigen Änderungen an diesem Addon werden in dieser Datei dokumentiert.

## [3.1.1] - 2026-09-18

### Fixed
- **Kritischer Datenverlust bei `picker_type: datetime`** (und potenziell allen anderen Picker-Typen): War auf derselben REDAXO-Installation zusätzlich noch das alte, vor der Umbenennung in v3.0.0 installierte `flatpickr`-Addon aktiv, luden dessen `boot.php` und dieses Addons `boot.php` beide unabhängig voneinander ihre jeweilige Kopie der Vendor-Bibliothek plus ein eigenes Init-Script (`flatpickr_init.js` bzw. `a11y_datetime_init.js`). Beide Scripts scannen `document.querySelectorAll('.a11y_datetime')` und initialisieren jedes gefundene Element — jedoch mit jeweils eigenem, nicht gegenseitig erkanntem Guard-Attribut (`data-flatpickr-initialized` vs. `data-a11y-datetime-initialized`). Zusätzlich übernimmt die Vendor-Bibliothek beim Erzeugen des sichtbaren `altInput` standardmäßig die komplette `className` des Original-Elements (siehe `setupInputs()`/`altInputClass`-Default in `a11y_datetime.js`) — inklusive der Trigger-Klasse `.a11y_datetime` selbst. Dadurch sah der frisch erzeugte, sichtbare `altInput` für jeden späteren Selector-Scan wie ein brandneues, noch nicht initialisiertes Picker-Feld aus. Ergebnis: Der Picker wurde zweimal auf demselben logischen Feld initialisiert (das zweite Mal auf dem `altInput` der ersten Instanz). Die zweite Instanz erzeugte ein eigenes verstecktes/sichtbares Input-Paar, das der Redakteur tatsächlich bedient — während das ORIGINALE, für den Formular-Submit benannte `<input name="FORM[...]">` bei der ersten (jetzt verwaisten) Instanz verblieb und nie ein `selectedDates`-Update bekam. Beim Absenden wurde daher ein leerer String übertragen, den PHP zu einem ungültigen Datum wie `-0001-11-30 00:00` verarbeitete — der ausgewählte Termin ging vollständig verloren. Fix in `assets/a11y_datetime_init.js`: (1) Das Guard-Attribut wird jetzt synchron VOR dem Aufruf der Vendor-Factory gesetzt (statt danach) und beim Prüfen UND Setzen wird zusätzlich das vom `flatpickr`-Addon verwendete `data-flatpickr-initialized`-Attribut berücksichtigt, sodass sich beide Init-Scripts gegenseitig zuverlässig erkennen. (2) `altInputClass` wird jetzt explizit gesetzt (Original-Klassenliste minus `.a11y_datetime`/`.a11y_datetime_range`), damit der generierte `altInput` nicht erneut als Trigger-Element erkannt wird. Live gegen eine REDAXO-5.21-Instanz mit beiden Addons parallel installiert reproduziert und verifiziert.
- **Platzhalter in der Backend-Konfigurationszusammenfassung** (`value.a11y_datetime_addon.tpl.php`, Texte wie „Typ: %s“, „Minuten: %s“, „Locale: %s“): Die Lang-Dateien nutzten das alte `sprintf`-Format `%s`, REDAXOs `rex_i18n::msg()` ersetzt aber ausschließlich positionelle Platzhalter im Format `{0}`, `{1}`, … . Dadurch blieb `%s` im Backend-Hilfetext unter dem Datums-/Zeitfeld immer wortwörtlich stehen, ohne dass der eigentliche Wert (Feldtyp, Minuten-Schrittweite, Locale, …) je sichtbar wurde. Fix: alle betroffenen Keys in `lang/de_de.lang` und `lang/en_gb.lang` (`a11y_datetime_yform_summary_*`) von `%s` auf `{0}` umgestellt.

## [3.1.0] - 2026-09-14

### Added
- MForm-Integration: neuer Feldtyp `a11y_datetime` über `MForm::registerFieldType()` (MForm ≥10.0), registriert in `boot.php` wenn MForm verfügbar ist. Nutzung im Modul-Code: `$form->addCustomField('a11y_datetime', $id, ['label' => '...', 'data-enableTime' => 'true'])`. Funktioniert sowohl im klassischen MForm-Formular als auch im Flex-Repeater (neue Klasse `FriendsOfREDAXO\A11yDatetimeAddon\MFormFieldType`, implementiert `FieldTypeInterface`). Anders als das YForm-Value-Feld bietet dieser Feldtyp kein eigenes Manager-Formular für Picker-Optionen — Optionen werden direkt als `data-*`-Attribute übergeben, analog zur manuellen HTML-Einbindung.
- **Hinweis**: Der MForm-Formbuilder (visuelle Drag&Drop-Oberfläche) hat aktuell keine Registrierungs-API für Custom-Feldtypen aus Fremd-Addons — `a11y_datetime` erscheint daher nicht in der Builder-Palette, nur programmatisch über `addCustomField()` im Modul-Code nutzbar. Ein entsprechender Vorschlag wurde im MForm-Repository eingereicht.

### Fixed
- Init-Script (`a11y_datetime_init.js`): ein globaler `window.__a11yDatetimeInitRan`-Guard verhinderte, dass nach dem ersten Laden der Seite jemals wieder ein Picker initialisiert wurde — betraf jede Form von dynamisch nachträglich eingefügtem Markup (REDAXO-PJAX-Navigation, MForm-Flex-Repeater-Items nach dem Klonen, Ajax-Inhalte). Der Guard war redundant (die eigentliche Mehrfach-Listener-Bindung war bereits separat über `jQuery(document).data('a11y-datetime-rex-ready-bound')` abgesichert) und wurde ersatzlos entfernt. Betraf auch schon die Vorgängerversion (`flatpickr_init.js`).
- Vendor-Update auf [a11y_datetime v5.2.9](https://github.com/FriendsOfREDAXO/a11y_datetime/releases/tag/v5.2.9): behebt einen Bug im `documentClick`-Handler, der einen Klick außerhalb des Pickers zwar korrekt erkannte, aber nie tatsächlich `close()` aufrief — der Kalender blieb dadurch dauerhaft offen, unabhängig davon, wohin als Nächstes geklickt wurde. Bei mehreren Picker-Instanzen auf einer Seite (z. B. im MForm-Flex-Repeater) konnten dadurch mehrere Kalender gleichzeitig offen bleiben. Root Cause im Vendor-TypeScript-Quellcode gefunden und dort behoben (nicht nur im Addon umgangen).

## [3.0.0] - 2026-09-14

### BREAKING CHANGE
- Komplette Umbenennung des Addons von `flatpickr` auf `a11y_datetime_addon`: Package-Name (`package.yml`), PHP-Namespace (`FriendsOfREDAXO\A11yDatetimeAddon`), YForm-Value-Typ-Name (`a11y_datetime_addon` statt `flatpickr`), Dateinamen (`a11y_datetime_init.js`, `value.a11y_datetime_addon.tpl.php`). Die CSS-Marker-Klasse heißt jetzt `.a11y_datetime` / `.a11y_datetime_range` (statt `.flatpickr` / `.flatpickr_range`). Grund: das Addon nutzte den Vendor-Fork `a11y_datetime` bereits seit einiger Zeit, trug aber weiterhin überall den alten Namen — inklusive eines eigenen README-Absatzes, der das erklären musste. Der Package-Name selbst wurde bewusst NICHT `a11y_datetime` (ohne Suffix), da im REDAXO-Projekt bereits ein gleichnamiges, fremdes Verzeichnis existiert (der npm/TypeScript-Quellcode des Vendor-Forks selbst) — `a11y_datetime_addon` vermeidet diesen Konflikt eindeutig.
- Die alte CSS-Klasse `.flatpickr` wird vom Init-Script nicht mehr erkannt. Bestehende Templates/Module mit `class="flatpickr"` müssen manuell auf `class="a11y_datetime"` umgestellt werden (analog `.flatpickr_range` → `.a11y_datetime_range`).
- Bestehende YForm-Tabellenfelder vom Typ `flatpickr` werden automatisch beim Addon-Update über eine neue `install.php`-Migration auf `a11y_datetime_addon` umgestellt (SQL-Update auf `rex_yform_field.type_name`). Keine manuelle Aktion in YForm selbst nötig.
- Der PHP-Namespace `FriendsOfREDAXO\Flatpickr` entfällt ersatzlos; Code, der `FrontendHelper` direkt importiert, muss auf `FriendsOfREDAXO\A11yDatetimeAddon\FrontendHelper` umgestellt werden.
- Die beiden `data-*`-Attribute `data-flatpickr-disable-callback` und `data-flatpickr-expert-json` heißen jetzt `data-a11y-datetime-disable-callback` und `data-a11y-datetime-expert-json`.

### Added
- Dünner Kompatibilitäts-Shim `rex_yform_value_flatpickr` (erbt von `rex_yform_value_a11y_datetime_addon`) für Alt-Installationen, deren YForm-Felder die automatische DB-Migration noch nicht durchlaufen haben. Als deprecated markiert, wird in einer künftigen Version entfernt.

### Changed
- `boot.php` und `FrontendHelper.php` teilen sich jetzt eine gemeinsame Asset-Liste (`FrontendHelper::assetList()`), keine doppelte Pfadpflege mehr zwischen Backend- und Frontend-Assets.
- Init-Script vereinfacht: keine doppelte CSS-Klassen-Erkennung mehr (nur noch `.a11y_datetime` / `.a11y_datetime_range`), keine tote Vendor-Fallback-Prüfung auf `window.flatpickr` mehr (das geladene Bundle setzt ohnehin immer beide globalen Symbole).
- YForm-Manager-Formular: Feld "Jahresbereich als JSON" (rohes `{"past":10,"future":10}`-Texteingabefeld) ersetzt durch zwei einfache Zahlenfelder "Jahre zurück" / "Jahre voraus". Kein JSON-Tippen mehr im Manager nötig.
- Direkte HTML-/Modul-Nutzung: `data-yearRange` als JSON-Objekt-Attribut ist weiterhin als Fallback unterstützt, aber `data-yearRangePast` / `data-yearRangeFuture` als einfache Zahlen-Attribute sind jetzt der empfohlene Weg — auch für Entwickler, die ein Input-Feld direkt im Modul/Template schreiben, ohne JSON tippen zu müssen. `data-timeRules` und das Expert-JSON-Attribut bleiben bewusst JSON (komplexe bzw. generische Struktur, kein sinnvolles flaches Äquivalent).

### Removed
- Rückwärtskompatibilitäts-Erklärungsabsatz ("Warum das Addon weiterhin flatpickr heißt") aus README entfernt.

## [2.2.12] - 2026-09-14

### Fixed
- `InvalidArgumentException` beim Rendern des YForm-Felds, wenn der `notice`-Parameter beim Anlegen per `setValueField()`-Code fehlt (#5): `getElement('notice')` liefert für einen fehlenden optionalen Endparameter `false` statt `''` zurück; das Template hat diesen booleschen Wert ungeprüft an `rex_i18n::translate()` übergeben, das einen String erwartet. `value.flatpickr.tpl.php` castet den Wert jetzt vor der Prüfung explizit auf `string`, analog zu allen anderen `getElement()`-Aufrufen in der Feldklasse.
- Konfigurationszusammenfassung ("Typ: … | Minuten: … | Locale: …") wurde fälschlich auch im Frontend-Formular als Help-Block ausgegeben (#6). Dieser Hinweis ist reine Backend-Redakteurshilfe und wird jetzt nur noch gerendert, wenn `rex::isBackend()` zutrifft.

### Changed
- Addon-Version auf `2.2.12` angehoben.

## [2.2.11] - 2026-09-07

### Fixed
- Vendor-Refresh für `a11y_datetime` auf `v5.2.8` eingespielt: behebt einen Bug im Time-Wheel-Popover, bei dem eine neu ausgewählte Stunde/Minute intermittierend wieder auf den vorherigen Wert zurückspringen konnte, weil ein `blur`-Handler den Klick auf die Wheel-Option unterlaufen hat.

### Changed
- Addon-Version auf `2.2.11` angehoben.

## [2.2.10] - 2026-08-13

### Changed
- Frischer Vendor-Refresh für `a11y_datetime` aus dem aktuellen Release `v5.2.7` eingespielt.
- Addon-Version auf `2.2.10` angehoben.

### Fixed
- Frontend- und Backend-Asset-Stand auf den aktuellen Release-Build konsolidiert.
- Vendor-Asset-Ordner und Release-Referenz erneut validiert, damit das Flatpickr-Addon auf einem sauberen, aktuellen Stand basiert.

## [2.2.9] - 2026-08-13

### Changed
- Vendor-Build für `a11y_datetime` erneut frisch synchronisiert und der Release-Asset-Stand validiert.
- Addon-Version auf `2.2.9` angehoben.

### Fixed
- Frontend- und Backend-Asset-Stand konsolidiert, damit der Flatpickr-Release wieder auf einem einheitlichen, frischen Build basiert.

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
