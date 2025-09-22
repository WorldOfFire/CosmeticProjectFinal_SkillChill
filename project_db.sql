-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Wrz 22, 2025 at 09:39 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_db`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `channels_products`
--

CREATE TABLE `channels_products` (
  `id_ch_products` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `channels_id` int(11) NOT NULL,
  `month` varchar(20) NOT NULL,
  `quantity_sold` int(11) NOT NULL,
  `sales_value_pln` decimal(12,2) NOT NULL,
  `sales_value_eur` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `channels_products`
--

INSERT INTO `channels_products` (`id_ch_products`, `product_id`, `channels_id`, `month`, `quantity_sold`, `sales_value_pln`, `sales_value_eur`) VALUES
(3, 22, 3, 'Styczeń', 12, 0.00, 0.00),
(4, 10, 9, 'Styczeń', 12, 0.00, 0.00),
(5, 10, 15, 'Styczeń', 12, 0.00, 0.00),
(6, 10, 21, 'Styczeń', 12, 0.00, 0.00),
(7, 10, 27, 'Styczeń', 12, 0.00, 0.00),
(8, 10, 33, 'Styczeń', 12, 0.00, 0.00),
(9, 22, 39, 'Styczeń', 12, 0.00, 0.00),
(10, 10, 45, 'Styczeń', 12, 0.00, 0.00),
(11, 22, 51, 'Styczeń', 12, 12.00, 0.00),
(12, 22, 57, 'Styczeń', 12, 12.00, 0.00),
(13, 30, 57, 'Styczeń', 12, 12.00, 0.00),
(17, 10, 65, 'Styczeń', 12, 12.00, 0.00),
(18, 10, 71, 'Styczeń', 12, 12.00, 0.00),
(20, 22, 78, 'Styczeń', 12, 12.00, 0.00),
(21, 30, 84, 'Styczeń', 12, 12.00, 0.00),
(22, 22, 90, 'Styczeń', 12, 12.00, 0.00),
(23, 10, 96, 'Styczeń', 12, 12.00, 0.00),
(24, 30, 102, 'Styczeń', 12, 12.00, 2.81),
(25, 30, 108, 'Styczeń', 12, 50.00, 11.72),
(26, 20, 108, 'Luty', 25, 250.00, 58.59),
(27, 30, 114, 'Lipiec', 12, 12.00, 2.81),
(28, 22, 120, 'Sierpień', 12, 12.00, 2.81),
(29, 22, 126, 'Lipiec', 12, 12.00, 2.81),
(30, 22, 132, 'Lipiec', 12, 12.00, 2.81),
(31, 30, 138, 'Marzec', 12, 12.00, 2.81),
(32, 22, 144, 'Lipiec', 12, 12.00, 2.81),
(33, 30, 150, 'Styczeń', 12, 12.00, 2.81),
(34, 10, 156, 'Styczeń', 12, 12.00, 2.81),
(35, 30, 162, 'Styczeń', 12, 12.00, 2.81),
(36, 25, 163, 'Luty', 12, 12.00, 2.81);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `clients_individual`
--

CREATE TABLE `clients_individual` (
  `id_individual` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_surname` varchar(100) NOT NULL,
  `client_address` text DEFAULT NULL,
  `phone_nr` varchar(50) DEFAULT NULL,
  `client_mail` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients_individual`
--

INSERT INTO `clients_individual` (`id_individual`, `report_id`, `client_name`, `client_surname`, `client_address`, `phone_nr`, `client_mail`) VALUES
(1, 20, 'dsgfsdg', 'xcbvsdfg', 'dxfvgbsdfg', 'dfsgdfg', 'fgsdfg'),
(2, 21, 'werwew', 'erwer', 'werwe', 'werr', 'werwe'),
(3, 22, 'sdfsd', 'sdfsdf', 'sdfsdf', 'sdfsdfd', 'fssdfdf'),
(4, 23, 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfdsf', 'sdfsdf'),
(5, 24, 'sdfds', 'dfsdf', 'sdf', 'sdff', 'sdff'),
(6, 25, 'dfg', 'dfgdf', 'dfgfg', 'dfgfg', 'dfgdfg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `company_corporate_entity`
--

CREATE TABLE `company_corporate_entity` (
  `id_entity` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `legal_form` varchar(50) DEFAULT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `regon` varchar(20) DEFAULT NULL,
  `krs` varchar(20) DEFAULT NULL,
  `legal_address` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_mail` varchar(100) DEFAULT NULL,
  `contact_phone_nr` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_corporate_entity`
--

INSERT INTO `company_corporate_entity` (`id_entity`, `report_id`, `company_name`, `legal_form`, `nip`, `regon`, `krs`, `legal_address`, `contact_person`, `contact_mail`, `contact_phone_nr`) VALUES
(1, 22, 'sdf', 'sdf', 'sdf', 'sdf', 'sfd', 'sfsd', 'sdf', '', ''),
(2, 23, 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsd', 'sdfsd', 'sdfsd', 'sdfsdf'),
(3, 34, 'sdcfsd', 'sdfsd', 'sdfsd', 'sdf', 'sdf', 'sdf', 'sdf', 'sdf', 'sdf');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `company_employees`
--

CREATE TABLE `company_employees` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `employees_index` varchar(20) NOT NULL,
  `registered` tinyint(1) NOT NULL DEFAULT 0,
  `country` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_employees`
--

