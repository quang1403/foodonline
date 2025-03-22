-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 05, 2025 lúc 02:52 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `code_camp_bd_fos`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `adm_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`adm_id`, `username`, `password`, `email`, `code`, `date`) VALUES
(1, 'ccbd', '0d89ec971a7bcfe26d68c177a9d53334', 'admin@gmail.com', '', '2023-02-22 07:18:13'),
(5, 'admin2', '70ba33708cbfb103f1a8e34afef333ba7dc021022b2d9aaa583aabb8058d8d67', 'admin2@gmail.com', '', '2025-03-03 15:09:27'),
(6, 'quan1', '202cb962ac59075b964b07152d234b70', 'admin@example.com', '12345', '2025-03-04 08:06:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dishes`
--

CREATE TABLE `dishes` (
  `d_id` int(11) NOT NULL,
  `rs_id` int(11) NOT NULL,
  `title` varchar(222) NOT NULL,
  `slogan` varchar(222) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `img` varchar(222) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `dishes`
--

INSERT INTO `dishes` (`d_id`, `rs_id`, `title`, `slogan`, `price`, `img`) VALUES
(11, 5, 'Spring Rolls', '\r\nBắp cải thái nhỏ, hành tây và cà rốt...', 6000.00, '606d75ce105d0.jpg'),
(12, 5, 'Manchurian Chicken', '\r\nMiếng gà nấu chậm với hành lá trong...', 11000.00, '606d7600dc54c.jpg'),
(13, 5, ' Buffalo Wings', 'Cánh gà chiên sốt Buffalo cay...', 12000.00, '606d765f69a19.jpg'),
(14, 6, '\r\nPhô Mai Mac N', 'Ăn kèm với queso cay truyền thống và nước sốt marina...', 9000.00, '606d768a1b2a1.jpg'),
(15, 6, 'Món khoai tây xoắn ', 'Khoai tây cắt lát xoắn ốc, phủ lớp sốt truyền thống...', 6000.00, '606d76ad0c0cb.jpg'),
(16, 6, 'Pasta thịt viên Penne', 'Thịt bò viên thảo mộc tỏi được ném vào nhà của chúng tôi...', 10000.00, '606d76eedbb99.jpg'),
(17, 5, 'Bún Chả Đặc Biệt', 'Thịt nướng thơm ngon, nước mắm đậm đà', 65000.00, 'bun_cha.jpg'),
(18, 5, 'Nem Cua Bể', 'Giòn tan, nhân đầy ắp hương vị biển', 55000.00, 'nem_cua.jpg'),
(19, 5, 'Canh Rau Cải', 'Thanh mát, giải nhiệt, tốt cho sức khỏe', 30000.00, 'canh_cai.jpg'),
(20, 6, 'Phở Tái Lăn', 'Bò xào thơm lừng, nước dùng ngọt thanh', 70000.00, 'pho_bo.jpg'),
(21, 6, 'Phở Gầu', 'Gầu bò mềm béo, nước dùng thơm ngậy', 75000.00, 'pho_bo.jpg'),
(22, 6, 'Phở Gà Xé', 'Thịt gà ta dai ngon, nước dùng thanh mát', 65000.00, 'pho_ga.jpg'),
(23, 7, 'Chả Cá Đặc Biệt', 'Chả cá thơm lừng, ăn kèm rau thì là', 120000.00, 'cha_ca.jpg'),
(24, 7, 'Chả Cá Lăng', 'Hương vị truyền thống, ngon khó cưỡng', 130000.00, 'cha_ca.jpg'),
(25, 7, 'Bún Cá', 'Thanh mát, bổ dưỡng, chuẩn vị Hà Nội', 60000.00, 'bun_ca.jpg'),
(26, 8, 'Cà Phê Sữa Đá', 'Đậm đà, chuẩn vị cà phê Việt', 45000.00, 'cafe_sua.jpg'),
(27, 8, 'Bạc Xỉu', 'Ngọt dịu, béo nhẹ, vị cà phê thơm ngon', 50000.00, 'cafe.jpg'),
(28, 8, 'Trà Sen Vàng', 'Hương trà dịu nhẹ, topping hạt sen ngon miệng', 48000.00, 'tra_sen.jpg'),
(29, 8, 'Cà Phê Cốt Dừa', 'Sánh mịn, thơm lừng vị dừa', 55000.00, 'cot_dua.jpg'),
(30, 8, 'Nâu Nóng', 'Cà phê đậm vị, giữ ấm ngày lạnh', 40000.00, 'nau_nong.jpg'),
(31, 9, 'Sinh Tố Bơ', 'Béo ngậy, giàu dinh dưỡng', 55000.00, 'sinh_to_bo.jpg\r\n'),
(32, 9, 'Trà Sữa Đường Đen', 'Ngọt béo, trân châu dai ngon', 57000.00, 'ts_dd.jpg'),
(33, 10, 'Trà Sữa Khoai Môn', 'Mùi thơm nhẹ, vị béo hấp dẫn', 52000.00, 'tra_sua.jpg'),
(34, 10, 'Trà Sữa Matcha', 'Hương trà xanh Nhật Bản thơm ngon', 57000.00, 'mat_cha.jpg'),
(35, 10, 'Trà Sữa Trân Châu Hoàng Gia', 'Vị ngọt dịu, topping phong phú', 49000.00, 'ts_hoanggia.jpg'),
(36, 11, 'Trà Xoài Nhiệt Đới', 'Vị xoài chua ngọt, giải nhiệt', 53000.00, 'tra_xoai.jpg'),
(37, 11, 'Trà Đào Cam Sả', 'Mát lạnh, thơm sả', 50000.00, 'tra_dao.jpg'),
(38, 11, 'Trà Sữa Oolong', 'Vị trà Oolong thơm nhẹ, không quá béo', 50000.00, 'olong.jpg'),
(39, 12, 'Trà Đen Macchiato', 'Lớp kem sánh mịn, đậm vị trà', 52000.00, 'machi.jpg\r\n'),
(40, 12, 'Trà Sữa Khoai Lang', 'Đậm vị khoai lang tím, độc đáo', 53000.00, 'ts_khoailang.jpg'),
(41, 6, 'Phở Gầu', 'Gầu bò mềm béo, nước dùng thơm ngậy', 75000.00, 'pho_tai.jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `remark`
--

CREATE TABLE `remark` (
  `id` int(11) NOT NULL,
  `frm_id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remark` longtext NOT NULL,
  `remarkDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `remark`
--

INSERT INTO `remark` (`id`, `frm_id`, `status`, `remark`, `remarkDate`) VALUES
(16, 23, 'in process', 'xin chào quý khách', '2025-03-04 13:58:59');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `restaurant`
--

CREATE TABLE `restaurant` (
  `rs_id` int(11) NOT NULL,
  `c_id` int(11) NOT NULL,
  `title` varchar(500) NOT NULL,
  `email` varchar(222) NOT NULL,
  `phone` varchar(222) NOT NULL,
  `url` varchar(222) NOT NULL,
  `o_hr` varchar(222) NOT NULL,
  `c_hr` varchar(222) NOT NULL,
  `o_days` varchar(222) NOT NULL,
  `address` mediumtext NOT NULL,
  `image` varchar(500) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `restaurant`
--

INSERT INTO `restaurant` (`rs_id`, `c_id`, `title`, `email`, `phone`, `url`, `o_hr`, `c_hr`, `o_days`, `address`, `image`, `date`) VALUES
(5, 1, 'Bún Chả Hương Liên', 'buncha.huonglien@gmail.com', '0987654321', 'http://buncha.com', '09:00', '21:00', 'Thứ 2 - Chủ Nhật', '24 Lê Văn Hưu, Hai Bà Trưng, Hà Nội', 'bun_cha_logo.jpg', '2025-03-04 10:00:28'),
(6, 2, 'Phở Thìn', 'phothinhanoi@gmail.com', '0912345678', 'http://phothinhanoi.com', '06:00', '22:00', 'Thứ 2 - Chủ Nhật', '13 Lò Đúc, Hai Bà Trưng, Hà Nội', 'pho_thin.jpg', '2025-03-04 10:04:24'),
(7, 3, 'Chả Cá Lã Vọng', 'chacavong@gmail.com', '0934567890', 'http://chacavong.com', '10:00', '22:00', 'Thứ 2 - Chủ Nhật', '14 Chả Cá, Hoàn Kiếm, Hà Nội', 'cha_ca_la_vong.jpg', '2025-03-04 11:02:25'),
(8, 4, 'Highlands Coffee', 'highlands.hanoi@gmail.com', '0981234567', 'http://highlandscoffee.com', '07:00', '23:00', 'Thứ 2 - Chủ Nhật', '1B Hai Bà Trưng, Hoàn Kiếm, Hà Nội', 'highlands.jpg', '2025-03-04 08:53:29'),
(9, 4, 'Cộng Cà Phê', 'congcaphe.hn@gmail.com', '0967890123', 'http://congcaphe.com', '07:30', '23:00', 'Thứ 2 - Chủ Nhật', '35B Nguyễn Hữu Huân, Hoàn Kiếm, Hà Nội', 'cafe_cong.jpg', '2025-03-05 13:36:55'),
(10, 4, 'The Alley', 'thealley.hn@gmail.com', '0956789012', 'http://thealley.vn', '10:00', '22:30', 'Thứ 2 - Chủ Nhật', '70 Trần Hưng Đạo, Hoàn Kiếm, Hà Nội', 'thealley.jpg', '2025-03-05 13:37:10'),
(11, 7, 'Trà Sữa TocoToco', 'tocotoco.hanoi@gmail.com', '0945678901', 'http://tocotocotea.com', '09:00', '22:00', 'Thứ 2 - Chủ Nhật', '101 Cầu Giấy, Hà Nội', 'toco.jpg', '2025-03-04 11:09:05'),
(12, 8, 'Gong Cha', 'gongcha.hanoi@gmail.com', '0941237890', 'http://gongcha.vn', '10:00', '22:00', 'Thứ 2 - Chủ Nhật', '59 Nguyễn Chí Thanh, Đống Đa, Hà Nội', 'gongcha.jpg', '2025-03-04 08:53:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `res_category`
--

CREATE TABLE `res_category` (
  `c_id` int(11) NOT NULL,
  `c_name` varchar(222) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `res_category`
--

INSERT INTO `res_category` (`c_id`, `c_name`, `date`) VALUES
(1, 'Bún', '2025-03-05 13:34:49'),
(2, 'Phở', '2025-03-05 13:35:05'),
(3, 'Cơm', '2025-03-05 13:35:16'),
(4, 'Đồ uống', '2025-03-05 13:35:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `u_id` int(11) NOT NULL,
  `username` varchar(222) DEFAULT NULL,
  `f_name` varchar(222) DEFAULT NULL,
  `l_name` varchar(222) DEFAULT NULL,
  `email` varchar(222) DEFAULT NULL,
  `phone` varchar(222) DEFAULT NULL,
  `password` varchar(222) DEFAULT NULL,
  `address` mediumtext DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`u_id`, `username`, `f_name`, `l_name`, `email`, `phone`, `password`, `address`, `status`, `date`) VALUES
(9, 'quan', 'Nguyễn', 'Quang', 'darkmeme144@gmail.com', '0988123467', 'e10adc3949ba59abbe56e057f20f883e', '54 Láng Hạ, Đống Đa, Hà Nội\r\nTòa nhà TC', 1, '2025-03-04 12:47:06');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users_orders`
--

CREATE TABLE `users_orders` (
  `o_id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `title` varchar(222) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(222) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users_orders`
--

INSERT INTO `users_orders` (`o_id`, `u_id`, `title`, `quantity`, `price`, `status`, `date`) VALUES
(23, 9, 'Manchurian Chicken', 2, 11000.00, 'in process', '2025-03-04 13:58:59'),
(25, 9, 'Spring Rolls', 1, 6000.00, NULL, '2025-03-05 12:31:40'),
(26, 9, 'Trà Xoài Nhiệt Đới', 1, 53000.00, NULL, '2025-03-05 13:39:37');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adm_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `dishes`
--
ALTER TABLE `dishes`
  ADD PRIMARY KEY (`d_id`);

--
-- Chỉ mục cho bảng `remark`
--
ALTER TABLE `remark`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`rs_id`);

--
-- Chỉ mục cho bảng `res_category`
--
ALTER TABLE `res_category`
  ADD PRIMARY KEY (`c_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`);

--
-- Chỉ mục cho bảng `users_orders`
--
ALTER TABLE `users_orders`
  ADD PRIMARY KEY (`o_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `adm_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `dishes`
--
ALTER TABLE `dishes`
  MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT cho bảng `remark`
--
ALTER TABLE `remark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `rs_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `res_category`
--
ALTER TABLE `res_category`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users_orders`
--
ALTER TABLE `users_orders`
  MODIFY `o_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
