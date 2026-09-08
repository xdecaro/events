# Xdecaro Core integration

Events uses the Xdecaro Core cross-product reference contract for relationships with Forms, Courses, Competitions, Membership, Documents and future Xdecaro products.

Current integration baseline: Xdecaro Core `1.0.0+`.

The repository does not yet contain the installable Events component. When implementation starts, add one small Events-owned runtime adapter registered through Joomla dependency injection. It must check for the public Core classes before use and return a controlled administrator message when optional Core integration is unavailable. Do not copy Core classes into Events.

Events is not yet allowed to invent its Joomla component element from the repository name. The first real component manifest must define the stable element; once published, cross-product references must use that exact installed Joomla identifier.

Use:

- `Xdecaro\Core\Integration\EntityReference` for `component/entity/id` references;
- `Xdecaro\Core\Integration\RelationReference` for typed links between references.

Events remains the owner of events, sessions/dates, capacity, registrations, waitlists, participants and check-in.

Typical integrations include:

- Events registration -> Forms submission with relation type such as `source_submission`;
- Events participant -> Membership member;
- Events event/session -> Courses edition, lesson or examination;
- Events event/session -> Competitions tournament, season or match, using Competitions component element `com_decarodcl`;
- Events event/registration -> Documents managed documents through the Documents public API.

Events must not absorb Courses or Competitions scheduling logic merely because an event can represent a course session or match. The owning product keeps its domain state; Events stores only its own event-domain state and explicit references.

Do not read or write another product's private tables as the integration API. Optional integrations must fail gracefully when unavailable and must not create circular mandatory dependencies.

Core remains optional until the Events package deliberately declares and enforces it as a mandatory dependency. That decision must update manifests, installer/update behavior, documentation and tests together.

Entity types become stable public API only when explicitly published by Events.
