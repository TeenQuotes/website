-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;

-- Changement of collation
ALTER TABLE teenquotesold.approve_quotes CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.config CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.connexions_log CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.delete_account CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.delete_quotes CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.newsletter CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.newsletters CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.stats CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.stories CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.teen_quotes_account CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.teen_quotes_comments CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.teen_quotes_favorite CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.teen_quotes_quotes CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.teen_quotes_settings CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.teen_quotes_visitors CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE teenquotesold.tooltips CONVERT TO character SET utf8 COLLATE utf8_unicode_ci;

-- Seed users table
TRUNCATE users;
INSERT INTO users (id, login, password, email, security_level, ip, birthdate, gender, country, city, avatar, about_me, hide_profile, notification_comment_quote, last_visit, created_at)
SELECT id, username, pass, email, security_level, ip, STR_TO_DATE(birth_date, '%d/%m/%Y'), 'F', NULL, city, avatar, about_me, hide_profile, notification_comment_quote, STR_TO_DATE(last_visit, '%d/%m/%Y'), joindate
FROM teenquotesold.teen_quotes_account;

-- Update gender
UPDATE users SET gender = 'F'
WHERE users.id IN (
	SELECT id
	FROM teenquotesold.teen_quotes_account a
	WHERE a.title = 'Miss' OR a.title = 'Mrs'
);
UPDATE users SET gender = 'M'
WHERE users.id IN (
	SELECT id
	FROM teenquotesold.teen_quotes_account a
	WHERE a.title = 'Mr'
);

-- Update security_level
UPDATE users SET security_level = 1 WHERE security_level = 3;

-- Update country
UPDATE users u, countries c, teenquotesold.teen_quotes_account a
SET u.country = c.id
WHERE a.id = u.id
AND c.name = a.country;

-- Update notification_comment_quote
UPDATE users SET notification_comment_quote = notification_comment_quote - 1;

-- Update hide_profile
UPDATE users SET hide_profile = hide_profile - 1;

-- Update default avatar
UPDATE users SET avatar = NULL WHERE avatar = 'icon50.png';

-- Update about_me
UPDATE users SET about_me = REPLACE(about_me, '<br />', '');

-- Seed quotes table
TRUNCATE quotes;
INSERT INTO quotes (id, content, user_id, approved, created_at)
SELECT id, texte_english, auteur_id, approved, timestamp_created
FROM teenquotesold.teen_quotes_quotes;

-- Seed comments table
TRUNCATE comments;
INSERT INTO comments (id, content, quote_id, user_id, created_at)
SELECT id, texte, id_quote, auteur_id, timestamp_created
FROM teenquotesold.teen_quotes_comments;

-- Deal with escaped characters
UPDATE comments SET content = REPLACE(content, "\\\'", "'");
UPDATE comments SET content = REPLACE(content, "\\\&quot;", "&quot;");

-- Seed profile_visitors table
TRUNCATE profile_visitors;
INSERT INTO profile_visitors (id, user_id, visitor_id)
SELECT id, id_user, id_visitor
FROM teenquotesold.teen_quotes_visitors;

-- Seed favorite_quotes table
TRUNCATE favorite_quotes;
INSERT INTO favorite_quotes (id, quote_id, user_id)
SELECT id, id_quote, id_user
FROM teenquotesold.teen_quotes_favorite;

-- Seed newsletter table
TRUNCATE newsletters;
INSERT INTO newsletters (user_id, type)
SELECT id, 'weekly'
FROM teenquotesold.teen_quotes_account;

-- Seed stories table
TRUNCATE stories;
INSERT INTO stories (id, represent_txt, frequence_txt, user_id, created_at)
SELECT id, txt_represent, txt_frequence, id_user, timestamp
FROM teenquotesold.stories;

-- Delete broken references in tables
DELETE FROM comments
WHERE quote_id NOT IN (SELECT id FROM quotes);

DELETE FROM comments
WHERE user_id NOT IN (SELECT id FROM users);

DELETE FROM favorite_quotes
WHERE quote_id NOT IN (SELECT id FROM quotes);

DELETE FROM favorite_quotes
WHERE user_id NOT IN (SELECT id FROM users);

DELETE FROM profile_visitors
WHERE user_id NOT IN (SELECT id FROM users);

DELETE FROM profile_visitors
WHERE visitor_id NOT IN (SELECT id FROM users);

DELETE FROM quotes
WHERE user_id NOT IN (SELECT id FROM users);

DELETE FROM stories
WHERE user_id NOT IN (SELECT id FROM users);

-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;