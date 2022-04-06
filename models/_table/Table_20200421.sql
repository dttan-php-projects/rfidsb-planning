-- MySQL dump 10.13  Distrib 8.0.16, for Win64 (x86_64)
--
-- Host: 147.121.73.252    Database: avery_rfid
-- ------------------------------------------------------
-- Server version	5.7.29-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adidas_fw`
--

DROP TABLE IF EXISTS `adidas_fw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `adidas_fw` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `INTERNAL_ITEM` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `NOTE` varchar(999) COLLATE utf8_bin DEFAULT NULL,
  `UPDATED_BY` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `CREATED_DATE_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `INTERNAL_ITEM` (`INTERNAL_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `database_trim`
--

DROP TABLE IF EXISTS `database_trim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `database_trim` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `INTERNAL_ITEM` varchar(255) DEFAULT NULL,
  `MATERIAL_CODE` varchar(255) DEFAULT NULL,
  `MATERIAL_DES` varchar(550) DEFAULT NULL,
  `RIBBON_CODE` varchar(255) DEFAULT NULL,
  `RIBBON_DES` varchar(550) DEFAULT NULL,
  `CHIEU_DAI` varchar(45) DEFAULT NULL,
  `CHIEU_NGANG` varchar(45) DEFAULT NULL,
  `RBO` varchar(255) DEFAULT NULL,
  `ORDER_ITEM` varchar(255) DEFAULT NULL,
  `REMARK` varchar(999) DEFAULT NULL,
  `REMARK_MUC` varchar(999) DEFAULT NULL,
  `MACHINE` varchar(255) DEFAULT NULL,
  `REMARK_GIAY` varchar(999) DEFAULT NULL,
  `UPDATED_BY` varchar(45) DEFAULT NULL,
  `CREATED_DATE_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `OTHER_REMARK_1` varchar(550) DEFAULT NULL,
  `OTHER_REMARK_2` varchar(550) DEFAULT NULL,
  `OTHER_REMARK_3` varchar(550) DEFAULT NULL,
  `OTHER_REMARK_4` varchar(550) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `INTERNAL_ITEM` (`INTERNAL_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=3044 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `material` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `material_fb`
--

DROP TABLE IF EXISTS `material_fb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `material_fb` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item` varchar(45) DEFAULT NULL,
  `item_des` varchar(999) DEFAULT NULL,
  `UPDATED_BY` varchar(45) DEFAULT NULL,
  `CREATED_DATE_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `item` (`item`)
) ENGINE=InnoDB AUTO_INCREMENT=1920 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ms_color`
--

DROP TABLE IF EXISTS `ms_color`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ms_color` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `internal_item` varchar(45) DEFAULT NULL,
  `rbo` varchar(100) DEFAULT NULL,
  `order_item` varchar(100) DEFAULT NULL,
  `color_code` varchar(45) DEFAULT NULL,
  `item_color` varchar(45) DEFAULT NULL,
  `material_code` varchar(45) DEFAULT NULL,
  `material_des` text,
  `ribbon_code` varchar(45) DEFAULT NULL,
  `ink_des` text,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `note` text,
  `blank_gap` float DEFAULT NULL,
  `remark` text,
  `form_type` varchar(45) DEFAULT NULL COMMENT '1 - CBS\n2 - NON CBS\n',
  `created_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_by` varchar(45) DEFAULT NULL,
  `other_remark_1` varchar(550) DEFAULT NULL,
  `other_remark_2` varchar(550) DEFAULT NULL,
  `other_remark_3` varchar(550) DEFAULT NULL,
  `other_remark_4` varchar(550) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `internal_item` (`internal_item`),
  KEY `material_code` (`material_code`)
) ENGINE=InnoDB AUTO_INCREMENT=985 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `no_cbs`
--

DROP TABLE IF EXISTS `no_cbs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `no_cbs` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `internal_item` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `rbo` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `order_item` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `material_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `ribbon_code` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `material_des` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ink_des` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `width` float DEFAULT NULL,
  `height` float DEFAULT NULL,
  `pcs_sht` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `ghi_chu_item` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `note_rbo` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `remark_GIAY` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `lay_sample_15_pcs` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `remark_MUC` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `first_order` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `blank_gap` float DEFAULT NULL,
  `kind_of_label` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `note_price` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `note_color` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `UPDATED_BY` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `CREATED_DATE_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `STANDARD_LT` varchar(9) COLLATE utf8_bin DEFAULT NULL,
  `OTHER_REMARK_1` varchar(550) COLLATE utf8_bin DEFAULT NULL,
  `OTHER_REMARK_2` varchar(550) COLLATE utf8_bin DEFAULT NULL,
  `OTHER_REMARK_3` varchar(550) COLLATE utf8_bin DEFAULT NULL,
  `OTHER_REMARK_4` varchar(550) COLLATE utf8_bin DEFAULT NULL,
  `note` varchar(550) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `internal_item` (`internal_item`)
) ENGINE=InnoDB AUTO_INCREMENT=504 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `remark`
--

