SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
SET time_zone = "+00:00";


ALTER TABLE `Stat`
  ADD COLUMN `group_id` int(11) NOT NULL AFTER `link_id`, ADD INDEX (`group_id`);


UPDATE Stat s
  SET s.group_id = (SELECT l.group_id FROM Links l WHERE l.id = s.link_id);


ALTER TABLE `Stat`
  ADD `visited_date` DATE NOT NULL AFTER `visited`, ADD INDEX (`visited_date`);


UPDATE Stat
  SET visited_date = DATE_FORMAT(visited, '%Y-%m-%d');
