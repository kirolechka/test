-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 02 2015 г., 07:23
-- Версия сервера: 5.6.16
-- Версия PHP: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;

DELIMITER $$
--
-- Процедуры
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `history_record`(_requester int, _type varchar(45), _table varchar(45), _master varchar(45), _name varchar(45), _oldvalue text, _newvalue text)
BEGIN

if _oldvalue is null and _newvalue is not null and _newvalue
or _oldvalue is not null and _newvalue is null
or _oldvalue != _newvalue then
	insert into bill.history
	(`requester`, `type`, `schema`, `table`, `master`, `name`, `value`)
	values
	(_requester, _type, database(), _table, _master, _name, _newvalue);
end if;

if _type = 'DELETE' and _name is null then
	insert into bill.history
	(`requester`, `type`, `schema`, `table`, `master`)
	values
	(_requester, _type, database(), _table, _master);
end if;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `currency_list`
--

CREATE TABLE IF NOT EXISTS `currency_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `code` varchar(3) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Дамп данных таблицы `currency_list`
--

INSERT INTO `currency_list` (`id`, `number`, `code`, `name`) VALUES
(1, 643, 'RUB', 'Российский руб.'),
(2, 840, 'USD', 'Доллар США'),
(3, 978, 'EUR', 'Евро'),
(4, 980, 'UAH', 'Украинская гривна'),
(9, 974, 'BYR', 'Белорусский рубль');

-- --------------------------------------------------------

--
-- Структура таблицы `exchange`
--

CREATE TABLE IF NOT EXISTS `exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) DEFAULT NULL,
  `base_currency_id` int(11) DEFAULT NULL,
  `course` double NOT NULL DEFAULT '1',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `currency_id_idx` (`currency_id`),
  KEY `base_currency_id_idx` (`base_currency_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `exchange`
--

INSERT INTO `exchange` (`id`, `currency_id`, `base_currency_id`, `course`, `time`) VALUES
(1, 1, 2, 68.9291, '2015-02-02 01:19:45'),
(2, 1, 3, 78.1105, '2015-02-02 01:19:45'),
(3, 1, 4, 43.2993, '2015-02-02 01:19:45'),
(4, 1, 9, 44.1005, '2015-02-02 01:19:46');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `exchange`
--
ALTER TABLE `exchange`
  ADD CONSTRAINT `fk_exchange_2` FOREIGN KEY (`base_currency_id`) REFERENCES `currency_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_exchange_1` FOREIGN KEY (`currency_id`) REFERENCES `currency_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
