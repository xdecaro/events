# Xdecaro Core integration

Events 1.0.0 uses Core by xdecaro 1.1.0+ for shared Web Asset Manager assets and the public cross-product reference contract. The Joomla component identifier is `com_decaroevents`.

Published entity types: `event`, `session`, `registration`.

Use `EntityReference` and `RelationReference` only through Events' `CoreIntegrationService`. Events never reads another product's private tables. Competitions references use its stable technical identifier `com_decarodcl`.

Events owns event/session/capacity/registration/waitlist/check-in state. Core contains no Events business logic.
