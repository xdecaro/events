# Events — Codex Repository Rules

## Xdecaro Core integration

Events is part of the Xdecaro Joomla ecosystem and should use **Xdecaro Core** for infrastructure that is genuinely shared across multiple Xdecaro extensions.

Core is infrastructure, not Events business logic.

Before implementing reusable technical functionality, inspect whether the same responsibility already exists in Core or clearly belongs there.

Good Core candidates include:

- shared design tokens and `.xdecaro-*` UI primitives;
- light/dark mode foundations;
- responsive administrator UI helpers;
- shared buttons, badges, cards, tables, modals, alerts and loading states;
- shared Web Asset Manager registration;
- generic JavaScript utilities;
- Joomla-compliant AJAX/CSRF helpers;
- dependency/version checks;
- common diagnostics;
- Xdecaro extension registry;
- shared information/update UI;
- genuinely generic cross-extension contracts or events.

Keep Events-specific business logic in this repository, including:

- events and event types;
- dates and sessions;
- venues and event locations;
- capacity and availability;
- registrations/participants when represented by the Events domain;
- waitlists;
- check-in;
- attendance to events;
- event-specific certificates or confirmations;
- schedules/agendas;
- event-specific notifications;
- event-specific reporting;
- event-domain integrations.

Do not move these domain concepts into Core.

A feature belongs in Core only when it is domain-neutral and useful to more than one Xdecaro product.

## Relationship with Bookings

Keep Events and Bookings conceptually separate.

Events manages scheduled events and their participants/workflows.

Bookings manages reservation of resources, appointments, services, spaces or capacity where booking is the primary domain.

Do not merge Bookings logic into Events or Core merely because both may use dates, availability or capacity.

Shared low-level infrastructure may live in Core only if it is genuinely domain-neutral.

## Forms integration

Events may use Forms for configurable registration/intake forms.

Do not duplicate the Forms builder or submission engine inside Events.

Events remains responsible for event capacity, eligibility, participant state, check-in and event workflow.

## Documents integration

Events may use Documents for attachments, participant documents, certificates or event files.

Do not duplicate a complete document-management engine inside Events.

Events owns why a document is required; Documents owns document-domain behavior.

## Courses and Competitions integration

An event may be related to a course, competition or other Xdecaro entity, but relationships must be implemented through stable APIs/events/contracts rather than direct coupling to internal tables whenever possible.

Avoid circular dependencies.

## Dependency policy

If Core becomes mandatory, update package, manifests, installer/update path and minimum Core version coherently.

Missing or incompatible dependencies must produce controlled Joomla administrator messages rather than opaque fatal errors.

Updates must preserve existing event data and configuration.

## Joomla and security

Continue to enforce where relevant:

- server-side ACL;
- Joomla CSRF tokens;
- filtered and validated input;
- escaped output;
- bound database queries;
- secure capacity/registration state changes;
- authorization checks for participant data;
- safe upload handling when attachments are supported;
- no security-sensitive authorization decisions made only in JavaScript.

## Database

Use `#__` for Joomla tables.

Keep Events-domain tables in Events.

Do not move event, session, participant, capacity, waitlist or check-in state into Core.

Database updates must preserve existing data and configuration. Avoid destructive table recreation during normal updates when a safe migration is possible.

Check indexes, joins, duplicate queries and queries inside loops, especially for calendars, participant lists and capacity calculations.

## UI and assets

Prefer Core for shared visual primitives and asset infrastructure when available, while keeping event-specific workflows local.

Verify:

- administrator and frontend behavior;
- calendar/list/detail views;
- capacity and registration states;
- responsive layout;
- accessibility;
- light mode;
- dark mode.

Load shared Core assets through Joomla Web Asset Manager and avoid duplicate CSS/JS registration.

## Regression rule

A Core-related change is complete only when affected Events behavior remains verified.

Check as applicable:

- clean installation;
- update installation;
- supported Joomla versions;
- event creation/editing;
- dates/sessions;
- registrations/participants;
- capacity/waitlists;
- check-in;
- Forms/Documents integrations;
- assets loaded once;
- AJAX;
- ACL and CSRF;
- database migrations;
- PHP errors/warnings;
- JavaScript Console;
- desktop/tablet/smartphone;
- light/dark mode.

Do not combine an opportunistic Core integration with unrelated large refactors.

## Working rule

When the user says **“procedi”**, execute the requested work directly after inspecting the relevant code and dependencies.

Do not ask for another confirmation when requirements are already clear.

If a proposed technical approach is weaker than a safer or more maintainable alternative, explain the issue and use or recommend the stronger approach.
