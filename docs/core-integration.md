# Xdecaro Core integration

Events 1.1.0 richiede Core by xdecaro 1.3.0+ e usa esclusivamente il namespace canonico `xdecaro\Core` per gli asset condivisi e il contratto pubblico cross-product.

Identità Joomla stabile: `com_decaroevents`.

Entità pubbliche: `event`, `session`, `registration`.

Le integrazioni usano `xdecaro\Core\Integration\EntityReference` e `xdecaro\Core\Integration\RelationReference` tramite `CoreIntegrationService`. Il namespace deprecato `Xdecaro\Core` non è usato da Events 1.1.0.

Events non legge né scrive tabelle private di altri prodotti. Gli identificatori esterni devono essere quelli pubblicati dai rispettivi componenti e non vanno duplicati come logica di dominio in Events.

Events resta proprietario di eventi, sessioni, capienza, registrazioni, lista d'attesa e check-in. Core non contiene logica Events.
