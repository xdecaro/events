# Changelog

## 1.1.0 - 2026-09-09

- Migrato il consumo delle API pubbliche Core al namespace canonico `xdecaro\Core`.
- Alzato il requisito Core già obbligatorio da 1.1.0+ a 1.3.0+.
- Conservati `com_decaroevents`, `pkg_decaroevents`, `Xdecaro\Component\Decaroevents` e lo schema `#__decaroevents_*`.
- Nessuna modifica alla logica di eventi, sessioni, capienza, registrazioni, waitlist e check-in; il file SQL 1.1.0 è solo un marker di versione Joomla.

## 1.0.0 - 2026-09-08

- Prima release installabile Core-first.
- Gestione eventi, sessioni, capienza, registrazioni e waitlist.
- Check-in amministrativo.
- Elenco e dettaglio eventi frontend con registrazione base.
- ACL, CSRF, query bindate/filtrate e controllo concorrenza sulla capienza.
- Contratti Core `EntityReference` e `RelationReference`.
- Build deterministica, CI, release e update server Joomla.
