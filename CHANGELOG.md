# Changelog

## 1.2.0 - 2026-09-09

- Aggiunto supporto opzionale a Editor by xdecaro per la descrizione dell'evento tramite il campo `editor` nativo di Joomla con preferenza `decaroeditor` e fallback `none`.
- Conservato il filtro `raw` già esistente per non modificare la semantica dei contenuti HTML salvati.
- Editor resta opzionale: nessun import di classi private Editor, nessun accesso a storage privato e nessuna dipendenza aggiunta a Core.
- Nessuna modifica allo schema dati o alla logica di eventi, sessioni, capienza, registrazioni, waitlist e check-in.
- Conservati supporto Joomla 5/6, `com_decaroevents`, `pkg_decaroevents` e il namespace storico `Xdecaro\Component\Decaroevents`.

## 1.1.2 - 2026-09-09

- Corretto il nome della classe installer del package in `pkg_decaroeventsInstallerScript`, così Joomla esegue realmente il preflight Core obbligatorio.
- Corretto il manifest SQL Joomla usando `charset="utf8"` senza cambiare le tabelle `utf8mb4`.
- Aggiunto `1.1.2.sql` come repair non distruttivo con `CREATE TABLE IF NOT EXISTS` per le sole tabelle Events.
- Aggiunti test runtime reali su Joomla 5.4.8 e 6.1.3 per installazione pulita, repair da 1.1.1 e rifiuto atomico quando Core è assente.
- Nessuna modifica alla logica di eventi, sessioni, capienza, registrazioni, waitlist o check-in.

## 1.1.1 - 2026-09-09

- Corretti i due riferimenti frontend residui al namespace Core deprecato `Xdecaro\Core`; il frontend usa ora il namespace canonico `xdecaro\Core`.
- Rafforzato il validator per controllare tutto il PHP runtime di componente e package contro future regressioni del namespace Core.
- Nessuna modifica alla logica di eventi, sessioni, capienza, registrazioni, waitlist e check-in; il file SQL 1.1.1 è solo un marker di versione Joomla.

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
