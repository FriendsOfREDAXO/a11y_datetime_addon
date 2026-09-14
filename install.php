<?php

/**
 * Migriert bestehende YForm-Felder vom alten Typnamen 'flatpickr' auf 'a11y_datetime_addon'.
 * Idempotent: ein zweiter Lauf findet keine Zeilen mehr und tut nichts.
 */

if (rex_addon::get('yform')->isAvailable()) {
    $sql = rex_sql::factory();
    $sql->setQuery(
        'UPDATE ' . rex::getTable('yform_field') . ' SET type_name = :new WHERE type_id = :type AND type_name = :old',
        [':new' => 'a11y_datetime_addon', ':type' => 'value', ':old' => 'flatpickr']
    );

    $migratedRows = $sql->getRows();
    if ($migratedRows > 0) {
        rex_logger::factory()->log('notice', 'a11y_datetime_addon: ' . $migratedRows . ' YForm-Feld(er) von Typ "flatpickr" auf "a11y_datetime_addon" migriert.', [], __FILE__, __LINE__);
    }
}