DROP TABLE IF EXISTS `remark`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `remark` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `internal_item` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `remark` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `internal_item` (`internal_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_ink_no_cbs_save`
--

DROP TABLE IF EXISTS `rfid_po_ink_no_cbs_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_ink_no_cbs_save` (
  `INK_NO` varchar(17) NOT NULL,
  `INK_ID` int(2) NOT NULL,
  `INK_PO_NO` varchar(45) NOT NULL,
  `INK_PO_SO_LINE` varchar(12) NOT NULL,
  `INK_PO_FORM_TYPE` varchar(45) NOT NULL,
  `INK_CODE` varchar(45) DEFAULT NULL,
  `INK_DES` varchar(2000) DEFAULT NULL,
  `INK_QTY` int(11) DEFAULT NULL,
  `INK_REMARK` varchar(2000) DEFAULT NULL,
  `INK_PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `INK_CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`INK_NO`,`INK_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_material_cbs_save`
--

DROP TABLE IF EXISTS `rfid_po_material_cbs_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_material_cbs_save` (
  `M_NO` varchar(17) NOT NULL,
  `M_ID` int(2) NOT NULL,
  `M_PO_NO` varchar(45) NOT NULL,
  `M_PO_SO_LINE` varchar(12) NOT NULL,
  `M_PO_FORM_TYPE` varchar(45) NOT NULL,
  `M_MATERIAL_CODE` varchar(45) DEFAULT NULL,
  `M_MATERIAL_DES` varchar(255) DEFAULT NULL,
  `M_MATERIAL_QTY` int(11) DEFAULT NULL,
  `M_MATERIAL_REMARK` varchar(255) DEFAULT NULL,
  `M_PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `M_CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`M_NO`,`M_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_material_ink_save`
--

DROP TABLE IF EXISTS `rfid_po_material_ink_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_material_ink_save` (
  `MI_NO` varchar(17) NOT NULL,
  `MI_ID` int(2) NOT NULL,
  `MI_PO_NO` varchar(45) NOT NULL,
  `MI_PO_SO_LINE` varchar(12) NOT NULL,
  `MI_PO_FORM_TYPE` varchar(45) NOT NULL,
  `MI_MATERIAL_CODE` varchar(45) DEFAULT NULL,
  `MI_MATERIAL_DES` varchar(2000) DEFAULT NULL,
  `MI_MATERIAL_QTY` int(11) DEFAULT NULL,
  `MI_INK_CODE` varchar(45) DEFAULT NULL,
  `MI_INK_DES` varchar(2000) DEFAULT NULL,
  `MI_INK_QTY` int(11) DEFAULT NULL,
  `MI_MATERIAL_INK_REMARK` varchar(255) DEFAULT NULL,
  `MI_PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `MI_CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MI_NO`,`MI_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_material_no_cbs_save`
--

DROP TABLE IF EXISTS `rfid_po_material_no_cbs_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_material_no_cbs_save` (
  `MN_NO` varchar(17) NOT NULL,
  `MN_ID` int(2) NOT NULL,
  `MN_PO_NO` varchar(45) NOT NULL,
  `MN_PO_SO_LINE` varchar(12) NOT NULL,
  `MN_PO_FORM_TYPE` varchar(45) DEFAULT NULL,
  `MN_MATERIAL_CODE` varchar(45) DEFAULT NULL,
  `MN_MATERIAL_DES` varchar(2000) DEFAULT NULL,
  `MN_MATERIAL_QTY` int(11) DEFAULT NULL,
  `MN_MATERIAL_REMARK` varchar(2000) DEFAULT NULL,
  `MN_PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `MN_CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MN_NO`,`MN_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_save`
--

DROP TABLE IF EXISTS `rfid_po_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_save` (
  `PO_NO` varchar(45) NOT NULL,
  `PO_NO_SUFFIX` varchar(45) DEFAULT 'NON',
  `PO_SO_LINE` varchar(12) NOT NULL,
  `PO_FORM_TYPE` varchar(45) NOT NULL,
  `PO_INTERNAL_ITEM` varchar(550) DEFAULT NULL,
  `PO_ORDER_ITEM` varchar(550) DEFAULT NULL,
  `PO_GPM` varchar(45) DEFAULT NULL,
  `PO_RBO` varchar(45) DEFAULT NULL,
  `PO_SHIP_TO_CUSTOMER` varchar(255) DEFAULT NULL,
  `PO_CS` varchar(45) DEFAULT NULL,
  `PO_QTY` int(11) DEFAULT NULL,
  `PO_LABEL_SIZE` varchar(45) DEFAULT NULL,
  `PO_MATERIAL_CODE` varchar(45) DEFAULT NULL,
  `PO_MATERIAL_DES` varchar(2000) DEFAULT NULL,
  `PO_MATERIAL_QTY` int(11) DEFAULT NULL,
  `PO_INK_CODE` varchar(45) DEFAULT NULL,
  `PO_INK_DES` varchar(255) DEFAULT NULL,
  `PO_INK_QTY` int(11) DEFAULT NULL,
  `PO_COUNT_SO_LINE` int(2) DEFAULT NULL,
  `PO_SAVE_DATE` date DEFAULT NULL,
  `PO_PROMISE_DATE` date DEFAULT NULL,
  `PO_REQUEST_DATE` date DEFAULT NULL,
  `PO_ORDERED_DATE` date DEFAULT NULL,
  `PO_MAIN_SAMPLE_LINE` varchar(550) DEFAULT NULL,
  `PO_SAMPLE` int(2) DEFAULT NULL,
  `PO_SAMPLE_15PCS` varchar(255) DEFAULT NULL,
  `PO_MATERIAL_REMARK` varchar(255) DEFAULT NULL,
  `PO_INK_REMARK` varchar(2000) DEFAULT NULL,
  `PO_REMARK_1` varchar(255) DEFAULT NULL,
  `PO_REMARK_2` varchar(255) DEFAULT NULL,
  `PO_REMARK_3` varchar(255) DEFAULT NULL,
  `PO_REMARK_4` varchar(255) DEFAULT NULL,
  `PO_D126` float DEFAULT NULL,
  `PO_PRINTED` int(2) DEFAULT NULL,
  `PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `PO_CREATED_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `PO_UPDATED_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `PO_DATE_RECEIVED` date DEFAULT NULL,
  `PO_FILE_DATE_RECEIVED` varchar(5) DEFAULT NULL,
  `PO_ORDER_TYPE_NAME` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`PO_NO`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_size_cbs_save`
--

DROP TABLE IF EXISTS `rfid_po_size_cbs_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_size_cbs_save` (
  `S_NO` varchar(17) NOT NULL,
  `S_ID` int(2) NOT NULL,
  `S_PO_NO` varchar(45) NOT NULL,
  `S_PO_SO_LINE` varchar(12) NOT NULL,
  `S_PO_FORM_TYPE` varchar(45) NOT NULL,
  `S_SIZE` varchar(45) DEFAULT NULL,
  `S_LABEL_ITEM` varchar(550) DEFAULT NULL,
  `S_BASE_ROLL` varchar(550) DEFAULT NULL,
  `S_QTY` int(11) DEFAULT NULL,
  `S_INK_QTY` float DEFAULT NULL,
  `S_PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `S_CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`S_NO`,`S_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfid_po_soline_save`
--

DROP TABLE IF EXISTS `rfid_po_soline_save`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfid_po_soline_save` (
  `SO_PO_NO` varchar(45) NOT NULL,
  `SO_LINE` varchar(12) NOT NULL,
  `SO_PO_QTY` int(11) DEFAULT '0',
  `SO_INTERNAL_ITEM` varchar(225) DEFAULT NULL,
  `SO_ORDER_ITEM` varchar(550) DEFAULT NULL,
  `SO_WIDTH` float DEFAULT '0',
  `SO_HEIGHT` float DEFAULT '0',
  `SO_PO_CREATED_BY` varchar(45) DEFAULT NULL,
  `SO_CREATED_DATE` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SO_PO_NO`,`SO_LINE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rfidsb_scrap`
--

DROP TABLE IF EXISTS `rfidsb_scrap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `rfidsb_scrap` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RBO` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `SCRAP` float(10,3) NOT NULL,
  `CREATED_BY` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `CREATED_DATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=996 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_ink`
--

DROP TABLE IF EXISTS `save_ink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_ink` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `INK_CODE` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `INK_DES` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `INK_QTY` float DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=24057 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_ink_new`
--

DROP TABLE IF EXISTS `save_ink_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_ink_new` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `INK_CODE` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `INK_DES` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `INK_QTY` float DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_SAVE_ITEM` (`ID_SAVE_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_item`
--

DROP TABLE IF EXISTS `save_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_item` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SAVE_DATE` date DEFAULT NULL,
  `NUMBER_NO` varchar(45) DEFAULT NULL,
  `SO_LINE` varchar(45) DEFAULT NULL,
  `CS` varchar(45) DEFAULT NULL,
  `PD` date DEFAULT NULL,
  `REQ` date DEFAULT NULL,
  `ORDERED` date DEFAULT NULL,
  `PO` varchar(45) DEFAULT NULL,
  `ITEM` varchar(45) DEFAULT NULL,
  `RBO` varchar(45) DEFAULT NULL,
  `QTY_MATERIAL` float DEFAULT NULL,
  `WIDTH` float DEFAULT NULL,
  `HEIGHT` float DEFAULT NULL,
  `INK_DES` varchar(255) DEFAULT NULL,
  `RIBBON` varchar(45) DEFAULT NULL,
  `D126` float DEFAULT NULL,
  `QTY` int(11) DEFAULT NULL,
  `WIDTH_HEIGHT` varchar(45) DEFAULT NULL,
  `GAP` float DEFAULT NULL,
  `FORM_TYPE` varchar(45) DEFAULT NULL,
  `REMARK_1` varchar(255) DEFAULT NULL,
  `REMARK_2` varchar(255) DEFAULT NULL,
  `REMARK_3` varchar(255) DEFAULT NULL,
  `REMARK_4` varchar(255) DEFAULT NULL,
  `REMARK_5` varchar(255) DEFAULT NULL,
  `REMARK_6` varchar(255) DEFAULT NULL,
  `PRINTED` varchar(45) DEFAULT '0',
  `CREATED_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(45) DEFAULT NULL,
  `SHIP_TO_CUSTOMER` varchar(255) DEFAULT NULL,
  `BILL_TO_CUSTOMER` varchar(255) DEFAULT NULL,
  `RBO_AUTOMAIL` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `NUMBER_NO` (`NUMBER_NO`)
) ENGINE=InnoDB AUTO_INCREMENT=31469 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_item_new`
--

DROP TABLE IF EXISTS `save_item_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_item_new` (
  `ID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `SAVE_DATE` date DEFAULT NULL,
  `DATA_RECEIVED_DATE` date DEFAULT NULL,
  `SO_LAN` varchar(255) DEFAULT NULL,
  `NUMBER_NO` varchar(45) DEFAULT NULL,
  `SO_LINE` varchar(45) DEFAULT NULL,
  `CS` varchar(45) DEFAULT NULL,
  `PD` date DEFAULT NULL,
  `REQ` date DEFAULT NULL,
  `ORDERED` date DEFAULT NULL,
  `PO` varchar(45) DEFAULT NULL,
  `ITEM` varchar(45) DEFAULT NULL,
  `RBO` varchar(45) DEFAULT NULL,
  `QTY_MATERIAL` float DEFAULT NULL,
  `WIDTH` float DEFAULT NULL,
  `HEIGHT` float DEFAULT NULL,
  `INK_DES` varchar(255) DEFAULT NULL,
  `RIBBON` varchar(45) DEFAULT NULL,
  `D126` float DEFAULT NULL,
  `QTY` int(11) DEFAULT NULL,
  `WIDTH_HEIGHT` varchar(45) DEFAULT NULL,
  `GAP` float DEFAULT NULL,
  `FORM_TYPE` varchar(45) DEFAULT NULL,
  `REMARK_1` varchar(255) DEFAULT NULL,
  `REMARK_2` varchar(255) DEFAULT NULL,
  `REMARK_3` varchar(255) DEFAULT NULL,
  `REMARK_4` varchar(255) DEFAULT NULL,
  `REMARK_5` varchar(255) DEFAULT NULL,
  `REMARK_6` varchar(255) DEFAULT NULL,
  `PRINTED` varchar(45) DEFAULT '0',
  `CREATED_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(45) DEFAULT NULL,
  `COUNT_SO_LINE` int(11) DEFAULT '1',
  `GPM` varchar(45) DEFAULT NULL,
  `SHIP_TO_CUSTOMER` varchar(255) DEFAULT NULL,
  `ORDER_TYPE_NAME` varchar(45) DEFAULT NULL,
  `BILL_TO_CUSTOMER` varchar(255) DEFAULT NULL,
  `RFID_CHECK_CB` varchar(999) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `NUMBER_NO` (`NUMBER_NO`),
  KEY `SAVE_DATE` (`SAVE_DATE`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_material`
--

DROP TABLE IF EXISTS `save_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_material` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) DEFAULT NULL,
  `MATERIAL_CODE` varchar(45) DEFAULT NULL,
  `MATERIAL_DES` varchar(255) DEFAULT NULL,
  `MATERIAL_QTY` float DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_SAVE_ITEM` (`ID_SAVE_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=58057 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_material_new`
--

DROP TABLE IF EXISTS `save_material_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_material_new` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) DEFAULT NULL,
  `MATERIAL_CODE` varchar(45) DEFAULT NULL,
  `MATERIAL_DES` varchar(255) DEFAULT NULL,
  `MATERIAL_QTY` float DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_SAVE_ITEM` (`ID_SAVE_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_new_size`
--

DROP TABLE IF EXISTS `save_new_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_new_size` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `so_line` varchar(45) DEFAULT NULL,
  `size` varchar(45) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_pvh_trim`
--

DROP TABLE IF EXISTS `save_pvh_trim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_pvh_trim` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) DEFAULT NULL,
  `SO_LINE` varchar(45) DEFAULT NULL,
  `INTERNAL_ITEM` varchar(255) DEFAULT NULL,
  `QTY` int(11) DEFAULT NULL,
  `PAPER_TYPE` varchar(255) DEFAULT NULL,
  `PAPER_QTY` int(11) DEFAULT NULL,
  `INK_TYPE` varchar(255) DEFAULT NULL,
  `INK_QTY` int(11) DEFAULT NULL,
  `ORDER_ITEM` varchar(255) DEFAULT NULL,
  `PAPER_DES` varchar(255) DEFAULT NULL,
  `INK_DES` varchar(255) DEFAULT NULL,
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `ID_SAVE_ITEM` (`ID_SAVE_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_size`
--

DROP TABLE IF EXISTS `save_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_size` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) DEFAULT NULL,
  `SIZE` varchar(45) DEFAULT NULL,
  `LABEL_ITEM` varchar(45) DEFAULT NULL,
  `BASE_ROLL` varchar(45) DEFAULT NULL,
  `QTY` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_SAVE_ITEM` (`ID_SAVE_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=33931 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `save_size_new`
--

DROP TABLE IF EXISTS `save_size_new`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `save_size_new` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ID_SAVE_ITEM` varchar(45) DEFAULT NULL,
  `SIZE` varchar(45) DEFAULT NULL,
  `LABEL_ITEM` varchar(45) DEFAULT NULL,
  `BASE_ROLL` varchar(45) DEFAULT NULL,
  `QTY` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `ID_SAVE_ITEM` (`ID_SAVE_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setting_item_form`
--

DROP TABLE IF EXISTS `setting_item_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `setting_item_form` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `INTERNAL_ITEM` varchar(255) DEFAULT NULL,
  `FORM_TYPE` varchar(255) DEFAULT NULL,
  `CREATED_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  `CREATED_BY` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID_UNIQUE` (`ID`),
  KEY `INTERNAL_ITEM` (`INTERNAL_ITEM`)
) ENGINE=InnoDB AUTO_INCREMENT=2992 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ua`
--

DROP TABLE IF EXISTS `ua`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `ua` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item` varchar(45) DEFAULT NULL,
  `size` varchar(45) DEFAULT NULL,
  `base_roll` varchar(45) DEFAULT NULL,
  `UPDATED_BY` varchar(45) DEFAULT NULL,
  `CREATED_DATE_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `item_size` (`item`,`size`)
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `EMAIL` varchar(255) DEFAULT NULL,
  `IS_ADMIN` varchar(255) DEFAULT '0',
  `UPDATED_BY` varchar(45) DEFAULT NULL,
  `CREATED_TIME` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_rfid`
--

DROP TABLE IF EXISTS `user_rfid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
 SET character_set_client = utf8mb4 ;
CREATE TABLE `user_rfid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-21  8:24:30
