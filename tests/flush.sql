-- Cleans up database between tests

DROP TABLE IF EXISTS `organization`;
DROP TABLE IF EXISTS `person`;
DROP TABLE IF EXISTS `privs`;
DROP TABLE IF EXISTS `test_table`;
DROP TABLE IF EXISTS `test_table_1`;
DROP TABLE IF EXISTS `test_table_2`;
DROP TABLE IF EXISTS `test_table_3`;
DROP TABLE IF EXISTS `user_groups`;
DROP VIEW IF EXISTS `organization_view`;
DROP VIEW IF EXISTS `person_view`;
DROP TRIGGER IF EXISTS `user_group_delete`;
