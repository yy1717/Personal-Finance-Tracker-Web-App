-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3307
-- 生成日期： 2026-03-09 11:20:32
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `personal_finance`
--

-- --------------------------------------------------------

--
-- 表的结构 `budgets`
--

CREATE TABLE `budgets` (
  `budgetID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `budget_month` date NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `budgets`
--

INSERT INTO `budgets` (`budgetID`, `userID`, `budget_month`, `amount`) VALUES
(1, 3, '2026-01-01', 900.00),
(9, 3, '0000-00-00', 7100.00),
(12, 3, '2026-02-01', 2000.00),
(13, 3, '2026-03-01', 2000.00),
(18, 3, '2026-04-01', 900.00);

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

CREATE TABLE `categories` (
  `categoryID` int(11) NOT NULL,
  `categoryName` varchar(30) NOT NULL,
  `userID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `categories`
--

INSERT INTO `categories` (`categoryID`, `categoryName`, `userID`) VALUES
(1, 'Food', NULL),
(2, 'Transport', NULL),
(3, 'Rent', NULL),
(4, 'Shopping', NULL),
(5, 'Daily_Use', NULL),
(6, 'Sport', NULL),
(7, 'Entertainment', NULL),
(8, 'Travel', NULL),
(9, 'Medical', NULL),
(10, 'Education', NULL),
(11, 'Pet', NULL),
(12, 'Repayment', NULL),
(13, 'Loan', NULL),
(14, 'Game', NULL),
(15, 'Others', NULL);

-- --------------------------------------------------------

--
-- 表的结构 `expenses`
--

CREATE TABLE `expenses` (
  `expenseID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `categoryID` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `expenses`
--

INSERT INTO `expenses` (`expenseID`, `userID`, `categoryID`, `amount`, `expense_date`, `description`) VALUES
(3, 3, 2, 50.00, '2026-01-24', 'test'),
(4, 3, 3, 500.00, '2026-01-25', 'test2'),
(5, 3, 5, 100.00, '2026-01-26', ''),
(6, 3, 15, 8.00, '2026-01-26', 'Test'),
(7, 3, 5, 80.70, '2026-01-26', 'Soap'),
(8, 3, 3, 8.00, '2026-01-26', 'Test'),
(10, 3, 1, 20.00, '2026-03-04', 'Lunch'),
(11, 3, 14, 25.00, '2026-03-03', 'Skin'),
(12, 3, 11, 30.00, '2026-03-06', 'Pet Food'),
(13, 3, 12, 50.00, '2026-03-10', 'Lucy'),
(14, 3, 4, 999.99, '2026-03-09', 'Online Shopping'),
(15, 3, 15, 250.00, '2026-03-06', 'Perfume'),
(16, 3, 10, 30.00, '2026-03-06', 'Chat GPT Go'),
(17, 3, 1, 15.00, '2026-03-06', 'Breakfast'),
(18, 3, 1, 21.50, '2026-03-06', 'Lunch'),
(19, 3, 1, 22.00, '2026-03-06', 'Dinner'),
(20, 3, 1, 18.88, '2026-03-08', 'Breakfast'),
(24, 3, 14, 99.00, '2026-02-10', 'skin'),
(25, 3, 7, 50.00, '2026-02-19', 'gym'),
(26, 3, 1, 10.00, '2026-02-01', 'breakfast'),
(28, 3, 1, 10.00, '2026-02-02', 'breakfast'),
(29, 3, 1, 15.00, '2026-02-02', 'lunch'),
(30, 3, 1, 10.00, '2025-02-03', 'breakfast'),
(31, 3, 1, 10.00, '2026-02-03', 'breakfast'),
(32, 3, 1, 15.00, '2026-03-03', 'lunch'),
(33, 3, 1, 10.00, '2026-02-04', 'breakfast'),
(34, 3, 1, 15.00, '2026-02-04', 'lunch'),
(35, 3, 1, 10.00, '2026-02-05', 'breakfast'),
(36, 3, 1, 15.00, '2026-02-05', 'lunch'),
(37, 3, 1, 10.00, '2026-02-06', 'breakfast'),
(38, 3, 1, 15.00, '2026-02-06', 'lunch'),
(39, 3, 1, 10.00, '2026-02-09', 'breakfast'),
(40, 3, 1, 10.00, '2026-02-10', 'breakfast'),
(41, 3, 1, 15.00, '2026-02-09', 'lunch'),
(42, 3, 1, 15.00, '2026-02-10', 'lunch'),
(43, 3, 1, 10.00, '2026-02-11', 'breakfast'),
(44, 3, 1, 15.00, '2026-02-11', 'lunch'),
(45, 3, 1, 10.00, '2026-02-12', 'breakfast'),
(46, 3, 1, 15.00, '2026-02-12', 'lunch'),
(47, 3, 1, 10.00, '2026-02-13', 'breakfast'),
(48, 3, 1, 15.00, '2026-02-13', 'lunch'),
(49, 3, 1, 10.00, '2026-02-16', 'breakfast');

-- --------------------------------------------------------

--
-- 表的结构 `income`
--

CREATE TABLE `income` (
  `incomeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `incomeCategoryID` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `income_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `income`
--

INSERT INTO `income` (`incomeID`, `userID`, `incomeCategoryID`, `amount`, `income_date`, `description`) VALUES
(1, 3, 7, 20.70, '2026-01-26', 'testing'),
(3, 3, 8, 88.00, '2026-01-27', 'OK'),
(4, 3, 5, 888.00, '2026-01-27', 'Test'),
(8, 3, 1, 1000.00, '2026-02-07', ''),
(14, 3, 4, 1200.00, '2026-03-04', 'Boss'),
(15, 3, 8, 750.00, '2026-03-03', 'Red Envelope'),
(16, 3, 9, 120.00, '2026-03-06', 'Part Time'),
(17, 3, 1, 90.00, '2026-03-06', 'PT'),
(18, 3, 6, 50.00, '2026-03-06', 'From Lucy'),
(20, 3, 3, 111.11, '2026-03-06', 'My Friend Lily'),
(21, 3, 1, 120.00, '2026-03-05', 'PT'),
(22, 3, 4, 80.00, '2026-03-04', 'PT'),
(23, 3, 7, 20.00, '2026-03-08', 'From Stella'),
(25, 3, 8, 1200.00, '2026-02-01', '');

-- --------------------------------------------------------

--
-- 表的结构 `incomecategories`
--

CREATE TABLE `incomecategories` (
  `incomeCategoryID` int(11) NOT NULL,
  `categoryName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `incomecategories`
--

INSERT INTO `incomecategories` (`incomeCategoryID`, `categoryName`) VALUES
(1, 'Salary'),
(2, 'Rent'),
(3, 'Gift'),
(4, 'Bonus'),
(5, 'Investment'),
(6, 'Borrow'),
(7, 'Receiving Payment'),
(8, 'Pocket Money'),
(9, 'Others');

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `userName` varchar(30) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`userID`, `userName`, `email`, `password_hash`, `created_at`) VALUES
(3, 'yy', 'yinyu030710@gmail.com', '$2y$10$4z11ojuwmdrvYBEGTeP8c.kdxH4btaPOMbj5msQOAM/M8qATbeu5q', '2026-01-21 17:44:07');

--
-- 转储表的索引
--

--
-- 表的索引 `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`budgetID`),
  ADD UNIQUE KEY `unique_user_month` (`userID`,`budget_month`);

--
-- 表的索引 `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryID`),
  ADD KEY `userID` (`userID`);

--
-- 表的索引 `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expenseID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `categoryID` (`categoryID`);

--
-- 表的索引 `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`incomeID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `incomeCategoryID` (`incomeCategoryID`);

--
-- 表的索引 `incomecategories`
--
ALTER TABLE `incomecategories`
  ADD PRIMARY KEY (`incomeCategoryID`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `budgets`
--
ALTER TABLE `budgets`
  MODIFY `budgetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- 使用表AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用表AUTO_INCREMENT `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expenseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- 使用表AUTO_INCREMENT `income`
--
ALTER TABLE `income`
  MODIFY `incomeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- 使用表AUTO_INCREMENT `incomecategories`
--
ALTER TABLE `incomecategories`
  MODIFY `incomeCategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 限制导出的表
--

--
-- 限制表 `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- 限制表 `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- 限制表 `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`categoryID`) REFERENCES `categories` (`categoryID`) ON DELETE SET NULL;

--
-- 限制表 `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `income_ibfk_2` FOREIGN KEY (`incomeCategoryID`) REFERENCES `incomecategories` (`incomeCategoryID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