INSERT INTO `company_employees` (`id`, `name`, `surname`, `mail`, `employees_index`, `registered`, `country`) VALUES
(1, 'Zosia', 'Kowalska', 'anna.kowalska@example.com', 'ES13041389', 1, 'Hiszpania'),
(2, 'Jan', 'Nowak', 'jan.nowak@example.com', 'IT23121789', 1, 'Włochy'),
(5, 'Katarzyna', 'Lewandowska', 'katarzyna.lewandowska@example.com', 'US05075598', 1, 'Stany Zjednoczone'),
(6, 'Michał', 'Kowalczyk', 'michal.kowalczyk@example.com', 'FR10079531', 1, 'Francja'),
(11, 'Magdalena', 'Wojciechowska', 'magdalena.wojciechowska@example.com', 'GB08121365', 1, 'Wielka Brytania'),
(13, 'Sara', 'Nowak', 'sara.nowak@example.com', 'PL24031866', 1, 'Polska'),
(14, 'Marcin', 'Grabowski', 'marcin.grabowski@example.com', 'BH099837', 1, 'Bahrajn'),
(19, 'Paulina', 'Olszewska', 'paulina.olszewska@example.com', 'GB13039639', 1, 'Wielka Brytania'),
(21, 'Magda', 'Sikorska', 'magda.sikorska@example.com', 'ES08107061', 1, 'Hiszpania'),
(23, 'Sylwia', 'Zawadzka', 'sylwia.zawadzka@example.com', 'CA06024649', 1, 'Kanada'),
(26, 'Sławomir', 'Wieczorek', 'slawomir.wieczorek@example.com', 'DE25051258', 1, 'Niemcy'),
(27, 'Joanna', 'Jabłońska', 'joanna.jablonska@example.com', 'PL13105106', 1, 'Polska'),
(28, 'Piotr', 'Leszczyński', 'piotr.leszczynski@example.com', 'ES10114550', 1, 'Hiszpania'),
(31, 'Ewelina', 'Rutkowska', 'ewelina.rutkowska@example.com', 'PL19026128', 1, 'Polska'),
(35, 'Felicja', 'Nowak', 'felicja.nowak@example.com', 'IT18047415', 1, 'Włochy'),
(36, 'Anna', 'Nowak', 'anna.nowak@example.com', 'PL20057715', 1, 'Polska'),
(38, 'anna', 'Black', 'anna.black@example.com', 'PL19027045', 1, 'Polska'),
(44, 'Weronika', 'Test', 'test@test.pl', 'PL06025305', 1, 'Polska'),
(45, 'Tania', 'Tory', 'tory@torywp.pl', 'AU2109119', 1, 'Australia'),
(48, 'sfdf', 'sdfdsf', 'sdfds@sdwsad', 'AZ25096906', 0, 'Azerbejdżan'),
(49, 'sefesf', 'sdfsdf', 'fwefwe@efe', 'BH25095560', 0, 'Bahrajn'),
(50, 'esffd', 'sdfsef', 'sdf@dwa', 'AU25096087', 0, 'Australia'),
(51, 'dsfse', 'sdfds', 'sfwe@sw', 'BH25095038', 1, 'Bahrajn'),
(52, 'efewef', 'wefwe', 'sdfwef@swaww', 'BS25091615', 0, 'Bahamy'),
(53, 'efdsf', 'sdfssdf', 'sdfwea@swqdfwe', 'BH25091536', 0, 'Bahrajn'),
(56, 'dgref', 'sfefsd', 'ewfsds@swdqdf', 'BS25099880', 1, 'Bahamy'),
(57, 'sdfes', 'dsfsd', 'sefsfd@swqd', 'AU25098025', 0, 'Australia'),
(58, 'Emma', 'Zielarczyk', 'zielarczyk@gmail.zl', 'MV25095676', 1, 'Malediwy'),
(60, 'Daria', 'Otoczak', 'otoczak@g.pl', 'AL25098514', 0, 'Albania');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `distributors`
--

