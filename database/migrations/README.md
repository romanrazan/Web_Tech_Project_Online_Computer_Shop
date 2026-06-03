# Database Migrations

Use this folder only if the group later needs a schema change.

Rule: no member should directly change tables in phpMyAdmin alone. Add an SQL migration file here, commit it, and all members run the same migration.
