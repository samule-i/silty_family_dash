CREATE TABLE "users"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL UNIQUE,
password CHAR(76) NOT NULL);

CREATE TABLE "calendar"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
note TEXT NOT NULL,
event_date INTEGER NOT NULL);

CREATE TABLE "calendar_archive"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
note TEXT NOT NULL,
event_date INTEGER NOT NULL);

CREATE TABLE "diary"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
title TEXT,
content TEXT);

CREATE TABLE "diary_archive"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL,
username TEXT NOT NULL,
title TEXT,
content TEXT);

CREATE TABLE "rules"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s','now')),
username TEXT NOT NULL,
title TEXT NOT NULL,
note TEXT,
applies_to INTEGER NOT NULL);

CREATE TABLE "rules_archive"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL,
username TEXT NOT NULL,
title TEXT NOT NULL,
note TEXT,
applies_to INTEGER NOT NULL);

CREATE TABLE "stars"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT,
note TEXT,
owner TEXT);

CREATE TABLE "rewards"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
cost INTEGER NOT NULL,
title TEXT NOT NULL,
note TEXT,
image TEXT,
owner TEXT,
award_date INTEGER);

CREATE TABLE "rewards_archive"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
cost INTEGER NOT NULL,
title TEXT NOT NULL,
note TEXT,
image TEXT,
owner TEXT,
award_date INTEGER);

CREATE TABLE "notes"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
title TEXT NOT NULL,
note TEXT NOT NULL);

CREATE TABLE "notes_archive"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
title TEXT NOT NULL,
note TEXT NOT NULL);

CREATE TABLE "external_links"(
id INTEGER PRIMARY KEY AUTOINCREMENT,
date INTEGER NOT NULL DEFAULT (strftime('%s', 'now')),
username TEXT NOT NULL,
title TEXT NOT NULL,
link TEXT NOT NULL);

CREATE TRIGGER update_username UPDATE OF username ON users
    BEGIN
        UPDATE rules SET username = new.username WHERE username = old.username;
		UPDATE rules_archive SET username = new.username WHERE username = old.username;
		UPDATE diary SET username = new.username WHERE username = old.username;
		UPDATE diary_archive SET username = new.username WHERE username = old.username;
		UPDATE stars SET username = new.username WHERE username = old.username;
		UPDATE notes SET username = new.username WHERE username = old.username;
		UPDATE notes_archive SET username = new.username WHERE username = old.username;
		UPDATE rewards SET username = new.username WHERE username = old.username;
		UPDATE rewards_archive SET username = new.username WHERE username = old.username;
		UPDATE calendar SET username = new.username WHERE username = old.username;
		UPDATE calendar_archive SET username = new.username WHERE username = old.username;
                UPDATE external_links SET username = new.username WHERE username = old.username;
	END;

INSERT INTO users username, password VALUES('silty', '$2y$10$dWEB3n9q12CKMr8MzOX/Y.rLIBTLMJUmriOA1Yk.onpIe/uWTU0Fm');
