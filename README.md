formgenerator_avisota
=====================

* Formular über den Formulargenerator anlegen bzw. folgende Felder anlegen
> E-Mail-Feld, muss den Feldnamen "email" haben
> Checkbox-Menü-Feld, mit Feldnamen "newsletter_signup" anlegen. Als Optionswert die ID des Avisota-Verteilers angeben. Hier können auch mehrere Verteiler zu Auswahl eingetragen werden
* In die htaccess folgende mod_rewrite-Regel einfügen:
> RewriteRule a/(.+) /_subscription_activation.php?a=confirm&token=$1 [R=301,L]
* Dateien aus TL_ROOT hochladen
* Einstellungen in _subscription_activation.php anpassen
* E-Mail-Templates anpassen (optional)