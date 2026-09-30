# Events by xdecaro

Events è il componente Joomla per eventi, sessioni, capienza, registrazioni, lista d'attesa e check-in.

- Componente: `com_decaroevents`
- Pacchetto: `pkg_decaroevents`
- Versione: `1.3.2`
- Joomla: 6.1.3+
- PHP: 8.3+
- Core by xdecaro: 1.3.0+ obbligatorio

Events usa Core esclusivamente per infrastruttura condivisa (asset/UI e contratti di riferimento) tramite il namespace canonico `xdecaro\Core`. La logica eventi rimane in Events.

## Backend 1.3

Il backend include una Dashboard amministrativa e liste Eventi, Sessioni e Registrazioni basate su SearchTools Joomla, con filtri, ordinamento, paginazione e layout responsive desktop/mobile.

## Correzioni 1.3.2

- La stessa email può registrarsi a sessioni diverse dello stesso evento; resta bloccata la registrazione duplicata alla stessa sessione.
- Le descrizioni create con l'editor sono renderizzate sul frontend mantenendo la formattazione consentita e passando attraverso un sanitizzatore allowlist Joomla contro contenuti XSS.
- La migrazione dello schema sostituisce soltanto il vecchio indice evento+email e non modifica i record esistenti.

## Compatibilità

Dalla versione 1.3.1 Events supporta esclusivamente Joomla 6.1.3 o successivo nella serie Joomla 6. Joomla 5 non è una piattaforma supportata.
