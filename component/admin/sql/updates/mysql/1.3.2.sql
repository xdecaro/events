-- Events 1.3.2: allow one email to register for different sessions of the same event.
-- Preserve all registration rows; only replace the legacy event-wide unique index.
ALTER TABLE `#__decaroevents_registrations`
  DROP INDEX `uniq_event_email`,
  ADD KEY `idx_event_session_email` (`event_id`,`session_id`,`email`);