CREATE TABLE `distributors` (
  `id` int(11) NOT NULL,
  `distributor_index` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distributors`
--

INSERT INTO `distributors` (`id`, `distributor_index`, `name`, `country`, `address`, `city`, `postal_code`, `phone`, `email`, `website`) VALUES
(1, 'D001', 'Alpha Distribution', 'Poland', 'ul. Warszawska 1', 'Warsaw', '00-001', '+48 22 111 11 11', 'alpha@example.com', 'www.alpha.com'),
(2, 'D002', 'Beta Supplies', 'Germany', 'Musterstr. 2', 'Berlin', '10115', '+49 30 222 22 22', 'beta@example.com', 'www.beta.com'),
(3, 'D003', 'Gamma Traders', 'France', '12 Rue de Lyon', 'Paris', '75001', '+33 1 333 33 33', 'gamma@example.com', 'www.gamma.com'),
(4, 'D004', 'Delta Logistics', 'Italy', 'Via Roma 5', 'Rome', '00100', '+39 06 444 44 44', 'delta@example.com', 'www.delta.com'),
(5, 'D005', 'Epsilon Corp', 'Spain', 'Calle Mayor 10', 'Madrid', '28001', '+34 91 555 55 55', 'epsilon@example.com', 'www.epsilon.com'),
(6, 'D006', 'Zeta Group', 'Netherlands', 'Dam 7', 'Amsterdam', '1012', '+31 20 666 66 66', 'zeta@example.com', 'www.zeta.com'),
(7, 'D007', 'Eta Enterprises', 'Belgium', 'Rue Neuve 20', 'Brussels', '1000', '+32 2 777 77 77', 'eta@example.com', 'www.eta.com'),
(8, 'D008', 'Theta Partners', 'UK', '10 Downing St', 'London', 'SW1A 2AA', '+44 20 888 88 88', 'theta@example.com', 'www.theta.com'),
(9, 'D009', 'Iota Solutions', 'Sweden', 'Storgatan 15', 'Stockholm', '11122', '+46 8 999 99 99', 'iota@example.com', 'www.iota.com'),
(10, 'D010', 'Kappa Imports', 'Norway', 'Karl Johans gate 20', 'Oslo', '0010', '+47 22 101 01', 'kappa@example.com', 'www.kappa.com'),
(11, 'D011', 'Lambda Supplies', 'Denmark', 'Strøget 30', 'Copenhagen', '1000', '+45 33 202 02', 'lambda@example.com', 'www.lambda.com'),
(12, 'D012', 'Mu Logistics', 'Finland', 'Mannerheimintie 40', 'Helsinki', '00100', '+358 9 303 03 03', 'mu@example.com', 'www.mu.com'),
(13, 'D013', 'Nu Traders', 'Portugal', 'Rua Augusta 50', 'Lisbon', '1100', '+351 21 404 04 04', 'nu@example.com', 'www.nu.com'),
(14, 'D014', 'Xi Group', 'Greece', 'Athinas 60', 'Athens', '10551', '+30 21 505 05 05', 'xi@example.com', 'www.xi.com'),
(15, 'D015', 'Omicron Enterprises', 'Ireland', 'O\'Connell St 70', 'Dublin', 'D01', '+353 1 606 06 06', 'omicron@example.com', 'www.omicron.com'),
(16, 'D016', 'Pi Partners', 'Austria', 'Kärntner Str. 80', 'Vienna', '1010', '+43 1 707 07 07', 'pi@example.com', 'www.pi.com'),
(17, 'D017', 'Rho Solutions', 'Switzerland', 'Bahnhofstrasse 90', 'Zurich', '8001', '+41 44 808 08 08', 'rho@example.com', 'www.rho.com'),
(18, 'D018', 'Sigma Imports', 'Poland', 'ul. Krakowska 12', 'Krakow', '30-001', '+48 12 909 09 09', 'sigma@example.com', 'www.sigma.com'),
(19, 'D019', 'Tau Corp', 'Germany', 'Leipziger Str. 22', 'Leipzig', '04109', '+49 341 101 01 01', 'tau@example.com', 'www.tau.com'),
(20, 'D020', 'Upsilon Group', 'France', 'Boulevard Saint-Germain 25', 'Paris', '75005', '+33 1 202 02 02', 'upsilon@example.com', 'www.upsilon.com'),
(21, 'D021', 'Phi Enterprises', 'Italy', 'Via Milano 30', 'Milan', '20100', '+39 02 303 03 03', 'phi@example.com', 'www.phi.com'),
(22, 'D022', 'Chi Partners', 'Spain', 'Calle Sevilla 35', 'Seville', '41001', '+34 95 404 04 04', 'chi@example.com', 'www.chi.com'),
(23, 'D023', 'Psi Solutions', 'Netherlands', 'Kalverstraat 40', 'Amsterdam', '1012', '+31 20 505 05 05', 'psi@example.com', 'www.psi.com'),
(24, 'D024', 'Omega Traders', 'Belgium', 'Avenue Louise 45', 'Brussels', '1050', '+32 2 606 06 06', 'omega@example.com', 'www.omega.com'),
(25, 'D025', 'Alpha2 Distribution', 'UK', 'Baker St 50', 'London', 'NW1 6XE', '+44 20 707 07 07', 'alpha2@example.com', 'www.alpha2.com'),
(26, 'D026', 'Beta2 Supplies', 'Sweden', 'Drottninggatan 55', 'Stockholm', '11136', '+46 8 808 08 08', 'beta2@example.com', 'www.beta2.com'),
(27, 'D027', 'Gamma2 Traders', 'Norway', 'Bogstadveien 60', 'Oslo', '0355', '+47 22 909 09 09', 'gamma2@example.com', 'www.gamma2.com'),
(28, 'D028', 'Delta2 Logistics', 'Denmark', 'Vesterbrogade 65', 'Copenhagen', '1620', '+45 33 101 01', 'delta2@example.com', 'www.delta2.com'),
(29, 'D029', 'Epsilon2 Corp', 'Finland', 'Aleksanterinkatu 70', 'Helsinki', '00100', '+358 9 202 02 02', 'epsilon2@example.com', 'www.epsilon2.com'),
(30, 'D030', 'Zeta2 Group', 'Portugal', 'Avenida da Liberdade 75', 'Lisbon', '1250', '+351 21 303 03 03', 'zeta2@example.com', 'www.zeta2.com');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `distributor_employees`
--

CREATE TABLE `distributor_employees` (
  `id` int(11) NOT NULL,
  `distributor_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `distributor_employees`
--

INSERT INTO `distributor_employees` (`id`, `distributor_id`, `employee_id`) VALUES
(1, 1, 60),
(2, 16, 1),
(3, 15, 2),
(4, 26, 5),
(5, 12, 6),
(6, 17, 11),
(7, 8, 13),
(8, 5, 14),
(9, 24, 19),
(10, 6, 21),
(11, 13, 23),
(12, 15, 26),
(13, 24, 27),
(14, 29, 28),
(15, 4, 31),
(16, 1, 35),
(17, 25, 36),
(18, 13, 38),
(19, 9, 44),
(20, 14, 45),
(21, 8, 48),
(22, 4, 49),
(23, 29, 50),
(24, 22, 51),
(25, 29, 52),
(26, 9, 53),
(27, 7, 56),
(28, 3, 57),
(29, 17, 58);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `entrepreneur`
--

CREATE TABLE `entrepreneur` (
  `id_entrepreneur` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `owner_surname` varchar(100) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `nip` varchar(20) DEFAULT NULL,
  `regon` varchar(20) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `phone_nr` varchar(50) DEFAULT NULL,
  `company_mail` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `entrepreneur`
--

INSERT INTO `entrepreneur` (`id_entrepreneur`, `report_id`, `owner_name`, `owner_surname`, `company_name`, `nip`, `regon`, `company_address`, `phone_nr`, `company_mail`) VALUES
(1, 22, 'sdfsdf', 'sdfsdf', 'sdfsd', 'sdfdsf', 'sdf', 'sdff', 'sdfsdf', 'sdfdf'),
(2, 23, 'fsdf', 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsf', 'sdfsdf'),
(3, 34, 'sdffsd', 'fsdf', 'sdfsdf', 'sdfsdf', 'sdfsdf', 'sdfsd', 'sdfsd', 'sdfsdf');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `inventory`
--

CREATE TABLE `inventory` (
  `id_stock` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `initial_stock` int(11) NOT NULL,
  `delivery` int(11) NOT NULL,
  `sold_quantity` int(11) NOT NULL,
  `remaining` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id_stock`, `report_id`, `product_id`, `initial_stock`, `delivery`, `sold_quantity`, `remaining`) VALUES
(1, 6, 10, 0, 0, 0, 0),
(7, 6, 14, 0, 0, 0, 0),
(8, 6, 23, 0, 0, 0, 0),
(9, 6, 24, 0, 0, 0, 0),
(10, 6, 1, 0, 0, 0, 0),
(11, 6, 7, 0, 0, 0, 0),
(12, 6, 20, 0, 0, 0, 0),
(28, 6, 17, 0, 0, 0, 0),
(31, 12, 10, 12, 12, 12, 12),
(32, 13, 22, 12, 0, 12, 0),
(33, 14, 22, 12, 0, 12, 0),
(34, 14, 30, 12, 0, 12, 0),
(35, 17, 10, 120, 0, 12, 108),
(36, 18, 10, 120, 0, 12, 108),
(37, 20, 22, 120, 0, 12, 108),
(38, 21, 30, 120, 0, 12, 108),
(39, 22, 22, 120, 0, 12, 108),
(40, 23, 10, 120, 0, 12, 108),
(41, 24, 30, 120, 0, 12, 108),
(42, 25, 30, 12, 5, 12, 5),
(43, 25, 20, 20, 10, 25, 5),
(44, 26, 30, 120, 0, 12, 108),
(45, 27, 22, 120, 0, 12, 108),
(46, 28, 22, 120, 20, 12, 128),
(47, 29, 22, 20, 0, 12, 8),
(48, 30, 30, 20, 0, 12, 8),
(49, 31, 22, 20, 0, 12, 8),
(50, 32, 30, 20, 0, 12, 8),
(51, 33, 10, 20, 0, 12, 8),
(52, 34, 30, 30, 0, 12, 18),
(53, 34, 25, 20, 0, 12, 8);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `portal_user`
--

CREATE TABLE `portal_user` (
  `id_user` int(11) NOT NULL,
  `employees_id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `access` tinyint(1) NOT NULL DEFAULT 0,
  `first_login` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portal_user`
--

INSERT INTO `portal_user` (`id_user`, `employees_id`, `login`, `password_hash`, `access`, `first_login`, `created_at`) VALUES
(1, 1, 'zkowalska', '$2y$12$2yeqq9RwJLKBzEVntxFcjOu3vpiROWE7Xpr4SEFpjhZAkPVf8lvhS', 4, 1, '2025-09-17 19:09:19'),
(2, 31, 'erutkowska', '$2y$10$Ek5/1NiudZwNwHONZBGoBOmHItBXiFuHMZf.WGMkmp698HXMzFd4K', 1, 1, '2025-09-17 19:09:42'),
(5, 28, 'pleszczynski', '$2y$10$QDWJrIJOvSnEjO5Ro.ktseLjJS40KHbrESLj6hDkeYmeqPtOT/rUu', 1, 1, '2025-09-17 19:24:48'),
(6, 27, 'jjablonska', '$2y$10$fRf0l7tG.rznd5O/FH6oYOrLt/BzdKqX1o46NGlu2u0oVcRc5vdcK', 1, 0, '2025-09-17 19:24:52'),
(7, 26, 'swieczorek', '$2y$10$6jf/XO06tiMh43.ot.krHubmlBINJrR8tWc64lV3ZPZjG81kNHTGy', 1, 1, '2025-09-17 19:24:55'),
(9, 2, 'jnowak', '$2y$10$EPw6wg0CGCCO.Or681hDJu1EsgVlLKD1szC8OVlRzPKO6NszHc3ry', 4, 1, '2025-09-17 19:32:17'),
(10, 5, 'klewandowska', '$2y$10$FaemFapTIcQB7bBKU6oyXeKAeadOORnKSqT2ZkLj2r7km7EN.upjK', 0, 1, '2025-09-17 19:33:09'),
(11, 6, 'mkowalczyk', '$2y$10$8K4Fk0r00htlbiZpGgQyh.izrQXff1kYtyKgyy.5RMqpcbzqGH4Qq', 0, 1, '2025-09-17 19:33:09'),
(13, 11, 'mwojciechowska', '$2y$10$TJn4L.xGWqFIOvGzT7Mho.kD8U8w0DjNA77MpaDKw9aXxJT3YAS4a', 2, 1, '2025-09-17 19:34:34'),
(15, 13, 'snowak', '$2y$10$qX/Uzjc07GftZWB7o6yhQuwnGEC9AFwvc1RaCF5xyEzahCLfowfoi', 2, 0, '2025-09-17 19:37:22'),
(16, 14, 'mgrabowski', '$2y$12$69OFFTEixGUNwVUZtYdWA.D23DQi/JkfACuq.wPIzrKoK69g10ZLm', 0, 0, '2025-09-17 19:37:44'),
(19, 21, 'msikorska', '$2y$10$Oed2KyKDR2GzsBMLfg/3yOvZMle0xWRPVkET/VR9mY4QwVXWTBcbq', 0, 0, '2025-09-17 19:48:22'),
(20, 19, 'polszewska', '$2y$10$k4oH0uEXqBwTuwSFXskDiO8xUB6hwBg2zj.hapBD9UeRq3fhD1ym2', 0, 0, '2025-09-17 19:52:13'),
(21, 23, 'szawadzka', '$2y$10$No28tyEgCDE88p1zIvv78OIJNlmuhOP0WXd6/cOvTxNnO2X8GDiyO', 0, 0, '2025-09-17 20:08:13'),
(22, 44, 'wtest', '$2y$12$qmUX5vSxYioPO.xDj1cL0.dGy.OVRdsRCggMUgEUrvcw489LOD7LS', 0, 0, '2025-09-22 12:25:47'),
(23, 45, 'ttory', '$2y$12$TXaFXmkJNFHSovSF4BJ7jufLTD4p5fW82m4mXCYeZxxNLf8YMsF8S', 0, 0, '2025-09-22 12:25:48'),
(24, 56, 'dsfefsd', '$2y$12$JZLLAn3LEW49FSRGG93ZNeCiGrZCSOj5hL.APDPSVlmigjfkehBmy', 0, 0, '2025-09-22 15:02:15'),
(25, 51, 'dsdfds', '$2y$12$cbkrCdedyUYHsU.DgptguejxSGdF2Lcp2f6RU29zZIERDymNvW0HG', 0, 0, '2025-09-22 15:02:23'),
(26, 58, 'ezielarczyk', '$2y$12$dZZ.8AHgykDOKj260aJieOiZlRgMTR6eDVcXtCP.dNorkKYA/PT4y', 0, 1, '2025-09-22 18:43:50');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id_product` int(11) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id_product`, `sku`, `name`, `description`) VALUES
(1, 'COS001', 'Krem nawilżający do twarzy', 'Lekki krem nawilżający do codziennej pielęgnacji skóry twarzy.'),
(2, 'COS002', 'Serum przeciwzmarszczkowe', 'Serum z witaminą C i kwasem hialuronowym, wygładza zmarszczki.'),
(3, 'COS003', 'Mleczko do demakijażu', 'Delikatne mleczko do usuwania makijażu, nie podrażnia skóry.'),
(4, 'COS004', 'Tonik odświeżający', 'Tonik oczyszczający pory i przywracający naturalne pH skóry.'),
(5, 'COS005', 'Peeling enzymatyczny do twarzy', 'Łagodny peeling usuwający martwy naskórek.'),
(6, 'COS006', 'Maska nawilżająca do twarzy', 'Intensywnie nawilżająca maska w kremie.'),
(7, 'COS007', 'Krem pod oczy', 'Krem redukujący cienie i opuchliznę pod oczami.'),
(8, 'COS008', 'Krem BB', 'Lekki krem tonujący z SPF, wyrównuje koloryt skóry.'),
(9, 'COS009', 'Pomadka nawilżająca', 'Pomadka do ust z naturalnymi olejkami.'),
(10, 'COS010', 'Balsam do ciała', 'Balsam intensywnie nawilżający i regenerujący skórę.'),
(11, 'COS011', 'Żel pod prysznic', 'Orzeźwiający żel do mycia ciała o delikatnym zapachu.'),
(12, 'COS012', 'Peeling do ciała', 'Peeling cukrowy wygładzający skórę całego ciała.'),
(13, 'COS013', 'Olejek do masażu', 'Relaksujący olejek do masażu całego ciała.'),
(14, 'COS014', 'Krem do rąk', 'Krem ochronny i nawilżający skórę dłoni.'),
(15, 'COS015', 'Pasta do zębów wybielająca', 'Pasta wybielająca z naturalnymi ekstraktami.'),
(16, 'COS016', 'Dezodorant w sprayu', 'Dezodorant bez aluminium, zapewniający świeżość przez cały dzień.'),
(17, 'COS017', 'Szampon nawilżający', 'Szampon do włosów suchych i zniszczonych.'),
(18, 'COS018', 'Odżywka do włosów', 'Odżywka wzmacniająca i regenerująca włosy.'),
(19, 'COS019', 'Maska do włosów', 'Maska głęboko regenerująca włosy zniszczone.'),
(20, 'COS020', 'Lakier do włosów', 'Lakier utrwalający fryzurę i nadający blask.'),
(21, 'COS021', 'Płyn do demakijażu oczu', 'Delikatny płyn do usuwania makijażu oczu.'),
(22, 'COS022', 'Balsam do ust ochronny', 'Balsam ochronny na zimę, chroni przed pękaniem.'),
(23, 'COS023', 'Krem na dzień z SPF', 'Krem na dzień chroniący skórę przed promieniowaniem UV.'),
(24, 'COS024', 'Krem na noc regenerujący', 'Bogaty krem wspomagający regenerację skóry w nocy.'),
(25, 'COS025', 'Maseczka oczyszczająca z glinką', 'Maska absorbująca nadmiar sebum i zanieczyszczenia.'),
(26, 'COS026', 'Serum wzmacniające rzęsy', 'Serum przyspieszające wzrost i pogrubiające rzęsy.'),
(27, 'COS027', 'Puder matujący', 'Puder do twarzy matujący strefę T.'),
(28, 'COS028', 'Korektor pod oczy', 'Korektor kryjący cienie i drobne niedoskonałości.'),
(29, 'COS029', 'Spray termoochronny do włosów', 'Spray chroniący włosy przed wysoką temperaturą.'),
(30, 'COS030', 'Balsam po opalaniu', 'Łagodzący balsam do skóry po ekspozycji na słońce.');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `purchase_report`
--

CREATE TABLE `purchase_report` (
  `id_purchase` int(11) NOT NULL,
  `name_creator` varchar(50) NOT NULL,
  `surname_creator` varchar(50) NOT NULL,
  `login_creator` varchar(50) NOT NULL,
  `index_creator` varchar(50) NOT NULL,
  `quarter` tinyint(4) NOT NULL,
  `year` year(4) NOT NULL,
  `last_year_sales_pl` decimal(10,2) NOT NULL DEFAULT 0.00,
  `last_year_sales_eur` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchases_pl` decimal(10,2) NOT NULL DEFAULT 0.00,
  `purchases_eur` decimal(10,2) NOT NULL DEFAULT 0.00,
  `budget_pl` decimal(10,2) NOT NULL DEFAULT 0.00,
  `budget_eur` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actual_sales_pl` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actual_sales_eur` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_pos` int(11) NOT NULL DEFAULT 0,
  `new_openings` int(11) NOT NULL DEFAULT 0,
  `new_openings_target` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_report`
--

INSERT INTO `purchase_report` (`id_purchase`, `name_creator`, `surname_creator`, `login_creator`, `index_creator`, `quarter`, `year`, `last_year_sales_pl`, `last_year_sales_eur`, `purchases_pl`, `purchases_eur`, `budget_pl`, `budget_eur`, `actual_sales_pl`, `actual_sales_eur`, `total_pos`, `new_openings`, `new_openings_target`, `created_at`) VALUES
(1, 'Anna', 'Kowalska', 'admin1', 'ES13041389', 1, '2025', 12.00, 2.67, 120.00, 26.67, 1230.00, 273.33, 516.00, 114.67, 20, 10, 30, '2025-09-21 17:22:55'),
(2, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', 1, '2025', 12.00, 2.67, 0.00, 0.00, 0.00, 0.00, 516.00, 114.67, 0, 0, 0, '2025-09-21 23:35:16'),
(3, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', 3, '2023', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '2025-09-21 23:42:23'),
(4, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', 1, '2010', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '2025-09-21 23:46:25'),
(5, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', 4, '2021', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '2025-09-21 23:47:52'),
(6, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', 3, '2008', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '2025-09-22 00:14:06'),
(7, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', 1, '2009', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '2025-09-22 00:17:38');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `report`
--

CREATE TABLE `report` (
  `id_report` int(11) NOT NULL,
  `quarter` tinyint(4) NOT NULL,
  `year` year(4) NOT NULL,
  `total_sales_pl` decimal(12,2) NOT NULL,
  `total_sales_eur` decimal(12,2) NOT NULL,
  `name_creator` varchar(100) NOT NULL,
  `surname_creator` varchar(100) NOT NULL,
  `login_creator` varchar(50) NOT NULL,
  `index_creator` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`id_report`, `quarter`, `year`, `total_sales_pl`, `total_sales_eur`, `name_creator`, `surname_creator`, `login_creator`, `index_creator`, `created_at`) VALUES
(2, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:00:42'),
(5, 1, '2024', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:10:05'),
(6, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:19:10'),
(7, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:21:07'),
(8, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:26:49'),
(9, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:31:49'),
(10, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:34:56'),
(11, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:35:29'),
(12, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 02:50:52'),
(13, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:02:55'),
(14, 1, '2025', 24.00, 5.33, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:04:06'),
(17, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:06:32'),
(18, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:08:59'),
(20, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:14:37'),
(21, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:22:52'),
(22, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:32:01'),
(23, 1, '2025', 12.00, 2.67, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:39:36'),
(24, 1, '2025', 12.00, 2.81, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:44:58'),
(25, 1, '2025', 300.00, 70.31, 'Anna', 'Kowalska', 'admin1', 'HR230101', '2025-09-21 03:47:31'),
(26, 3, '2020', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 01:29:48'),
(27, 3, '2017', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 01:31:09'),
(28, 3, '2010', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:20:03'),
(29, 3, '2012', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:20:40'),
(30, 1, '2002', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:23:44'),
(31, 3, '2001', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:28:31'),
(32, 1, '1998', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:31:41'),
(33, 1, '2002', 12.00, 2.81, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:34:10'),
(34, 1, '2009', 24.00, 5.63, 'Anna', 'Kowalska', 'akowalska', 'ES13041389', '2025-09-22 02:36:31');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sales_channels`
--

CREATE TABLE `sales_channels` (
  `id_channels` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `sales_channel_id` int(11) DEFAULT NULL,
  `sale_pln` decimal(12,2) NOT NULL,
  `sale_eur` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_channels`
--

INSERT INTO `sales_channels` (`id_channels`, `report_id`, `sales_channel_id`, `sale_pln`, `sale_eur`) VALUES
(2, 2, 1, 12.00, 2.67),
(3, 5, 1, 12.00, 2.67),
(4, 5, 2, 0.00, 0.00),
(5, 5, 3, 0.00, 0.00),
(6, 5, 4, 0.00, 0.00),
(7, 5, 5, 0.00, 0.00),
(8, 5, 6, 0.00, 0.00),
(9, 6, 1, 12.00, 2.67),
(10, 6, 2, 0.00, 0.00),
(11, 6, 3, 0.00, 0.00),
(12, 6, 4, 0.00, 0.00),
(13, 6, 5, 0.00, 0.00),
(14, 6, 6, 0.00, 0.00),
(15, 7, 1, 12.00, 2.67),
(16, 7, 2, 0.00, 0.00),
(17, 7, 3, 0.00, 0.00),
(18, 7, 4, 0.00, 0.00),
(19, 7, 5, 0.00, 0.00),
(20, 7, 6, 0.00, 0.00),
(21, 8, 1, 12.00, 2.67),
(22, 8, 2, 0.00, 0.00),
(23, 8, 3, 0.00, 0.00),
(24, 8, 4, 0.00, 0.00),
(25, 8, 5, 0.00, 0.00),
(26, 8, 6, 0.00, 0.00),
(27, 9, 1, 12.00, 2.67),
(28, 9, 2, 0.00, 0.00),
(29, 9, 3, 0.00, 0.00),
(30, 9, 4, 0.00, 0.00),
(31, 9, 5, 0.00, 0.00),
(32, 9, 6, 0.00, 0.00),
(33, 10, 1, 12.00, 2.67),
(34, 10, 2, 0.00, 0.00),
(35, 10, 3, 0.00, 0.00),
(36, 10, 4, 0.00, 0.00),
(37, 10, 5, 0.00, 0.00),
(38, 10, 6, 0.00, 0.00),
(39, 11, 1, 12.00, 2.67),
(40, 11, 2, 0.00, 0.00),
(41, 11, 3, 0.00, 0.00),
(42, 11, 4, 0.00, 0.00),
(43, 11, 5, 0.00, 0.00),
(44, 11, 6, 0.00, 0.00),
(45, 12, 1, 12.00, 2.67),
(46, 12, 2, 0.00, 0.00),
(47, 12, 3, 0.00, 0.00),
(48, 12, 4, 0.00, 0.00),
(49, 12, 5, 0.00, 0.00),
(50, 12, 6, 0.00, 0.00),
(51, 13, 1, 12.00, 2.67),
(52, 13, 2, 0.00, 0.00),
(53, 13, 3, 0.00, 0.00),
(54, 13, 4, 0.00, 0.00),
(55, 13, 5, 0.00, 0.00),
(56, 13, 6, 0.00, 0.00),
(57, 14, 1, 24.00, 5.33),
(58, 14, 2, 0.00, 0.00),
(59, 14, 3, 0.00, 0.00),
(60, 14, 4, 0.00, 0.00),
(61, 14, 5, 0.00, 0.00),
(62, 14, 6, 0.00, 0.00),
(65, 17, 1, 12.00, 2.67),
(66, 17, 2, 0.00, 0.00),
(67, 17, 3, 0.00, 0.00),
(68, 17, 4, 0.00, 0.00),
(69, 17, 5, 0.00, 0.00),
(70, 17, 6, 0.00, 0.00),
(71, 18, 1, 12.00, 2.67),
(72, 18, 2, 0.00, 0.00),
(73, 18, 3, 0.00, 0.00),
(74, 18, 4, 0.00, 0.00),
(75, 18, 5, 0.00, 0.00),
(76, 18, 6, 0.00, 0.00),
(78, 20, 1, 12.00, 2.67),
(79, 20, 2, 0.00, 0.00),
(80, 20, 3, 0.00, 0.00),
(81, 20, 4, 0.00, 0.00),
(82, 20, 5, 0.00, 0.00),
(83, 20, 6, 0.00, 0.00),
(84, 21, 1, 12.00, 2.67),
(85, 21, 2, 0.00, 0.00),
(86, 21, 3, 0.00, 0.00),
(87, 21, 4, 0.00, 0.00),
(88, 21, 5, 0.00, 0.00),
(89, 21, 6, 0.00, 0.00),
(90, 22, 1, 12.00, 2.67),
(91, 22, 2, 0.00, 0.00),
(92, 22, 3, 0.00, 0.00),
(93, 22, 4, 0.00, 0.00),
(94, 22, 5, 0.00, 0.00),
(95, 22, 6, 0.00, 0.00),
(96, 23, 1, 12.00, 2.67),
(97, 23, 2, 0.00, 0.00),
(98, 23, 3, 0.00, 0.00),
(99, 23, 4, 0.00, 0.00),
(100, 23, 5, 0.00, 0.00),
(101, 23, 6, 0.00, 0.00),
(102, 24, 1, 12.00, 2.81),
(103, 24, 2, 0.00, 0.00),
(104, 24, 3, 0.00, 0.00),
(105, 24, 4, 0.00, 0.00),
(106, 24, 5, 0.00, 0.00),
(107, 24, 6, 0.00, 0.00),
(108, 25, 1, 300.00, 70.31),
(109, 25, 2, 0.00, 0.00),
(110, 25, 3, 0.00, 0.00),
(111, 25, 4, 0.00, 0.00),
(112, 25, 5, 0.00, 0.00),
(113, 25, 6, 0.00, 0.00),
(114, 26, 1, 12.00, 2.81),
(115, 26, 2, 0.00, 0.00),
(116, 26, 3, 0.00, 0.00),
(117, 26, 4, 0.00, 0.00),
(118, 26, 5, 0.00, 0.00),
(119, 26, 6, 0.00, 0.00),
(120, 27, 1, 12.00, 2.81),
(121, 27, 2, 0.00, 0.00),
(122, 27, 3, 0.00, 0.00),
(123, 27, 4, 0.00, 0.00),
(124, 27, 5, 0.00, 0.00),
(125, 27, 6, 0.00, 0.00),
(126, 28, 1, 12.00, 2.81),
(127, 28, 2, 0.00, 0.00),
(128, 28, 3, 0.00, 0.00),
(129, 28, 4, 0.00, 0.00),
(130, 28, 5, 0.00, 0.00),
(131, 28, 6, 0.00, 0.00),
(132, 29, 1, 12.00, 2.81),
(133, 29, 2, 0.00, 0.00),
(134, 29, 3, 0.00, 0.00),
(135, 29, 4, 0.00, 0.00),
(136, 29, 5, 0.00, 0.00),
(137, 29, 6, 0.00, 0.00),
(138, 30, 1, 12.00, 2.81),
(139, 30, 2, 0.00, 0.00),
(140, 30, 3, 0.00, 0.00),
(141, 30, 4, 0.00, 0.00),
(142, 30, 5, 0.00, 0.00),
(143, 30, 6, 0.00, 0.00),
(144, 31, 1, 12.00, 2.81),
(145, 31, 2, 0.00, 0.00),
(146, 31, 3, 0.00, 0.00),
(147, 31, 4, 0.00, 0.00),
(148, 31, 5, 0.00, 0.00),
(149, 31, 6, 0.00, 0.00),
(150, 32, 1, 12.00, 2.81),
(151, 32, 2, 0.00, 0.00),
(152, 32, 3, 0.00, 0.00),
(153, 32, 4, 0.00, 0.00),
(154, 32, 5, 0.00, 0.00),
(155, 32, 6, 0.00, 0.00),
(156, 33, 1, 12.00, 2.81),
(157, 33, 2, 0.00, 0.00),
(158, 33, 3, 0.00, 0.00),
(159, 33, 4, 0.00, 0.00),
(160, 33, 5, 0.00, 0.00),
(161, 33, 6, 0.00, 0.00),
(162, 34, 1, 12.00, 2.81),
(163, 34, 2, 12.00, 2.81),
(164, 34, 3, 0.00, 0.00),
(165, 34, 4, 0.00, 0.00),
(166, 34, 5, 0.00, 0.00),
(167, 34, 6, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `sales_channels_name`
--

CREATE TABLE `sales_channels_name` (
  `id_sales_channel` int(11) NOT NULL,
  `sale_channel_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_channels_name`
--

INSERT INTO `sales_channels_name` (`id_sales_channel`, `sale_channel_name`) VALUES
(1, 'Professional Sales'),
(2, 'Pharmacy Sales'),
(3, 'E-commerce Sales B2C'),
(4, 'E-commerce Sales B2B'),
(5, 'Third Party'),
(6, 'Other');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_activity`
--

CREATE TABLE `user_activity` (
  `id_activity` int(11) NOT NULL,
  `p_user_id` int(11) NOT NULL,
  `activity_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`id_activity`, `p_user_id`, `activity_date`) VALUES
(13, 9, '2023-09-17 11:00:08'),
(14, 9, '2023-09-17 12:32:08'),
(15, 9, '2023-09-18 11:00:08'),
(16, 10, '2025-09-16 11:00:08'),
(17, 10, '2025-09-12 11:00:08'),
(18, 10, '2025-09-18 11:00:08'),
(19, 11, '2025-09-16 10:00:08'),
(20, 11, '2025-09-13 11:00:08'),
(21, 11, '2025-09-18 11:00:08'),
(22, 13, '2025-09-18 07:00:08'),
(23, 13, '2025-09-16 11:00:08'),
(24, 13, '2025-09-18 11:00:08'),
(25, 15, '2025-09-18 10:30:08'),
(26, 15, '2025-09-14 11:00:08'),
(27, 15, '2025-09-18 11:00:08'),
(28, 1, '2025-09-22 20:43:37'),
(29, 1, '2025-09-22 20:43:38'),
(30, 1, '2025-09-22 20:43:39'),
(31, 1, '2025-09-22 20:43:50'),
(32, 1, '2025-09-22 20:43:52'),
(33, 1, '2025-09-22 20:43:53'),
(34, 1, '2025-09-22 20:44:12'),
(35, 26, '2025-09-22 20:44:49'),
(36, 26, '2025-09-22 20:47:18'),
(37, 26, '2025-09-22 20:47:37'),
(38, 26, '2025-09-22 20:47:54'),
(39, 26, '2025-09-22 20:48:08'),
(40, 26, '2025-09-22 20:48:47'),
(41, 26, '2025-09-22 20:49:13'),
(42, 26, '2025-09-22 20:49:27'),
(43, 26, '2025-09-22 20:49:55'),
(44, 26, '2025-09-22 20:50:31'),
(45, 26, '2025-09-22 20:51:45'),
(46, 26, '2025-09-22 20:51:48'),
(47, 26, '2025-09-22 20:51:50'),
(48, 26, '2025-09-22 20:51:50'),
(49, 26, '2025-09-22 20:51:53'),
(50, 26, '2025-09-22 20:51:58'),
(51, 26, '2025-09-22 20:52:00'),
(52, 26, '2025-09-22 20:52:01'),
(53, 26, '2025-09-22 20:52:03'),
(54, 1, '2025-09-22 20:53:01'),
(55, 1, '2025-09-22 20:56:02'),
(56, 1, '2025-09-22 21:25:04'),
(57, 1, '2025-09-22 21:36:42'),
(58, 1, '2025-09-22 21:36:47'),
(59, 1, '2025-09-22 21:36:50'),
(60, 1, '2025-09-22 21:36:54'),
(61, 1, '2025-09-22 21:36:55'),
(62, 1, '2025-09-22 21:37:02'),
(63, 1, '2025-09-22 21:37:49'),
(64, 1, '2025-09-22 21:37:50');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_attempts`
--

CREATE TABLE `user_attempts` (
  `id_attempt` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_attempts` tinyint(1) NOT NULL DEFAULT 0,
  `account_access` tinyint(1) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_attempts`
--

INSERT INTO `user_attempts` (`id_attempt`, `user_id`, `login_attempts`, `account_access`, `updated_at`) VALUES
(5, 1, 0, 0, '2025-09-22 18:53:01'),
(6, 2, 3, 1, '2025-09-21 11:17:17'),
(9, 5, 0, 0, '2025-09-17 19:24:48'),
(10, 6, 3, 1, '2025-09-17 21:25:35'),
(11, 7, 0, 0, '2025-09-20 16:17:55'),
(13, 9, 1, 0, '2025-09-22 07:24:47'),
(14, 10, 0, 0, '2025-09-17 19:33:09'),
(15, 11, 0, 0, '2025-09-17 19:33:09'),
(17, 13, 3, 1, '2025-09-17 21:24:45'),
(19, 15, 0, 0, '2025-09-17 19:37:22'),
(20, 16, 0, 0, '2025-09-17 19:37:44'),
(23, 19, 0, 0, '2025-09-17 19:48:22'),
(24, 20, 0, 0, '2025-09-17 19:52:13'),
(25, 21, 0, 0, '2025-09-17 20:08:13'),
(26, 22, 0, 0, '2025-09-22 12:25:47'),
(27, 23, 0, 0, '2025-09-22 12:25:48'),
(28, 24, 0, 0, '2025-09-22 15:02:15'),
(29, 25, 0, 0, '2025-09-22 15:02:23'),
(30, 26, 0, 0, '2025-09-22 18:43:50');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `channels_products`
--
ALTER TABLE `channels_products`
  ADD PRIMARY KEY (`id_ch_products`),
  ADD UNIQUE KEY `channels_id` (`channels_id`,`product_id`,`month`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `clients_individual`
--
ALTER TABLE `clients_individual`
  ADD PRIMARY KEY (`id_individual`),
  ADD KEY `report_id` (`report_id`);

--
-- Indeksy dla tabeli `company_corporate_entity`
--
ALTER TABLE `company_corporate_entity`
  ADD PRIMARY KEY (`id_entity`),
  ADD KEY `report_id` (`report_id`);

--
-- Indeksy dla tabeli `company_employees`
--
ALTER TABLE `company_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- Indeksy dla tabeli `distributors`
--
ALTER TABLE `distributors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `distributor_index` (`distributor_index`);

--
-- Indeksy dla tabeli `distributor_employees`
--
ALTER TABLE `distributor_employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `distributor_id` (`distributor_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indeksy dla tabeli `entrepreneur`
--
ALTER TABLE `entrepreneur`
  ADD PRIMARY KEY (`id_entrepreneur`),
  ADD KEY `report_id` (`report_id`);

--
-- Indeksy dla tabeli `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id_stock`),
  ADD UNIQUE KEY `report_id` (`report_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeksy dla tabeli `portal_user`
--
ALTER TABLE `portal_user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `employees_id` (`employees_id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`);

--
-- Indeksy dla tabeli `purchase_report`
--
ALTER TABLE `purchase_report`
  ADD PRIMARY KEY (`id_purchase`),
  ADD UNIQUE KEY `uq_creator_quarter_year` (`login_creator`,`quarter`,`year`);

--
-- Indeksy dla tabeli `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id_report`);

--
-- Indeksy dla tabeli `sales_channels`
--
ALTER TABLE `sales_channels`
  ADD PRIMARY KEY (`id_channels`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `fk_sales_channel` (`sales_channel_id`);

--
-- Indeksy dla tabeli `sales_channels_name`
--
ALTER TABLE `sales_channels_name`
  ADD PRIMARY KEY (`id_sales_channel`);

--
-- Indeksy dla tabeli `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id_activity`),
  ADD KEY `user_activity_ibfk_1` (`p_user_id`);

--
-- Indeksy dla tabeli `user_attempts`
--
ALTER TABLE `user_attempts`
  ADD PRIMARY KEY (`id_attempt`),
  ADD KEY `user_attempts_ibfk_1` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `channels_products`
--
ALTER TABLE `channels_products`
  MODIFY `id_ch_products` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `clients_individual`
--
ALTER TABLE `clients_individual`
  MODIFY `id_individual` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company_corporate_entity`
--
ALTER TABLE `company_corporate_entity`
  MODIFY `id_entity` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_employees`
--
ALTER TABLE `company_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `distributors`
--
ALTER TABLE `distributors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `distributor_employees`
--
ALTER TABLE `distributor_employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `entrepreneur`
--
ALTER TABLE `entrepreneur`
  MODIFY `id_entrepreneur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id_stock` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `portal_user`
--
ALTER TABLE `portal_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `purchase_report`
--
ALTER TABLE `purchase_report`
  MODIFY `id_purchase` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id_report` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `sales_channels`
--
ALTER TABLE `sales_channels`
  MODIFY `id_channels` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT for table `sales_channels_name`
--
ALTER TABLE `sales_channels_name`
  MODIFY `id_sales_channel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id_activity` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `user_attempts`
--
ALTER TABLE `user_attempts`
  MODIFY `id_attempt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `channels_products`
--
ALTER TABLE `channels_products`
  ADD CONSTRAINT `channels_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`),
  ADD CONSTRAINT `channels_products_ibfk_2` FOREIGN KEY (`channels_id`) REFERENCES `sales_channels` (`id_channels`);

--
-- Constraints for table `clients_individual`
--
ALTER TABLE `clients_individual`
  ADD CONSTRAINT `clients_individual_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`id_report`);

--
-- Constraints for table `company_corporate_entity`
--
ALTER TABLE `company_corporate_entity`
  ADD CONSTRAINT `company_corporate_entity_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`id_report`);

--
-- Constraints for table `distributor_employees`
--
ALTER TABLE `distributor_employees`
  ADD CONSTRAINT `distributor_employees_ibfk_1` FOREIGN KEY (`distributor_id`) REFERENCES `distributors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `distributor_employees_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `company_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `entrepreneur`
--
ALTER TABLE `entrepreneur`
  ADD CONSTRAINT `entrepreneur_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`id_report`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`id_report`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id_product`);

--
-- Constraints for table `portal_user`
--
ALTER TABLE `portal_user`
  ADD CONSTRAINT `portal_user_ibfk_1` FOREIGN KEY (`employees_id`) REFERENCES `company_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_channels`
--
ALTER TABLE `sales_channels`
  ADD CONSTRAINT `fk_sales_channel` FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channels_name` (`id_sales_channel`),
  ADD CONSTRAINT `sales_channels_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`id_report`);

--
-- Constraints for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`p_user_id`) REFERENCES `portal_user` (`id_user`);

--
-- Constraints for table `user_attempts`
--
ALTER TABLE `user_attempts`
  ADD CONSTRAINT `user_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `portal_user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
