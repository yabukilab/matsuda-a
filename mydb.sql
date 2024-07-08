-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-07-08 19:22:12
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `mydb`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `pro_code` int(11) NOT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `comments`
--

INSERT INTO `comments` (`id`, `pro_code`, `text`, `created_at`) VALUES
(18, 65, 'あああああ', '2024-07-03 15:15:47'),
(19, 65, 'aa', '2024-07-04 15:17:33'),
(20, 65, 'ああああ', '2024-07-05 05:02:19'),
(21, 65, '例', '2024-07-05 06:16:56'),
(22, 65, 'aaa', '2024-07-08 16:54:42');

-- --------------------------------------------------------

--
-- テーブルの構造 `mst_product`
--

CREATE TABLE `mst_product` (
  `code` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `mst_product`
--

INSERT INTO `mst_product` (`code`, `name`, `price`, `stock`, `number`) VALUES
(1, 'イチゴオレ', 120, 80, 1),
(2, 'バナナオレ', 120, 40, 1),
(3, 'カフェオレ', 120, 40, 1),
(4, 'リチャージ　フルーツ', 120, 10, 1),
(5, 'リチャージ　マンゴー', 120, 10, 1),
(6, 'アクアヨーグルト', 120, 30, 1),
(7, '抹茶ラテ', 120, 10, 1),
(8, 'コカ・コーラ', 170, 20, 2),
(9, '三ツ矢サイダー　ぶどうリンゴ', 160, 20, 2),
(10, 'ゼロサイダー', 150, 20, 2),
(11, 'アイスティー　微糖', 130, 20, 2),
(12, '綾鷹', 160, 10, 2),
(13, '生茶', 140, 20, 2),
(14, 'やかん麦茶', 130, 20, 2),
(15, 'CHILL　OUT', 210, 10, 2),
(16, 'ナタデココ　白ブドウ', 170, 20, 2),
(17, 'いろはす　レモン', 140, 10, 2),
(18, 'いろはす', 120, 20, 2),
(19, 'Dr Pepper', 130, 10, 2),
(20, 'スプライト', 130, 10, 2),
(21, 'アクエリアス', 130, 10, 2),
(22, 'マルチビタミン', 130, 10, 2),
(23, '鉄分ポリフェノール', 140, 10, 2),
(24, '午後の紅茶　ミルクティー', 130, 20, 2),
(25, '午後の紅茶　レモンティー', 130, 10, 2),
(26, '生茶　ほうじ茶', 130, 10, 2),
(27, '香るブラック', 130, 20, 2),
(28, 'PLATINUM　BLACK', 110, 10, 2),
(29, 'WONDA　金の微糖', 130, 10, 2),
(30, 'WONDA　MORNING　SHOT', 130, 10, 2),
(31, 'GOLDEN　DRIP', 130, 10, 2),
(32, 'EMERALD MOUNTAIN', 130, 10, 2),
(35, 'MAX COFFEE', 130, 20, 2),
(36, 'ICE CAFE AU LAIT', 130, 10, 2),
(37, 'ICE COCOA', 150, 10, 2),
(38, '綾鷹　抹茶ラテ', 150, 20, 2),
(39, '綾鷹', 130, 10, 2),
(40, '特茶　PREMIUM', 180, 10, 3),
(41, '伊右衛門', 140, 20, 3),
(42, 'やさしい麦茶', 130, 20, 3),
(43, 'グリーン　ダカラ', 150, 10, 3),
(44, 'ダブルビタミン', 150, 10, 3),
(45, 'きりっと果実', 150, 20, 3),
(46, 'サントリー　天然水', 130, 30, 3),
(47, 'CRAFT BOSS　ラテ', 170, 10, 3),
(48, 'CRAFT BOSS　ブラック', 160, 20, 3),
(50, 'CRAFT BOSS　無糖紅茶', 160, 10, 3),
(51, 'CRAFT BOSS　レモンティー', 160, 20, 3),
(52, 'CRAFT BOSS　フルーツティー', 160, 20, 3),
(53, 'CRAFT BOSS　贅沢ミルクティー', 170, 20, 3),
(54, 'BOSS　カフェラテ', 130, 10, 3),
(55, 'BOSS ブラック', 120, 10, 3),
(56, 'マスカットサイダー', 150, 20, 3),
(57, 'レモンスカッシュ', 150, 20, 3),
(58, 'C.Cレモン', 150, 10, 3),
(59, 'DEKAVITA', 150, 10, 3),
(60, 'ZONE', 190, 10, 3),
(61, 'C.Cレモン　南国フレーバー', 130, 10, 3),
(62, 'MOUNTAM DEW', 130, 10, 3),
(63, 'PEPSI　COLA', 130, 10, 3),
(64, 'なっちゃんリンゴ', 130, 10, 3),
(65, '果実でビタミン', 130, 10, 3);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `mst_product`
--
ALTER TABLE `mst_product`
  ADD PRIMARY KEY (`code`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- テーブルの AUTO_INCREMENT `mst_product`
--
ALTER TABLE `mst_product`
  MODIFY `code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
