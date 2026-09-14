<?php

declare(strict_types=1);

/**
 * Deprecated: nur für Alt-Installationen, deren YForm-Felder die
 * install.php-Migration von type_name 'flatpickr' auf 'a11y_datetime_addon'
 * noch nicht durchlaufen haben. Kann in einer künftigen Version entfernt
 * werden. Bewusst NICHT als flatpickr.php benannt, damit YForms
 * Autodiscovery in lib/yform/value/ sie nicht als eigenständigen Typ
 * registriert.
 */
class rex_yform_value_flatpickr extends rex_yform_value_a11y_datetime_addon
{
}
