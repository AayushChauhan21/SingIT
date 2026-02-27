-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 06:22 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `singit`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `photo`) VALUES
(1, 'Admin', 'admin@123.com', '$2y$10$BdionT5EvtVNnYh4yZui1uHmsdxf4N1LRjEozeF.PtqSMI6Uybp62', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1755957113/oagz9u3tjfequ5nv6xsk.png');

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE `artist` (
  `arid` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artist`
--

INSERT INTO `artist` (`arid`, `name`, `photo`, `description`, `image`) VALUES
(1, 'KK', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765473821/uiplqwcd22uyxekpa7sn.jpg', 'Krishnakumar Kunnath, popularly known as KK, was an Indian playback singer. KK is regarded as one of the greatest and most prolific playback singers in India. Noted for his versatility in a variety of music genres, he recorded songs primarily in Hindi, Tamil, Telugu and Kannada language', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483528/vw0czcz84avo4vijndrd.png'),
(2, 'Shreya Ghoshal', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765474235/fbnobcqfmtgifk2l3h8z.jpg', 'Shreya Ghoshal is an Indian playback singer. Noted for her wide vocal range and versatility, she is one of the most prolific and influential singers of India. Ghoshal began learning music at the age of four', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765476699/qcccamc5vq1skippf67a.png'),
(3, 'Altaf Raja', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765474276/qoiuttqsyajibzb86apq.jpg', 'Altaf Raja is an Indian Qawwali singer. In 1997 Altaf gained recognition with his debut album Tum To Thehre Pardesi. His most recent song is Ae Sanam. He uses urdu shayari in his Songs.', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765475129/l9jbbw4joyqkpey89nke.png'),
(4, 'Atif Aslam', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765474524/gu4kaijbjcgdb1qbwlbu.jpg', 'Born in Wazirabad, Punjab, Atif Aslam started his music career in early 2000s. He released his debut album, Jal Pari, in 2004. He went on to sing songs in both Indian (Bollywood) and Pakistani (Lollywood) film industries.', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483504/l76xpfwhrdbxlqreuxcb.png'),
(5, 'Pritam', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765474589/oedyjh7cpwnsygwp1rc2.jpg', 'Pritam Chakraborty, also popularly known mononymously as Pritam, is a National Award winning Indian composer, instrumentalist, music producer and singer', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483472/lmjdhdgbd80w36r6yoqh.png'),
(6, 'Sonu Nigam', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765474635/jateczfpeobgwgajlks1.jpg', 'Sonu Nigam is an Indian playback singer, music director, dubbing artist and actor. He is considered as one of the most versatile and most popular singer of India with a very wide vocal range. His songs vary from romantic to break-up, classical to devotional, Party to patriotic, ghazals to qawwali and rock to pop', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765476671/lntrjpxjfhinpvwdweev.png'),
(7, 'A. R. Rahman', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765475326/bdu9shkkfxakbtux7ntr.jpg', 'Allah Rakha Rahman, also known by the initialism ARR, is an Indian music composer, record producer, singer, songwriter, multi-instrumentalist, and philanthropist known for his works in Indian cinema; predominantly in Tamil and Hindi films, with occasional forays in international cinema', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765475330/embjbslmuwofexylc7az.png'),
(8, 'Alan Walker', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765503868/hakw1jssgjidzvtxkcow.jpg', 'Alan Olav Walker is a Norwegian DJ and record producer. His songs \"Faded\", \"Sing Me to Sleep\", \"Alone\", \"All Falls Down\", \"Ignite\", and \"Darkside\" have each been multi-platinum-certified and reached number 1 on the VG-lista chart in Norway.', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765503874/qcsbydeyjtffcoacvufz.png'),
(9, 'Arijit Singh', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504042/xm4d7lyokz6auhl6lkvh.jpg', 'Arijit Singh is an Indian playback singer, composer, music producer and instrumentalist. A leading figure in contemporary Hindi film music, he is the recipient of several accolades including two National Film Awards and eight Filmfare Awards. He was conferred the Padma Shri by the Government of India in 2025', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504046/jewuo27nlzfyhujxxejt.png'),
(10, 'Weeknd', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504741/sbb3fi5f4z1mibz2jw30.jpg', 'Abel Makkonen Tesfaye, known professionally as the Weeknd, is a Canadian singer-songwriter, record producer, and actor. Regarded as an influential figure in popular music, he is known for his light-lyric tenor vocal range and falsetto, as well as his alternative R&B sound. Wikipedia\r\nBorn: 16 February 1990 (age 35 years)', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504744/ruazimx7gsjxi6s2j1dm.png'),
(11, 'Eminem', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504903/bvnt6qscfrs8qcglsywo.jpg', 'Marshall Bruce Mathers III, known professionally as Eminem, is an American rapper, songwriter, and record producer', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504910/gzfdlfap7fsieejcxebh.png'),
(12, 'Amir Jamal', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1756518298/jmf2eroiur1l0cgbq4bu.jpg', NULL, NULL),
(17, 'Imagine Dragons', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1756021860/xufijje9sgt8kx9buayk.jpg', NULL, NULL),
(18, 'Atif Aslam', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1756520054/jbxw1hysnttq63hoxgxa.jpg', 'Born in Wazirabad, Punjab, Atif Aslam started his music career in early 2000s. He released his debut album, Jal Pari, in 2004. He went on to sing songs in both Indian (Bollywood) and Pakistani (Lollywood) film industries.', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1761629811/115801907_316182089759989_6656461439504800059_n-removebg-preview_sajr7b.png');

-- --------------------------------------------------------

--
-- Table structure for table `artist_song`
--

CREATE TABLE `artist_song` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artist_song`
--

INSERT INTO `artist_song` (`id`, `song_id`, `artist_id`) VALUES
(29, 1, 1),
(2, 2, 7),
(3, 3, 2),
(4, 4, 2),
(31, 5, 7),
(7, 6, 9),
(15, 7, 11),
(9, 8, 1),
(16, 9, 11),
(28, 10, 10),
(21, 11, 11),
(22, 12, 5),
(23, 13, 11),
(33, 20, 1),
(30, 21, 8),
(32, 22, 17);

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `gid` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`gid`, `name`, `image`) VALUES
(1, 'Sad', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765473036/u4supfnd845i8cgqrtgf.jpg'),
(2, 'Traveling', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765473063/yxpire4omo99kktcitz2.jpg'),
(3, 'Romantic', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765473081/lxqqhax28c8bxrpwtg4z.jpg'),
(4, 'Pop Music', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765479130/ewm77nit2nblzudf8mxl.jpg'),
(5, 'kids', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765482895/hfg8horlfqmkckidn6x8.jpg'),
(6, 'Workout', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765482942/ohqwaj6a5zqcdut33qxh.jpg'),
(7, 'Phonk', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483092/fjqhyqmi9ul4hwmpempg.jpg'),
(8, 'Party', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483159/jxs4pq37blrdafdb0qdj.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `genre_song`
--

CREATE TABLE `genre_song` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genre_song`
--

INSERT INTO `genre_song` (`id`, `song_id`, `genre_id`) VALUES
(34, 1, 1),
(2, 2, 3),
(3, 3, 3),
(4, 4, 3),
(36, 5, 2),
(7, 6, 3),
(15, 7, 4),
(9, 8, 3),
(16, 9, 7),
(32, 10, 6),
(33, 10, 8),
(21, 11, 4),
(22, 12, 3),
(23, 13, 8),
(39, 20, 1),
(38, 20, 3),
(35, 21, 8),
(37, 22, 8);

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `lid` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `image` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `language`
--

INSERT INTO `language` (`lid`, `name`, `image`) VALUES
(1, 'English', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765473362/um9u7xqybnaxv6i3fjkq.jpg'),
(2, 'Hindi', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765473371/eqpwcon1jone9wqakdjo.jpg'),
(3, 'Tamil', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483245/eg1eakfv73e2w3biu6uo.jpg'),
(4, 'Punjabi ', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483357/eep5ywoy8b2jgkoqvju1.jpg'),
(5, 'Urdu', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765483388/ki1ghkma725q3d3hw3lp.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `language_song`
--

CREATE TABLE `language_song` (
  `id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `language_song`
--

INSERT INTO `language_song` (`id`, `song_id`, `language_id`) VALUES
(1, 1, 2),
(2, 21, 1),
(4, 22, 1),
(5, 20, 2);

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_songs`
--

CREATE TABLE `playlist_songs` (
  `id` int(11) NOT NULL,
  `playlist_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `slider`
--

CREATE TABLE `slider` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slider`
--

INSERT INTO `slider` (`id`, `sid`) VALUES
(1, 1),
(2, 8);

-- --------------------------------------------------------

--
-- Table structure for table `song`
--

CREATE TABLE `song` (
  `sid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `poster` varchar(255) NOT NULL,
  `length` time DEFAULT NULL,
  `lyrics` text DEFAULT NULL,
  `album` varchar(100) DEFAULT NULL,
  `instrumental` varchar(255) DEFAULT NULL,
  `vocal` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `song`
--

INSERT INTO `song` (`sid`, `name`, `image`, `poster`, `length`, `lyrics`, `album`, `instrumental`, `vocal`) VALUES
(1, 'Zara Sa', 'https://i.scdn.co/image/ab67616d0000b273727d531901c07a499498c544', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765475672/dfztt05qcudrlqvmv8kd.jpg', '00:05:04', '[00:36.57] ज़रा सी दिल में दे जगह तू\r\n[00:42.54] ज़रा सा अपना ले बना\r\n[00:48.33] ज़रा सा ख़्वाबों में सजा तू\r\n[00:54.35] ज़रा सा यादों में बसा\r\n[00:58.77] मैं चाहूँ तुझ को, मेरी जाँ, बेपनाह\r\n[01:04.35] फ़िदा हूँ तुझ पे, मेरी जाँ, बेपनाह\r\n[01:10.70] \r\n[01:35.19] ज़रा सी दिल में दे जगह तू\r\n[01:41.18] ज़रा सा अपना ले बना\r\n[01:46.93] ज़रा सा ख़्वाबों में सजा तू\r\n[01:52.66] ज़रा सा यादों में बसा\r\n[01:58.22] \r\n[02:15.50] मैं तेरे, मैं तेरे क़दमों में रख दूँ ये जहाँ\r\n[02:20.98] मेरा इश्क़ दीवानगी\r\n[02:27.19] है नहीं, है नहीं आशिक़ कोई मुझ सा तेरा\r\n[02:32.71] तू मेरे लिए बंदगी\r\n[02:38.07] मैं चाहूँ तुझ को, मेरी जाँ, बेपनाह\r\n[02:44.01] फ़िदा हूँ तुझ पे, मेरी जाँ, बेपनाह\r\n[02:49.92] \r\n[03:14.75] (ज़रा सी दिल में दे जगह तू)\r\n[03:20.75] (ज़रा सा अपना ले बना)\r\n[03:26.45] (ज़रा सा ख़्वाबों में सजा तू)\r\n[03:32.28] (ज़रा सा यादों में बसा)\r\n[03:37.48] कह भी दे, कह भी दे दिल में तेरे जो है छुपा\r\n[03:42.95] ख़्वाहिश है जो तेरी\r\n[03:49.07] रख नहीं, रख नहीं पर्दा कोई मुझ से, ऐ जाँ\r\n[03:54.68] कर ले तू मेरा यक़ीं\r\n[04:00.18] मैं चाहूँ तुझ को, मेरी जाँ, बेपनाह\r\n[04:05.93] फ़िदा हूँ तुझ पे, मेरी जाँ, बेपनाह\r\n[04:11.94] ', 'Jannat (Original Motion Picture Soundtrack)', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765475656/fns1hpdrzmnleehjj59i.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765475642/h9w0unbzooxdn9z8aqbh.mp3'),
(6, 'Milne Hai Mujhse Aayi', 'https://i.scdn.co/image/ab67616d0000b273c23b439a783ec15f5f503a9a', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765504506/xlzszhmmrqthqge6sppm.jpg', '00:04:58', '[00:13.58] Milne hai mujhse aayi\r\n[00:16.88] Phir jaane kyun tanhaai\r\n[00:20.14] Kis mod pe hai laayi aashiqui?\r\n[00:25.46] Oh, khud se hai ya Khuda se\r\n[00:30.15] Iss pal meri ladaai?\r\n[00:33.54] Kis mod pe hai laayi aashiqui?\r\n[00:39.30] ♪\r\n[00:52.60] Aashiqui baazi hai taash ki\r\n[00:56.26] Toot\'te-bante vishwaas ki\r\n[01:02.51] Oh, milne hai mujhse aayi\r\n[01:06.77] Phir jaane kyun tanhaai\r\n[01:10.13] Kis mod pe hai laayi aashiqui?\r\n[01:16.64] ♪\r\n[01:30.18] Jaane kyun main sochta hoon\r\n[01:33.41] \"Khaali sa main ek raasta hoon\"\r\n[01:36.74] Tune mujhe kahin kho diya hai\r\n[01:40.12] Ya main kahin khud laapata hoon?\r\n[01:44.01] Aa, dhoond le tu phir mujhe\r\n[01:50.76] Qasmein bhi doon toh kya tujhe?\r\n[01:56.26] Aashiqui baazi hai taash ki\r\n[01:59.55] Toot\'te-bante vishwaas ki\r\n[02:06.71] Milne hai mujhse aayi\r\n[02:10.18] Phir jaane kyun tanhaai\r\n[02:13.32] Kis mod pe hai laayi aashiqui?\r\n[02:19.45] ♪\r\n[02:50.09] Toota hua saaz hoon main\r\n[02:53.43] Khud se hi naraaz hoon main\r\n[02:56.72] Seene mein jo kahin pe dabi hai\r\n[03:00.17] Aisi koi awaaz hoon main\r\n[03:04.09] Sun le mujhe tu bin kahe\r\n[03:10.70] Kab tak khamoshi dil sahe?\r\n[03:16.11] Aashiqui baazi hai taash ki\r\n[03:19.52] Toot\'te-bante vishwaas ki\r\n[03:22.82] Aashiqui baazi hai taash ki\r\n[03:26.10] Toot\'te-bante vishwaas ki\r\n[03:32.59] Oh, milne hai mujhse aayi\r\n[03:36.86] Phir jaane kyun tanhaai\r\n[03:40.06] Kis mod pe hai laayi aashiqui?\r\n[03:45.14] Oh-oh, khud se hai ya Khuda se\r\n[03:50.11] Iss pal meri ladaai?\r\n[03:53.47] Kis mod pe hai laayi aashiqui?\r\n[03:59.68] ♪\r\n[04:12.71] Aashiqui baazi hai taash ki\r\n[04:16.22] Toot\'te-bante vishwaas ki\r\n[04:21.64] ', 'AASHIQUI 2', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765504498/q4cz9q2yguhaixse6bwy.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765504490/vptkp2gyydj3ierhbukl.mp3'),
(7, 'Mockingbird', 'https://i.scdn.co/image/ab67616d0000b2731bec21e57fff76db49e15a70', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765505948/c8jcjyufcwysoylgejmh.jpg', '00:04:11', '[00:02.39] Yeah\r\n[00:04.46] I know sometimes things may not always make sense to you right now\r\n[00:09.98] But hey, what daddy always tell you?\r\n[00:13.67] Straighten up little soldier\r\n[00:15.82] Stiffen up that upper lip\r\n[00:18.32] What you crying about?\r\n[00:20.57] You got me\r\n[00:22.06] Hailie, I know you miss your mom\r\n[00:23.84] And I know you miss your dad when I\'m gone\r\n[00:25.76] But I\'m trying to give you the life that I never had\r\n[00:27.81] I can see you\'re sad\r\n[00:29.16] Even when you smile\r\n[00:30.28] Even when you laugh\r\n[00:31.26] I can see it in your eyes\r\n[00:32.65] Deep inside, you wanna cry\r\n[00:33.93] Cuz you\'re scared\r\n[00:34.84] I ain\'t there?\r\n[00:35.40] Daddy\'s with you in your prayers\r\n[00:36.68] No more crying\r\n[00:37.54] Wipe them tears\r\n[00:38.09] Daddy\'s here\r\n[00:38.83] No more nightmares\r\n[00:39.58] We gonna pull together through it\r\n[00:40.81] We gon\' do it\r\n[00:41.86] Laney, uncle\'s crazy ain\'t he?\r\n[00:43.25] Yeah but he loves you girl and you better know it\r\n[00:45.16] We\'re all we got in this world\r\n[00:46.72] When it spins\r\n[00:47.35] When it swirls\r\n[00:48.03] When it whirls\r\n[00:48.72] When it twirls\r\n[00:49.44] Two little beautiful girls\r\n[00:50.90] Looking puzzled, in a daze\r\n[00:52.30] I know it\'s confusing you\r\n[00:53.71] Daddy\'s always on the move\r\n[00:55.12] Mama\'s always on the news\r\n[00:56.53] I try to keep you sheltered from it\r\n[00:58.29] But somehow it seems, the harder that I try to do that\r\n[01:00.48] The more it backfires on me\r\n[01:02.07] All the things, growing up\r\n[01:03.45] As daddy that he had to see\r\n[01:04.70] Daddy don\'t want you to see\r\n[01:06.44] But you see just as much as he did\r\n[01:07.54] That we did not plan it to be this way\r\n[01:09.26] You\'re mother and me\r\n[01:10.49] But things have got so bad between us\r\n[01:11.98] I don\'t see us ever being\r\n[01:13.20] Together ever again\r\n[01:14.69] Like we used to be when was teenagers\r\n[01:16.56] But then of course\r\n[01:17.64] Everything always happens for a reason\r\n[01:19.60] I guess it was never meant to be\r\n[01:21.29] But it\'s just something\r\n[01:22.13] We have no control over\r\n[01:23.30] And that\'s what destiny is\r\n[01:25.06] But no more worries\r\n[01:26.39] Rest your head and go to sleep\r\n[01:27.47] Maybe one day we\'ll wake up\r\n[01:28.89] And this will all just be a dream\r\n[01:30.34] Now hush little baby don\'t you cry\r\n[01:32.90] Everythings gonna be alright\r\n[01:35.11] Stiffen that upper lip up little lady\r\n[01:37.49] I told ya, daddy\'s here to hold ya\r\n[01:39.81] Through the night\r\n[01:40.92] I know mommy\'s not here right now and we don\'t know why\r\n[01:44.40] We feel how we feel inside\r\n[01:46.53] It may seem a little crazy, pretty baby\r\n[01:49.35] But I promise, Mama\'s gonna be alright\r\n[01:52.20] It\'s funny\r\n[01:53.32] I remember back one year when daddy had no money\r\n[01:55.92] Mommy wrapped the Christamas presents up\r\n[01:57.85] And stuck them under the tree\r\n[01:58.72] And said some of them were from me\r\n[02:00.36] Cos daddy couldn\'t buy \'em\r\n[02:01.60] I\'ll never forget that Christmas\r\n[02:03.09] I sat up the whole night cryin\'\r\n[02:04.45] Cuz daddy felt like a bum\r\n[02:05.98] See daddy had a job\r\n[02:07.19] But his job was to keep the food on the table for you and mom\r\n[02:09.55] And at the time every house that we lived in\r\n[02:12.64] Either kept getting broken into and robbed or shot up on the block\r\n[02:15.64] And your mom, was saving money\r\n[02:17.54] For you in a jar trying to start a piggy bank for you\r\n[02:20.15] So you can go to college\r\n[02:21.38] Almost had a thousand dollars\r\n[02:23.04] Till someone broke in and stole it\r\n[02:24.71] And I know it hurt so bad it broke your mama\'s heart\r\n[02:27.37] And it seemed like everything was just starting to fall apart\r\n[02:30.02] Mom and dads was arguing a lot\r\n[02:32.42] So mama moved back on the Chalmers (?) in the flat\r\n[02:34.64] One bedroom apartment\r\n[02:35.86] And dad moved back to the other side of 8 mile on Novarra\r\n[02:38.67] And that\'s when daddy went to California with his CD\r\n[02:41.29] And met Dr. Dre and flew you and Mama out to see me\r\n[02:44.08] But daddy had to work\r\n[02:45.52] You and mama had to leave me\r\n[02:46.92] Then you started seeing daddy on the TV\r\n[02:49.28] And mama didn\'t like it\r\n[02:50.78] And you and Laney were too young to understand it\r\n[02:53.00] Papa was a rolling stone\r\n[02:54.40] Mama developed a habit\r\n[02:55.86] And it all happened too fast for either one of us to grab it\r\n[02:58.53] I\'m just sorry you were there and had to witness it first hand\r\n[03:01.56] Cuz all I ever wanted to do was just make you proud\r\n[03:04.12] Now I\'m sittin\' in this empty house, just reminiscin\'\r\n[03:06.65] Looking at your baby pictures it just trips me out\r\n[03:10.25] To see how much you both have grown\r\n[03:11.48] It\'s almost like your sisters now\r\n[03:12.87] Wow, I guess you pretty much are\r\n[03:15.06] And daddy\'s still here\r\n[03:16.07] Laney I\'m talking to you too\r\n[03:17.66] Daddy\'s still here\r\n[03:18.92] I like the sound of that, yeah\r\n[03:20.41] It\'s got a ring to it, don\'t it?\r\n[03:21.75] Shhh, mama\'s only gone for the moment\r\n[03:23.74] Now hush little baby don\'t you cry\r\n[03:26.48] Everythings gonna be alright\r\n[03:28.75] Stiffen that upper lip up little lady\r\n[03:31.09] I told ya daddy\'s here to hold ya\r\n[03:33.32] Through the night\r\n[03:34.58] I know mommy\'s not here right now and we don\'t know why\r\n[03:38.03] We feel how we feel inside\r\n[03:40.08] It may seem a little crazy pretty baby\r\n[03:42.85] But I promise\r\n[03:44.03] Mama\'s gonna be alright\r\n[03:45.77] And if you ask me to\r\n[03:46.97] Daddy\'s gonna buy you a mocking bird\r\n[03:49.55] I\'ma give you the world\r\n[03:51.46] I\'ma buy a diamond ring for you\r\n[03:53.46] I\'ma sing for you, I\'ll do anything for you to see you smile\r\n[03:57.15] And if the mockingbird don\'t sing and the ring don\'t shine\r\n[04:00.56] I\'ma break that birdy\'s neck\r\n[04:02.72] I\'ll go back to the jewler who sold it to ya\r\n[04:05.69] And make him eat every karat\r\n[04:07.33] Don\'t fuck wit dat\r\n[04:08.75] Haha\r\n[04:09.64] ', 'Encore', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765505945/w1blrp3qagzvdlrrdo17.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765505887/d2lfzj2zgy7qxuexqlrh.mp3'),
(8, 'Tujhe Sochta Hoon', 'https://i.scdn.co/image/ab67616d0000b27371da5e89467bd75d2ed9f1fa', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765506374/tek5wslssimrpghhzxlz.jpg', '00:05:13', '[00:13.06] तुझे सोचता हूँ मैं शामों सुबह\r\n[00:18.06] इससे ज़्यादा तुझे\r\n[00:20.86] और चाहूँ तो क्या\r\n[00:24.42] तेरे ही ख्यालों में डूबा रहा\r\n[00:29.65] इससे ज़्यादा तुझे\r\n[00:32.53] और चाहूँ तो क्या\r\n[00:36.52] बस सारे ग़म में जाना\r\n[00:39.65] संग हूँ तेरे\r\n[00:42.50] हर एक मौसम मैं जाना\r\n[00:45.50] संग हूँ तेरे\r\n[00:48.41] अब इतने इंतहा भी ना ले मेरे\r\n[00:57.52] संग हूँ तेरे\r\n[01:00.81] \r\n[01:03.43] संग हूँ तेरे\r\n[01:09.49] संग हूँ तेरे\r\n[01:17.31] \r\n[01:29.38] मेरी धड़कनों में\r\n[01:32.97] ही तेरी सदा\r\n[01:35.55] इस कदर तू मेरी\r\n[01:38.00] रूह में बस गया\r\n[01:41.33] तेरी यादों से\r\n[01:43.94] कब रहा मैं जुदा\r\n[01:46.96] वक़्त से पूछ ले\r\n[01:49.99] वक़्त मेरा गवाह\r\n[01:53.28] बस सारे ग़म में जाना\r\n[01:56.60] संग हूँ तेरे\r\n[01:59.59] हर एक मौसम में जाना\r\n[02:02.96] संग हूँ तेरे\r\n[02:05.64] अब इतने इंतहा भी ना ले मेरे\r\n[02:12.57] \r\n[02:14.79] संग हूँ तेरे\r\n[02:20.69] संग हूँ तेरे\r\n[02:26.73] संग हूँ तेरे\r\n[02:30.54] \r\n[03:11.10] तू मेरा ठिकाना\r\n[03:13.36] मेरा आशियाना\r\n[03:16.89] ढले शाम जब भी\r\n[03:19.57] मेरे पास आना\r\n[03:22.49] है बाँहों में रहना\r\n[03:25.64] कहीं अब ना जाना\r\n[03:28.70] हूँ महफूज़ इनमे\r\n[03:31.31] बुरा है ज़माना\r\n[03:34.78] \r\n[03:37.19] बस सारे ग़म में जाना\r\n[03:37.61] संग हूँ तेरे\r\n[03:40.12] हर एक मौसम में जाना\r\n[03:43.50] संग हूँ तेरे\r\n[03:46.23] अब इतने इंतहा भी ना ले मेरे\r\n[03:55.05] संग हूँ तेरे\r\n[04:01.10] संग हूँ तेरे\r\n[04:07.02] संग हूँ तेरे...\r\n[04:13.90] ', 'Jannat 2', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765506371/xzheswviuoci0sc2fe5n.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765506362/msngggkuep45aclr3iwm.mp3'),
(10, 'Blinding Lights', 'https://i.scdn.co/image/ab67616d0000b2738863bc11d2aa12b54f5aeb36', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765513396/mbudk2eotn4gvuatyiz9.jpg', '00:03:20', '[00:13.42] Yeah\r\n[00:15.54] \r\n[00:27.03] I\'ve been tryna call\r\n[00:29.65] I\'ve been on my own for long enough\r\n[00:32.90] Maybe you can show me how to love, maybe\r\n[00:38.31] I\'m going through withdrawals\r\n[00:41.04] You don\'t even have to do too much\r\n[00:44.10] You can turn me on with just a touch, baby\r\n[00:49.44] I look around and\r\n[00:50.62] Sin City\'s cold and empty (oh)\r\n[00:53.67] No one\'s around to judge me (oh)\r\n[00:56.11] I can\'t see clearly when you\'re gone\r\n[01:00.73] I said, ooh, I\'m blinded by the lights\r\n[01:06.62] No, I can\'t sleep until I feel your touch\r\n[01:11.63] I said, ooh, I\'m drowning in the night\r\n[01:17.91] Oh, when I\'m like this, you\'re the one I trust\r\n[01:22.44] (Hey, hey, hey)\r\n[01:25.70] \r\n[01:34.18] I\'m running out of time\r\n[01:37.20] \'Cause I can see the sun light up the sky\r\n[01:39.88] So I hit the road in overdrive, baby, oh\r\n[01:46.68] The city\'s cold and empty (oh)\r\n[01:49.65] No one\'s around to judge me (oh)\r\n[01:52.34] I can\'t see clearly when you\'re gone\r\n[01:56.89] I said, ooh, I\'m blinded by the lights\r\n[02:03.04] No, I can\'t sleep until I feel your touch\r\n[02:08.31] I said, ooh, I\'m drowning in the night\r\n[02:14.09] Oh, when I\'m like this, you\'re the one I trust\r\n[02:19.46] I\'m just walking by to let you know (by to let you know)\r\n[02:22.12] I can never say it on the phone (say it on the phone)\r\n[02:25.52] Will never let you go this time (ooh)\r\n[02:30.44] I said, ooh, I\'m blinded by the lights\r\n[02:36.58] No, I can\'t sleep until I feel your touch\r\n[02:40.84] (Hey, hey, hey)\r\n[02:44.51] \r\n[03:04.38] I said, ooh, I\'m blinded by the lights\r\n[03:10.04] No, I can\'t sleep until I feel your touch\r\n[03:13.71] ', 'Radio 538 10\'s Dossier', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765513738/e956l602sdq6gf7kaivj.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765513731/wo5e07lnos4fjxtqvst6.mp3'),
(11, 'Mockingbird', 'https://i.scdn.co/image/ab67616d0000b2731bec21e57fff76db49e15a70', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765507768/fficytdqe8iwvrd9oxtu.jpg', '00:04:11', '[00:02.39] Yeah\r\n[00:04.46] I know sometimes things may not always make sense to you right now\r\n[00:09.98] But hey, what daddy always tell you?\r\n[00:13.67] Straighten up little soldier\r\n[00:15.82] Stiffen up that upper lip\r\n[00:18.32] What you crying about?\r\n[00:20.57] You got me\r\n[00:22.06] Hailie, I know you miss your mom\r\n[00:23.84] And I know you miss your dad when I\'m gone\r\n[00:25.76] But I\'m trying to give you the life that I never had\r\n[00:27.81] I can see you\'re sad\r\n[00:29.16] Even when you smile\r\n[00:30.28] Even when you laugh\r\n[00:31.26] I can see it in your eyes\r\n[00:32.65] Deep inside, you wanna cry\r\n[00:33.93] Cuz you\'re scared\r\n[00:34.84] I ain\'t there?\r\n[00:35.40] Daddy\'s with you in your prayers\r\n[00:36.68] No more crying\r\n[00:37.54] Wipe them tears\r\n[00:38.09] Daddy\'s here\r\n[00:38.83] No more nightmares\r\n[00:39.58] We gonna pull together through it\r\n[00:40.81] We gon\' do it\r\n[00:41.86] Laney, uncle\'s crazy ain\'t he?\r\n[00:43.25] Yeah but he loves you girl and you better know it\r\n[00:45.16] We\'re all we got in this world\r\n[00:46.72] When it spins\r\n[00:47.35] When it swirls\r\n[00:48.03] When it whirls\r\n[00:48.72] When it twirls\r\n[00:49.44] Two little beautiful girls\r\n[00:50.90] Looking puzzled, in a daze\r\n[00:52.30] I know it\'s confusing you\r\n[00:53.71] Daddy\'s always on the move\r\n[00:55.12] Mama\'s always on the news\r\n[00:56.53] I try to keep you sheltered from it\r\n[00:58.29] But somehow it seems, the harder that I try to do that\r\n[01:00.48] The more it backfires on me\r\n[01:02.07] All the things, growing up\r\n[01:03.45] As daddy that he had to see\r\n[01:04.70] Daddy don\'t want you to see\r\n[01:06.44] But you see just as much as he did\r\n[01:07.54] That we did not plan it to be this way\r\n[01:09.26] You\'re mother and me\r\n[01:10.49] But things have got so bad between us\r\n[01:11.98] I don\'t see us ever being\r\n[01:13.20] Together ever again\r\n[01:14.69] Like we used to be when was teenagers\r\n[01:16.56] But then of course\r\n[01:17.64] Everything always happens for a reason\r\n[01:19.60] I guess it was never meant to be\r\n[01:21.29] But it\'s just something\r\n[01:22.13] We have no control over\r\n[01:23.30] And that\'s what destiny is\r\n[01:25.06] But no more worries\r\n[01:26.39] Rest your head and go to sleep\r\n[01:27.47] Maybe one day we\'ll wake up\r\n[01:28.89] And this will all just be a dream\r\n[01:30.34] Now hush little baby don\'t you cry\r\n[01:32.90] Everythings gonna be alright\r\n[01:35.11] Stiffen that upper lip up little lady\r\n[01:37.49] I told ya, daddy\'s here to hold ya\r\n[01:39.81] Through the night\r\n[01:40.92] I know mommy\'s not here right now and we don\'t know why\r\n[01:44.40] We feel how we feel inside\r\n[01:46.53] It may seem a little crazy, pretty baby\r\n[01:49.35] But I promise, Mama\'s gonna be alright\r\n[01:52.20] It\'s funny\r\n[01:53.32] I remember back one year when daddy had no money\r\n[01:55.92] Mommy wrapped the Christamas presents up\r\n[01:57.85] And stuck them under the tree\r\n[01:58.72] And said some of them were from me\r\n[02:00.36] Cos daddy couldn\'t buy \'em\r\n[02:01.60] I\'ll never forget that Christmas\r\n[02:03.09] I sat up the whole night cryin\'\r\n[02:04.45] Cuz daddy felt like a bum\r\n[02:05.98] See daddy had a job\r\n[02:07.19] But his job was to keep the food on the table for you and mom\r\n[02:09.55] And at the time every house that we lived in\r\n[02:12.64] Either kept getting broken into and robbed or shot up on the block\r\n[02:15.64] And your mom, was saving money\r\n[02:17.54] For you in a jar trying to start a piggy bank for you\r\n[02:20.15] So you can go to college\r\n[02:21.38] Almost had a thousand dollars\r\n[02:23.04] Till someone broke in and stole it\r\n[02:24.71] And I know it hurt so bad it broke your mama\'s heart\r\n[02:27.37] And it seemed like everything was just starting to fall apart\r\n[02:30.02] Mom and dads was arguing a lot\r\n[02:32.42] So mama moved back on the Chalmers (?) in the flat\r\n[02:34.64] One bedroom apartment\r\n[02:35.86] And dad moved back to the other side of 8 mile on Novarra\r\n[02:38.67] And that\'s when daddy went to California with his CD\r\n[02:41.29] And met Dr. Dre and flew you and Mama out to see me\r\n[02:44.08] But daddy had to work\r\n[02:45.52] You and mama had to leave me\r\n[02:46.92] Then you started seeing daddy on the TV\r\n[02:49.28] And mama didn\'t like it\r\n[02:50.78] And you and Laney were too young to understand it\r\n[02:53.00] Papa was a rolling stone\r\n[02:54.40] Mama developed a habit\r\n[02:55.86] And it all happened too fast for either one of us to grab it\r\n[02:58.53] I\'m just sorry you were there and had to witness it first hand\r\n[03:01.56] Cuz all I ever wanted to do was just make you proud\r\n[03:04.12] Now I\'m sittin\' in this empty house, just reminiscin\'\r\n[03:06.65] Looking at your baby pictures it just trips me out\r\n[03:10.25] To see how much you both have grown\r\n[03:11.48] It\'s almost like your sisters now\r\n[03:12.87] Wow, I guess you pretty much are\r\n[03:15.06] And daddy\'s still here\r\n[03:16.07] Laney I\'m talking to you too\r\n[03:17.66] Daddy\'s still here\r\n[03:18.92] I like the sound of that, yeah\r\n[03:20.41] It\'s got a ring to it, don\'t it?\r\n[03:21.75] Shhh, mama\'s only gone for the moment\r\n[03:23.74] Now hush little baby don\'t you cry\r\n[03:26.48] Everythings gonna be alright\r\n[03:28.75] Stiffen that upper lip up little lady\r\n[03:31.09] I told ya daddy\'s here to hold ya\r\n[03:33.32] Through the night\r\n[03:34.58] I know mommy\'s not here right now and we don\'t know why\r\n[03:38.03] We feel how we feel inside\r\n[03:40.08] It may seem a little crazy pretty baby\r\n[03:42.85] But I promise\r\n[03:44.03] Mama\'s gonna be alright\r\n[03:45.77] And if you ask me to\r\n[03:46.97] Daddy\'s gonna buy you a mocking bird\r\n[03:49.55] I\'ma give you the world\r\n[03:51.46] I\'ma buy a diamond ring for you\r\n[03:53.46] I\'ma sing for you, I\'ll do anything for you to see you smile\r\n[03:57.15] And if the mockingbird don\'t sing and the ring don\'t shine\r\n[04:00.56] I\'ma break that birdy\'s neck\r\n[04:02.72] I\'ll go back to the jewler who sold it to ya\r\n[04:05.69] And make him eat every karat\r\n[04:07.33] Don\'t fuck wit dat\r\n[04:08.75] Haha\r\n[04:09.64] ', 'Encore', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765507765/wfr2i0f5eqhbdpbmqf0k.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765507749/uflzbqtktqd9loarefm9.mp3'),
(12, 'Tu hi haqeeqat ', 'https://i.scdn.co/image/ab67616d0000b273bcb8ec035de7b2dda0f74660', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765509163/uknolznyujpinsngbtlt.jpg', '00:05:02', '[00:42.92] Tu Hi Haqeeaqat\r\n[00:44.82] Khawwb Tu\r\n[00:46.58] Dariya Tu Hi\r\n[00:49.07] Pyaas Tu\r\n[00:51.14] Tu Hi Dil Ke Beqarari\r\n[00:55.16] Tu Sukun Tu Sukun\r\n[00:59.26] Jau Mein Ab Jab Jis Jagah\r\n[01:03.34] Pau Mein Tujhko\r\n[01:05.51] Us Jagah\r\n[01:07.86] Saath Ho Ke Na Hoon\r\n[01:11.21] Tu Hai Roobaru Roobaru\r\n[01:15.87] Tu Humsafar\r\n[01:17.53] Tu Humkadam\r\n[01:19.38] Tu Hamnava Mera\r\n[01:23.91] Tu Humsafar\r\n[01:25.65] Tu Humkadam\r\n[01:28.02] Tu Hamnava Mera\r\n[01:49.71] Aa Tujhe In Bahon Mein Bharke\r\n[01:52.96] Aur Bhi Kar Loon Main Kareeb\r\n[01:56.87] Tu Juda Ho To Lage Hai\r\n[02:01.25] Aata Jata Har Pal Ajeeb\r\n[02:05.52] Is Jahan Mein Hai Aur Na Hoga\r\n[02:08.78] Mujhsa Koi Bhi Kushnaseeb\r\n[02:14.13] Tune Mujhko Dil Diya Hai\r\n[02:18.49] Mein Hoon Tere Sabse Kareeb\r\n[02:22.54] Mein Hi To Tere Dil Main Hoon\r\n[02:26.52] Mein Hi To Saason Mein Basu\r\n[02:30.67] Tere Dil Ki Dhadkano Mein\r\n[02:34.99] Mein Hi Hoon Mein Hi Hoon\r\n[02:38.40] Tu Humsafar\r\n[02:40.92] Tu Humkadam\r\n[02:42.63] Tu Hamnava Mera\r\n[02:47.12] Tu Humsafar\r\n[02:48.80] Tu Humkadam\r\n[02:50.99] Tu Hamnava Mera\r\n[03:19.96] Kab Bhala Ab Yeh Waqt Guzre\r\n[03:24.06] Kuch Pata Chalta Hi Nahi\r\n[03:28.44] Jab Se Mujhko Tu Mila Hai\r\n[03:32.61] Hosh Kuch Bhi Apna Nahi\r\n[03:36.68] Uff Yeh Teri Palkein Ghani Si\r\n[03:40.72] Chav In Ki Hai Dilnasheen\r\n[03:44.83] Abb Kise Dar Dhoop Ka Hai\r\n[03:49.04] Kyun Ki Hai Yeh Mujhpe Beechi\r\n[03:53.05] Tere Bina Na Saans Loo\r\n[03:57.02] Tere Bina Na Main Jeu\r\n[04:01.36] Tere Bina Na Ek Pal Bhi\r\n[04:05.59] Reh Saku Re Saku\r\n[04:09.73] Tu Hi Haqeeaqat\r\n[04:11.86] Khawwb Tu\r\n[04:13.56] Dariya Tu Hi\r\n[04:15.69] Pyaas Tu\r\n[04:18.03] Tu Hi Dil Ke Beqarari\r\n[04:22.07] Tu Sukun Tu Sukun\r\n[04:25.77] Tu Humsafar\r\n[04:27.95] Tu Humkadam\r\n[04:30.36] Tu Hamnava Mera\r\n[04:34.20] Tu Humsafar\r\n[04:36.67] Tu Humkadam\r\n[04:38.63] ', 'Pritam', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765509161/l3plcqu8p6euf8y01tvv.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765509139/dmehxwa9cwclotzd5i52.mp3'),
(13, 'The Real Slim Shady', 'https://i.scdn.co/image/ab67616d0000b273dbb3dd82da45b7d7f31b1b42', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765512558/darrpllyz9tt7eloar8j.jpg', '00:04:44', '[00:02.62] May I have your attention, please?\r\n[00:05.59] May I have your attention, please?\r\n[00:08.53] Will the real Slim Shady please stand up?\r\n[00:11.95] I repeat, will the real Slim Shady please stand up?\r\n[00:16.45] We\'re gonna have a problem here?\r\n[00:18.08] Y\'all act like you never seen a white person before\r\n[00:20.23] Jaws all on the floor like Pam, like Tommy just burst in the door\r\n[00:23.12] And started whoopin\' her ass worse than before\r\n[00:25.04] They first were divorced, throwin\' her over furniture (ah!)\r\n[00:27.10] It\'s the return of the-, oh, wait, no way, you\'re kidding\r\n[00:29.88] He didn\'t just say what I think he did, did he?\r\n[00:32.17] And Dr. Dre said-, nothing you idiots\r\n[00:34.62] Dr. Dre\'s dead, he\'s locked in my basement (haha)\r\n[00:36.73] Feminist women love Eminem\r\n[00:38.23] Chicka-chicka-chicka Slim Shady, I\'m sick of him\r\n[00:40.51] Look at him, walkin\' around, grabbin\' his you-know-what\r\n[00:43.39] Flippin\' the you-know-who, yeah, but he\'s so cute though\r\n[00:45.91] Yeah, I probably got a couple of screws up in my head loose\r\n[00:48.03] But no worse, than what\'s goin\' on in your parents bedrooms\r\n[00:50.37] Sometimes I wanna get on TV and just let loose, but can\'t\r\n[00:53.35] But it\'s cool for Tom Green to hump a dead moose\r\n[00:55.32] My bum is on your lips, my bum is on your lips\r\n[00:57.22] And if I\'m lucky you might just give it a little kiss\r\n[00:59.58] And that\'s the message that we deliver to little kids\r\n[01:01.79] And expect them not to know what a woman\'s clitoris is\r\n[01:04.00] Of course, they\'re gonna know what intercourse is\r\n[01:06.03] By the time they hit fourth grade\r\n[01:07.26] They got the Discovery Channel, don\'t they?\r\n[01:08.76] We ain\'t nothin\' but mammals\r\n[01:10.49] Well, some of us are cannibals\r\n[01:11.72] Who cut other people open like cantaloupes\r\n[01:13.48] But if we can hump dead animals and antelopes\r\n[01:15.58] Then there\'s no reason that a man and another man can\'t elope\r\n[01:18.06] But if you feel like I feel, I got the antidote\r\n[01:20.43] Women wave your pantyhose, sing the chorus and it goes...\r\n[01:22.73] I\'m Slim Shady, yes, I\'m the real Shady\r\n[01:24.74] All you other Slim Shadys, are just imitating\r\n[01:26.97] So won\'t the real Slim Shady, please stand up?\r\n[01:29.65] Please stand up, please stand up\r\n[01:31.89] \'Cause I\'m Slim Shady, yes, I\'m the real Shady\r\n[01:33.88] All you other Slim Shadys, are just imitating\r\n[01:36.03] So won\'t the real Slim Shady, please stand up?\r\n[01:38.56] Please stand up, please stand up\r\n[01:40.80] Will Smith don\'t gotta cuss in his raps to sell records\r\n[01:42.97] Well I do, so fuck him and fuck you too\r\n[01:45.58] You think I give a damn about a Grammy\r\n[01:47.21] Half of you critics can\'t even stomach me\r\n[01:49.17] Let alone stand me\r\n[01:49.90] But Slim, what if you win, wouldn\'t it be weird?\r\n[01:52.15] Why, so you guys can just lie to get me here\r\n[01:54.34] So you can sit me here next to Britney Spears?\r\n[01:56.65] Shit, Christina Aguilera better switch me chairs\r\n[01:59.17] So I can sit next to Carson Daly and Fred Durst\r\n[02:01.37] And hear \'em argue over who she gave head to first\r\n[02:03.54] Little bitch put me on blast on MTV\r\n[02:05.87] Yeah, he\'s cute, but I think he\'s married to Kim, hehe!\r\n[02:08.27] I should download her audio on mp3\r\n[02:10.38] And show the whole world, how you gave Eminem VD (ahh!)\r\n[02:13.03] I\'m sick of you little girl and boy groups, all you do is annoy me\r\n[02:16.13] So I have been sent here to destroy you\r\n[02:17.78] And there\'s a million of us just like me\r\n[02:19.76] Who cuss like me, who just don\'t give a fuck like me\r\n[02:21.72] Who dress like me, walk talk and act like me\r\n[02:24.27] And just might be, the next best thing, but not quite me\r\n[02:26.82] \'Cause I\'m Slim Shady, yes, I\'m the real Shady\r\n[02:28.97] All you other Slim Shadys, are just imitating\r\n[02:31.20] So won\'t the real Slim Shady, please stand up?\r\n[02:33.57] Please stand up, please stand up\r\n[02:35.83] \'Cause I\'m Slim Shady, yes, I\'m the real Shady\r\n[02:38.16] All you other Slim Shadys, are just imitating\r\n[02:40.47] So won\'t the real Slim Shady, please stand up?\r\n[02:42.71] Please stand up, please stand up\r\n[02:44.99] I\'m like a head trip to listen to, \'cause I\'m only givin\' you\r\n[02:47.53] Things you joke about with your friends inside your livin\' room\r\n[02:49.82] The only difference is I got the balls to say it in front of y\'all\r\n[02:52.37] And I don\'t gotta be false or sugar-coat it at all\r\n[02:54.72] I just get on the mic and spit it\r\n[02:56.73] And whether you like to admit it, I just shit it\r\n[02:58.84] Better than ninety percent of you rappers out can\r\n[03:00.93] Then you wonder, how can kids eat up these albums like Valiums?\r\n[03:03.49] It\'s funny \'cause at the rate I\'m goin\' when I\'m thirty\r\n[03:06.09] I\'ll be the only person in the nursin\' home flirting\r\n[03:08.19] Pinchin\' nurses asses while I\'m jackin\' off with Jergens\r\n[03:10.46] And I\'m jerkin\' but this whole bag of Viagra isn\'t workin\'\r\n[03:12.76] In every single person there\'s a Slim Shady lurkin\'\r\n[03:15.03] He could be workin\' at Burger King, spittin\' on your onion rings\r\n[03:17.44] Or in the parkin\' lot circling, screamin\' I don\'t give a fuck\r\n[03:20.44] With his windows down and his system up\r\n[03:22.02] So will the real Shady, please stand up?\r\n[03:24.17] And put one of those fingers, on each hand up\r\n[03:26.48] And be proud to be outta your mind and outta control\r\n[03:28.80] And one more time loud as you can, how does it go?\r\n[03:31.24] I\'m Slim Shady, yes, I\'m the real Shady\r\n[03:33.40] All you other Slim Shadys, are just imitating\r\n[03:35.57] So won\'t the real Slim Shady, please stand up?\r\n[03:38.16] Please stand up, please stand up\r\n[03:40.27] \'Cause I\'m Slim Shady, yes, I\'m the real Shady\r\n[03:42.67] All you other Slim Shadys, are just imitating\r\n[03:45.03] So won\'t the real Slim Shady, please stand up?\r\n[03:47.20] Please stand up, please stand up\r\n[03:49.53] \'Cause I\'m Slim Shady, yes, I\'m the real Shady\r\n[03:51.80] All you other Slim Shadys, are just imitating\r\n[03:53.92] So won\'t the real Slim Shady, please stand up?\r\n[03:56.33] Please stand up, please stand up\r\n[03:58.64] \'Cause I\'m Slim Shady, yes, I\'m the real Shady\r\n[04:00.94] All you other Slim Shadys, are just imitating\r\n[04:03.21] So won\'t the real Slim Shady, please stand up?\r\n[04:05.56] Please stand up, please stand up\r\n[04:08.38] Haha, guess there\'s a Slim Shady in all of us\r\n[04:13.46] Fuck it, let\'s all stand up\r\n[04:16.65] ', 'The Marshall Mathers LP (U.K. Only)', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765512555/owoe3tmwrecoep3nwukk.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765512535/ugcgzt60d5ahsidl6xxo.mp3'),
(14, 'Believer', 'https://i.scdn.co/image/ab67616d0000b2735675e83f707f1d7271e5cf8a', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1756536579/nzxy2mfv50wy3i2cgvsc.jpg', '00:02:04', '[00:07.44] First Things First, I\'ma Say All The Words Inside My Head\r\n[00:11.65] I\'m Fired Up, And Tired Of The Way That Things Have Been, Oh-Ooh\r\n[00:17.71] The Way That Things Have Been, Oh-Ooh\r\n[00:22.80] Second Thing Second, Don\'t You Tell Me What You Think That I Can Be\r\n[00:27.08] I\'m The One At The Sail, I\'m The Master Of My Sea, Oh-Ooh\r\n[00:33.26] The Master Of My Sea, Oh-Ooh\r\n[00:37.80] I Was Broken From A Young Age\r\n[00:39.70] Taking My Sulking To The Masses\r\n[00:41.65] Writing My Poems For The Few\r\n[00:43.38] That Look At Me, Took To Me, Shook To Me, Feelin\' Me\r\n[00:45.45] Singing From Heartache From The Pain\r\n[00:47.32] Taking My Message From The Veins\r\n[00:49.32] Speaking My Lesson From The Brain\r\n[00:51.08] Seeing The Beauty Through The\r\n[00:54.67] Pain!\r\n[00:55.25] You Made Me A, You Made Me A Believer, Believer\r\n[01:02.27] Pain!\r\n[01:03.06] You Break Me Down, And Build Me Up, Believer, Believer\r\n[01:09.15] Pain!\r\n[01:10.61] Oh, Let The Bullets Fly, Oh, Let Them Rain\r\n[01:14.46] My Life, My Love, My Drive It Came From\r\n[01:17.52] Pain!\r\n[01:18.37] You Made Me A, You Made Me A Believer, Believer\r\n[01:24.14] Third Things Third, Send A Prayer To The Ones Up Above\r\n[01:28.58] All The Hate That You\'ve Heard Has Turned Your Spirit To A Dove, Oh-Ooh\r\n[01:34.51] Your Spirit Up Above, Oh-Ooh\r\n[01:39.34] I Was Choking In The Crowd\r\n[01:41.13] Building My Rain Up In The Cloud\r\n[01:42.90] Falling Like Ashes To The Ground\r\n[01:44.86] Hoping My Feelings, They Would Drown\r\n[01:46.95] But They Never Did, Ever Lived, Ebbin\' And Flowin\'\r\n[01:49.35] Inhibited, Limited, \'Til It Broke Open, And It Rained Down\r\n[01:52.41] It Rained Down Like\r\n[01:55.87] Pain!\r\n[01:56.82] You Made Me A, You Made Me A Believer, Believer\r\n[02:03.60] Pain!\r\n[02:04.63] You Break Me Down, And Build Me Up, Believer, Believer\r\n[02:10.63] Pain!\r\n[02:12.17] Oh, Let The Bullets Fly, Oh, Let Them Rain\r\n[02:15.90] My Life, My Love, My Drive It Came From\r\n[02:19.10] Pain!\r\n[02:20.02] You Made Me A, You Made Me A Believer, Believer\r\n[02:25.52] Last Things Last, By The Grace Of The Fire And The Flames\r\n[02:30.05] You\'re The Face Of The Future, The Blood In My Veins, Oh-Ooh\r\n[02:35.98] The Blood In My Veins, Oh-Ooh\r\n[02:40.90] But They Never Did, Ever Lived, Ebbin\' And Flowin\'\r\n[02:43.10] Inhibited, Limited, \'Til It Broke Open, And It Rained Down\r\n[02:46.16] It Rained Down Like\r\n[02:49.95] Pain!\r\n[02:50.31] You Made Me A, You Made Me A Believer, Believer\r\n[02:57.58] Pain!\r\n[02:58.42] You Break Me Down, And Build Me Up, Believer, Believer\r\n[03:04.33] Pain!\r\n[03:06.04] Oh, Let The Bullets Fly, Oh, Let Them Rain\r\n[03:09.64] My Life, My Love, My Drive It Came From\r\n[03:12.92] Pain!\r\n[03:13.46] You Made Me A, You Made Me A Believer, Believer\r\n[03:18.97]', 'Evolve', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1756234424/l7olidxp07hr3gvrcpmt.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1756234415/tqvvwwdthkqsws9v9gcw.mp3'),
(18, 'Tum To Thehre Pardesi', 'https://i.scdn.co/image/ab67616d0000b273e7bc15738713afbb4b18657e', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1756536579/nzxy2mfv50wy3i2cgvsc.jpg', '00:02:29', '[00:36.10] तुम तो ठहरे परदेसी\r\n[00:40.52] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[00:49.21] (तुम तो ठहरे परदेसी, साथ क्या निभाओगे)\r\n[00:57.92] (तुम तो ठहरे परदेसी, साथ क्या निभाओगे)\r\n[01:06.79] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[01:15.47] (तुम तो ठहरे परदेसी, साथ क्या निभाओगे)\r\n[01:24.22] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[01:28.53] सुबह पहली, सुबह पहली...\r\n[01:33.15] सुबह पहली गाड़ी से घर को लौट जाओगे\r\n[01:42.01] (सुबह पहली गाड़ी से घर को लौट जाओगे)\r\n[01:50.61] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[01:59.13] \r\n[02:37.34] जब तुम्हें अकेले में मेरी याद आएगी\r\n[02:45.91] (जब तुम्हें अकेले में मेरी याद आएगी)\r\n[02:54.94] खिंचे-खिंचे हुए रहते हो, क्यूँ?\r\n[02:58.62] खिंचे-खिंचे हुए रहते हो, ध्यान किसका है?\r\n[03:02.77] ज़रा बताओ तो ये इम्तिहान किसका है?\r\n[03:06.91] हमें भुला दो, मगर ये तो याद ही होगा\r\n[03:10.54] हमें भुला दो, मगर ये तो याद ही होगा\r\n[03:14.43] नई सड़क पे पुराना मकान किसका है\r\n[03:18.13] (जब तुम्हें अकेले में मेरी याद आएगी)\r\n[03:26.91] जब तुम्हें अकेले में मेरी याद आएगी\r\n[03:31.25] आँसुओं की, आँसुओं की...\r\n[03:35.68] आँसुओं की बारिश में ए तुम भी भीग जाओगे\r\n[03:44.41] (आँसुओं की बारिश में तुम भी भीग जाओगे)\r\n[03:52.99] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[04:01.42] \r\n[04:22.99] ग़म की धूप में दिल की हसरतें ना जल जाएँ\r\n[04:31.56] (ग़म की धूप में दिल की हसरतें ना जल जाएँ)\r\n[04:40.34] तुझ को, ए तुझ को देखेंगे सितारे तो ज़िया माँगेंगे\r\n[04:46.30] तुझ को देखेंगे सितारे तो ज़िया माँगेंगे\r\n[04:50.44] और प्यासे तेरी ज़ुल्फ़ों से घटा माँगेंगे\r\n[04:54.91] अपने काँधे से दुपट्टा ना सरकने देना\r\n[04:58.20] वरना बूढ़े भी जवानी की दुआ माँगेंगे, ईमान से\r\n[05:03.45] (ग़म की धूप में दिल की हसरतें ना जल जाएँ)\r\n[05:12.03] ग़म की धूप में दिल की हसरतें ना जल जाएँ\r\n[05:16.43] गेसुओं के, गेसुओं के...\r\n[05:20.85] गेसुओं के साए में कब हमें सुलाओगे?\r\n[05:29.49] (गेसुओं के साए में कब हमें सुलाओगे?)\r\n[05:37.94] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[05:46.49] \r\n[06:23.78] मुझको क़त्ल कर डालो शौक़ से, मगर सोचो\r\n[06:32.35] (मुझको क़त्ल कर डालो शौक़ से, मगर सोचो)\r\n[06:41.35] इस शहर-ए-नामुराद की इज़्ज़त करेगा कौन?\r\n[06:47.40] अरे, हम भी चले गए तो मोहब्बत करेगा कौन?\r\n[06:51.70] इस घर की देख-भाल को वीरानियाँ तो हों\r\n[06:55.76] इस घर की देख-भाल को वीरानियाँ तो हों\r\n[06:59.95] जाले हटा दिए तो हिफ़ाज़त करेगा कौन?\r\n[07:03.91] (मुझको क़त्ल कर डालो शौक़ से, मगर सोचो)\r\n[07:12.34] मुझको क़त्ल कर डालो शौक़ से, मगर सोचो\r\n[07:16.97] मेरे बाद, मेरे बाद...\r\n[07:21.06] मेरे बाद तुम किस पर ये बिजलियाँ गिराओगे?\r\n[07:29.65] (मेरे बाद तुम किस पर बिजलियाँ गिराओगे?)\r\n[07:38.01] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[07:46.56] \r\n[08:07.86] यूँ तो ज़िंदगी अपनी मय-कदे में गुज़री है\r\n[08:16.30] (यूँ तो ज़िंदगी अपनी मय-कदे में गुज़री है)\r\n[08:24.10] अश्कों में हुस्न-ओ-रंग समोता रहा हूँ मैं\r\n[08:29.67] अश्कों में हुस्न-ओ-रंग समोता रहा हूँ मैं\r\n[08:33.89] आँचल किसी का थाम के रोता रहा हूँ मैं\r\n[08:37.78] निखरा है जा के अब कहीं चेहरा शऊर का\r\n[08:41.52] निखरा है जा के अब कहीं चेहरा शऊर का\r\n[08:45.32] बरसों इसे शराब से धोता रहा हूँ मैं\r\n[08:50.38] (यूँ तो ज़िंदगी अपनी मय-कदे में गुज़री है)\r\n[08:59.27] बहकी हुई बहार ने पीना सिखा दिया\r\n[09:03.94] बदमस्त बर्ग-ओ-बार ने पीना सिखा दिया\r\n[09:08.57] पीता हूँ इस ग़रज़ से कि जीना है चार दिन\r\n[09:12.32] पीता हूँ इस ग़रज़ से कि जीना है चार दिन\r\n[09:16.36] मरने के इंतज़ार ने पीना सीखा दिया\r\n[09:20.18] (यूँ तो ज़िंदगी अपनी मय-कदे में गुज़री है)\r\n[09:28.76] यूँ तो ज़िंदगी अपनी मय-कदे में गुज़री है\r\n[09:33.15] इन नशीली, इन नशीली...\r\n[09:37.63] इन नशीली आँखों से अरे, कब हमें पिलाओगे?\r\n[09:46.44] (इन नशीली आँखों से कब हमें पिलाओगे?)\r\n[09:54.80] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[10:03.27] \r\n[10:40.68] क्या करोगे तुम आख़िर कब्र पर मेरी आकर?\r\n[10:49.25] (क्या करोगे तुम आख़िर कब्र पर मेरी आकर?)\r\n[10:58.30] क्या करोगे तुम आख़िर कब्र पर मेरी आकर, क्योंकि\r\n[11:04.03] जब तुम से इत्तफ़ाक़न...\r\n[11:08.28] जब तुम से इत्तफ़ाक़न मेरी नज़र मिली थी\r\n[11:12.52] अब याद आ रहा है, शायद वो जनवरी थी\r\n[11:16.86] तुम यूँ मिली दुबारा फिर माह-ए-फ़रवरी में\r\n[11:21.06] जैसे कि हमसफ़र हो तुम राह-ए-ज़िंदगी में\r\n[11:25.36] कितना हसीं ज़माना आया था मार्च लेकर\r\n[11:29.53] राह-ए-वफ़ा पे थी तुम वादों की torch लेकर\r\n[11:33.75] बाँधा जो अहद-ए-उल्फ़त, अप्रैल चल रहा था\r\n[11:37.94] दुनिया बदल रही थी, मौसम बदल रहा था\r\n[11:42.22] लेकिन मई जब आई, जलने लगा ज़माना\r\n[11:46.34] हर शख़्स की ज़बाँ पर था बस यही फ़साना\r\n[11:50.61] दुनिया के डर से तुमने बदली थी जब निगाहें\r\n[11:54.71] था जून का महीना, लब पे थी गर्म आहें\r\n[11:58.91] जुलाई में जो तुमने की बातचीत कुछ कम\r\n[12:03.23] थे आसमाँ पे बादल और मेरी आँखें पुर-नम\r\n[12:07.54] माह-ए-अगस्त में जब बरसात हो रही थी\r\n[12:11.59] बस आँसुओं की बारिश दिन-रात हो रही थी\r\n[12:15.88] कुछ याद आ रहा है, वो माह था सितंबर\r\n[12:20.18] भेजा था तुमने मुझको तर्क़-ए-वफ़ा का letter\r\n[12:24.36] तुम ग़ैर हो रही थी, अक्टूबर आ गया था\r\n[12:28.51] दुनिया बदल चुकी थी, मौसम बदल चुका था\r\n[12:32.84] जब आ गया नवंबर, ऐसी भी रात आई\r\n[12:36.95] मुझसे तुम्हें छुड़ाने सजकर बारात आई\r\n[12:41.26] बेक़ैफ़ था दिसंबर, जज़्बात मर चुके थे\r\n[12:45.39] मौसम था सर्द उसमें, अरमाँ बिखर चुके थे\r\n[12:49.58] लेकिन ये क्या बताऊँ, अब हाल दूसरा है\r\n[12:53.91] (लेकिन ये क्या बताऊँ, अब हाल दूसरा है)\r\n[12:58.19] (लेकिन ये क्या बताऊँ, अब हाल दूसरा है)\r\n[13:02.36] लेकिन ये क्या बताऊँ, अब हाल दूसरा है\r\n[13:07.34] अरे, वो साल दूसरा था, ये साल दूसरा है\r\n[13:11.89] (वो साल दूसरा था, ये साल दूसरा है)\r\n[13:16.09] (वो साल दूसरा था, ये साल दूसरा है)\r\n[13:20.53] क्या करोगे तुम आख़िर...\r\n[13:22.81] क्या करोगे तुम आख़िर कब्र पर मेरी आकर?\r\n[13:27.07] थोड़ी देर, थोड़ी देर...\r\n[13:31.49] थोड़ी देर रो लोगे और भूल जाओगे\r\n[13:40.18] (थोड़ी देर रो लोगे और भूल जाओगे)\r\n[13:48.59] (थोड़ी देर रो लोगे और भूल जाओगे)\r\n[13:57.04] तुम तो ठहरे परदेसी, साथ क्या निभाओगे\r\n[14:05.31] (तुम तो ठहरे परदेसी, साथ क्या निभाओगे)\r\n[14:13.62] सुबह पहली गाड़ी से घर को लौट जाओगे\r\n[14:21.89] (सुबह पहली गाड़ी से घर को लौट जाओगे)\r\n[14:30.21] (सुबह पहली गाड़ी से घर को लौट जाओगे)\r\n[14:38.83] ', 'Tum To Thehre Pardesi', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1756517202/rd8r8jvsctej6wta4eqy.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1756517176/gq7i9c2tnwk2wmtmmf59.mp3'),
(19, 'Kaho Na Kaho', 'https://i.scdn.co/image/ab67616d0000b27354aca5d6dc5534f7ffb1c1eb', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1761628861/hq720_x3kyub.jpg', '00:03:12', '[00:34.09] कहो ना कहो, ये आँखें बोलती है\n[00:39.22] ओ सनम ओ सनम, ओ मेरे सनम\n[00:45.14] मोहब्बत के सफर में ये सहारा है\n[00:50.71] वफा के साहिलों का ये किनारा है\n[00:56.21] कहो ना कहो, ये आँखें बोलती है\n[01:01.31] ओ सनम ओ सनम, ओ मेरे सनम\n[01:07.30] मोहब्बत के सफर में ये सहारा है\n[01:12.71] वफा के साहिलों का ये किनारा है\n[01:19.10] बादलों से ऊँची उड़ान उनकी\n[01:21.81] सब से अलग पहचान उनकी\n[01:24.60] उनसे है प्यार की कहानी मंसूर \n[01:27.20] आती जाती साँसों की रवानी मंसूर\n[01:30.08] बादलों से ऊँची उड़ान उनकी\n[01:32.70] सब से अलग पहचान उनकी\n[01:35.63] उनसे है प्यार की कहानी मंसूर \n[01:38.32] आती जाती साँसों की रवानी मंसूर\n[01:43.16] तमल्ली म\'आ वाला हंटा \'बैदी\'सं\'अम्\n[01:48.38] निती अल्कि हवा तमल्ली म\'आ\n[01:54.25] तमल्ली ब\'आली ऊफ्फल दी-वला बंसा\'\n[01:59.54] तमल्ली वा हिशमिल्लाह-हंटा बनु वेया\n[02:05.13] कहो ना कहो, ये आँखें बोलती है\n[02:10.47] ओ सनम ओ सनम, ओ मेरे सनम\n[02:16.17] मोहब्बत के सफर में तू हमारा है\n[02:21.71] अंधेरे रास्तों का तू सितारा है\n[02:28.08] तमल्ली हबीबी महत-इ-दिल्लै\n[02:30.83] इ\'लय्या \'यनेया दिंदा \'यले\n[02:33.67] विल्ले-हा वलेला कुल्ली कु\n[02:36.36] याकुनिया-हबीबी महत-इ-दिल्लै\n[02:39.01] तुही जीने का सहारा है\n[02:41.74] मेरी मौजों का किनारा है\n[02:44.60] मेरे लिए ये जहां है तु\n[02:47.23] तूझे मेरे दिल ने पुकारा है\n[02:50.20] \n[03:39.03] कहो न कहो, ये साँसें बोलती है\n[03:44.05] ओ सनम ओ सनम, ओ मेरे सनम\n[03:50.02] लबों पे नाम तेरे बस हमारा है\n[03:55.39] येह तेरा दिल भी जाना अब हमारा है\n[04:01.11] कहो ना कहो, ये आँखें बोलती है\n[04:06.19] ओ सनम ओ सनम, ओ मेरे सनम\n[04:12.07] मोहब्बत के सफर में, ये सहारा है\n[04:17.53] वफा के साहिलों का, ये किनारा है\n[04:23.00] \n[04:23.90] ख्वाबों में तुझको सांवरा है\n[04:26.37] जज़्बों में अपने उतारा है\n[04:29.40] मेरी ये आँखें जिधर देखे\n[04:32.05] तेरा ही तेरा नजारा है\n[04:34.99] ख्वाबों में तुझको सांवरा है\n[04:37.46] जज़्बों में अपने उतारा है\n[04:40.38] मेरी ये आँखें जिधर देखे\n[04:43.17] तेरा ही तेरा नजारा है\n[04:45.97] ख्वाबों में तुझको सांवरा है\n[04:48.70] जज़्बों में अपने उतारा है\n[04:51.39] मेरी ये आँखें जिधर देखे\n[04:54.17] तेरा ही तेरा नजारा है\n[04:57.00] ख्वाबों में तुझको सांवरा है\n[04:59.68] जज़्बों में अपने उतारा है\n[05:02.48] मेरी ये आँखें जिधर देखे\n[05:05.26] तेरा ही तेरा नजारा है\n[05:08.23] ', 'MURDER', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1756520284/bb0v5dtnfax2b7tcswed.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1756520275/xslbrcaggl4otfw5z84o.mp3'),
(20, 'Kaise Mein Kahun Tujhse', 'https://i.scdn.co/image/ab67616d0000b273e3fcf9171730df35aaf03c67', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765516283/y6dqzuci0gpba0apqpw2.jpg', '00:01:05', '[00:12.41] कैसे मैं कहूँ तुझसे\r\n[00:20.47] रहना है तेरे दिल मे\r\n[00:28.89] कैसे मैं कहूँ तुझसे\r\n[00:37.09] रहना है तेरे दिल मे\r\n[00:43.59] ', 'Rehnaa Hai Terre Dil Mein', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765516278/tzg4hyrjbxsog6nshmvp.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765516256/ldgoxftkxilsbu2u1cow.mp3'),
(21, 'Faded', 'https://i.scdn.co/image/ab67616d0000b273c4d00cac55ae1b4598c9bc90', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765514270/c6dtizy54rpmwh8houdq.jpg', '00:04:10', '[00:11.14] You were the shadow to my light\r\n[00:14.00] Did you feel us?\r\n[00:17.77] Another star\r\n[00:19.76] You fade away\r\n[00:21.87] Afraid our aim is out of sight\r\n[00:24.67] Wanna see us\r\n[00:28.43] Alive\r\n[00:30.90] Where are you now?\r\n[00:34.43] \r\n[00:36.53] Where are you now?\r\n[00:41.70] Where are you now?\r\n[00:44.32] Was it all in my fantasy?\r\n[00:47.13] Where are you now?\r\n[00:49.54] Were you only imaginary?\r\n[00:53.64] Where are you now?\r\n[00:57.01] Atlantis\r\n[00:59.16] Under the sea, under the sea\r\n[01:04.38] Where are you now?\r\n[01:07.07] Another dream\r\n[01:10.39] The monster\'s running wild inside of me\r\n[01:14.37] I\'m faded\r\n[01:19.65] I\'m faded\r\n[01:23.58] So lost, I\'m faded\r\n[01:28.05] \r\n[01:30.34] I\'m faded\r\n[01:34.22] So lost, I\'m faded\r\n[01:37.72] These shallow waters never met\r\n[01:40.88] What I needed\r\n[01:44.47] I\'m letting go\r\n[01:46.34] A deeper dive\r\n[01:48.56] Eternal silence of the sea\r\n[01:51.75] I\'m breathing\r\n[01:55.14] Alive\r\n[01:57.60] Where are you now?\r\n[02:01.14] \r\n[02:03.26] Where are you now?\r\n[02:08.41] Under the bright\r\n[02:09.77] But faded lights\r\n[02:11.03] You set my heart on fire\r\n[02:13.68] Where are you now?\r\n[02:16.31] Where are you now?\r\n[02:19.91] \r\n[02:31.04] Where are you now?\r\n[02:34.32] Atlantis\r\n[02:36.31] Under the sea, under the sea\r\n[02:41.66] Where are you now?\r\n[02:44.46] Another dream\r\n[02:47.75] The monster\'s running wild inside of me\r\n[02:51.71] I\'m faded\r\n[02:55.02] \r\n[02:57.17] I\'m faded\r\n[03:00.92] So lost, I\'m faded\r\n[03:05.46] \r\n[03:07.79] I\'m faded\r\n[03:11.53] So lost, I\'m faded\r\n[03:14.22] ', 'Sensation 2016 Angels & Demons', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765514265/qdcatkzi9fraw3kcclnv.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765514258/z8g75blgmulgpmyun5ck.mp3'),
(22, 'Enemy', 'https://i.scdn.co/image/ab67616d0000b273fc915b69600dce2991a61f13', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1765514985/drrk1pq8rtcohboeoir3.jpg', '00:02:53', '[00:04.42] Look out for yourself\r\n[00:06.87] I wake up to the sounds of the silence that allows\r\n[00:10.09] For my mind to run around with my ear up to the ground\r\n[00:13.10] I\'m searching to behold the stories that are told\r\n[00:16.14] When my back is to the world that was smiling when I turned\r\n[00:19.50] Tell you, you\'re the greatest\r\n[00:25.47] But once you turn, they hate us\r\n[00:32.86] Oh, the misery\r\n[00:35.27] Everybody wants to be my enemy\r\n[00:38.98] Spare the sympathy\r\n[00:41.33] Everybody wants to be my enemy\r\n[00:45.93] \r\n[00:48.00] Look out for yourself\r\n[00:49.63] My enemy\r\n[00:52.06] \r\n[00:54.30] Look out for yourself\r\n[00:55.72] But I\'m ready\r\n[00:56.73] Your words up on the wall as you\'re praying for my fall\r\n[00:59.94] And the laughter in the halls\r\n[01:01.54] And the names that I\'ve been called\r\n[01:03.01] I stack it in my mind and I\'m waiting for the time\r\n[01:06.26] When I show you what it\'s like to be words spit in a mic\r\n[01:09.38] Tell you, you\'re the greatest\r\n[01:15.37] But once you turn, they hate us (ha)\r\n[01:22.69] Oh, the misery\r\n[01:24.97] Everybody wants to be my enemy\r\n[01:28.75] Spare the sympathy\r\n[01:31.38] Everybody wants to be my enemy\r\n[01:35.44] \r\n[01:37.94] Look out for yourself\r\n[01:39.62] My enemy (yeah)\r\n[01:44.32] Look out for yourself\r\n[01:44.88] Uh, look, okay\r\n[01:46.87] I\'m hoping that somebody pray for me\r\n[01:48.44] I\'m praying that somebody hope for me\r\n[01:49.96] I\'m staying where nobody \'posed to be\r\n[01:51.75] Posted, being a wreck of emotions\r\n[01:53.24] Ready to go whenever, just let me know\r\n[01:54.30] The road is long, so put the pedal into the floor\r\n[01:55.96] The enemy on my trail, my energy unavailable\r\n[01:58.11] I\'ma tell \'em, \"Hasta luego\"\r\n[01:59.47] They wanna plot on my trot to the top\r\n[02:00.73] I\'ve been outta shape, thinkin\' out the box\r\n[02:01.88] I\'m an astronaut, I blasted off the planet rock\r\n[02:03.71] To cause catastrophe and it matters more because I had it\r\n[02:05.94] And I had a thought about wreaking havoc on an opposition, kinda shockin\'\r\n[02:08.52] They want a static with precision, I\'m automatic quarterback\r\n[02:10.61] I ain\'t talking sacking pack it, pack it up, I don\'t panic, batter-batter up\r\n[02:13.47] Who the baddest? It don\'t matter \'cause we at ya throat\r\n[02:14.87] Everybody wants to be my enemy\r\n[02:18.55] Spare the sympathy\r\n[02:21.31] Everybody wants to be my enemy\r\n[02:24.97] Oh, the misery\r\n[02:27.32] Everybody wants to be my enemy\r\n[02:31.09] Spare the sympathy\r\n[02:33.64] Everybody wants to be my enemy (I swear)\r\n[02:36.73] Pray it away, I swear, I never be a saint, no way\r\n[02:42.17] My enemy\r\n[02:42.99] Pray it away, I swear, I never be a saint\r\n[02:46.50] Look out for yourself\r\n[02:46.71] ', 'Enemy (from the series Arcane League of Legends)', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765514980/kxxflkpsnqfr8yekdawe.mp3', 'https://res.cloudinary.com/do3hihcwa/video/upload/v1765514967/aqqa9v0n95gnhwruuih0.mp3');

-- --------------------------------------------------------

--
-- Table structure for table `special`
--

CREATE TABLE `special` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `special`
--

INSERT INTO `special` (`id`, `sid`) VALUES
(1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `photo`) VALUES
(1, 'Dhruv', 'dhruv@gmail.com', 'Dhruv5', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1764439300/admin_profiles/ttbekwaymimm5vhqaanq.jpg'),
(2, 'Aayush', 'chauhanaayush367@gmail.com', '$2y$10$P7R5BKdqMzsDajHTKpLd6eeUTDuz2NXr2F/UixuLwYOhYqm7VT7yy', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1764439551/admin_profiles/ur3r2fxnpeib4nnq9yfh.jpg'),
(3, 'Aryan', 'aryansariya009@gmail.com', '$2y$10$Q37VMbdmw9Mm/w/dID5KzO.YpQLc3PgLMjQcdycMMT.Z8lMmHkQoy', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1764439674/admin_profiles/kvpinijjdvplscxxa0bw.png'),
(5, 'Kallu', 'kallu@gmail.com', 'kalu', 'https://res.cloudinary.com/do3hihcwa/image/upload/v1761892011/397dc29646c3d7b11e1412aa0f1ca865_lhh4we.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`arid`);

--
-- Indexes for table `artist_song`
--
ALTER TABLE `artist_song`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `song_id` (`song_id`,`artist_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`song_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`gid`);

--
-- Indexes for table `genre_song`
--
ALTER TABLE `genre_song`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `song_id` (`song_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_song` (`user_id`,`song_id`),
  ADD KEY `fk_history_song` (`song_id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`lid`);

--
-- Indexes for table `language_song`
--
ALTER TABLE `language_song`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language_id` (`language_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`name`);

--
-- Indexes for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `playlist_id` (`playlist_id`,`song_id`),
  ADD KEY `song_id` (`song_id`);

--
-- Indexes for table `slider`
--
ALTER TABLE `slider`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sid` (`sid`);

--
-- Indexes for table `song`
--
ALTER TABLE `song`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `special`
--
ALTER TABLE `special`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sid` (`sid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `artist`
--
ALTER TABLE `artist`
  MODIFY `arid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `artist_song`
--
ALTER TABLE `artist_song`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `favourite`
--
ALTER TABLE `favourite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genre`
--
ALTER TABLE `genre`
  MODIFY `gid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `genre_song`
--
ALTER TABLE `genre_song`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `lid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `language_song`
--
ALTER TABLE `language_song`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist_songs`
--
ALTER TABLE `playlist_songs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `slider`
--
ALTER TABLE `slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `song`
--
ALTER TABLE `song`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `special`
--
ALTER TABLE `special`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `language_song`
--
ALTER TABLE `language_song`
  ADD CONSTRAINT `language_song_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `language` (`lid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `language_song_ibfk_2` FOREIGN KEY (`song_id`) REFERENCES `song` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
