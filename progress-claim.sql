/*
 Navicat Premium Data Transfer

 Source Server         : Localhost
 Source Server Type    : MySQL
 Source Server Version : 80406 (8.4.6)
 Source Host           : localhost:3306
 Source Schema         : cpm

 Target Server Type    : MySQL
 Target Server Version : 80406 (8.4.6)
 File Encoding         : 65001

 Date: 29/10/2026 15:39:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cost_contract_progress_claim_activities
-- ----------------------------
DROP TABLE IF EXISTS `cost_contract_progress_claim_activities`;
CREATE TABLE `cost_contract_progress_claim_activities`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `cc_progress_claim_id` bigint UNSIGNED NOT NULL COMMENT 'cost_contract_progress_claims',
  `log_status_id` tinyint UNSIGNED NOT NULL COMMENT 'log_statuses',
  `step_order` tinyint UNSIGNED NOT NULL,
  `assigned_to` bigint NULL DEFAULT NULL COMMENT 'users',
  `cc` json NULL,
  `due_date` date NULL DEFAULT NULL,
  `remarks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_progress_claim_activity_latest`(`cc_progress_claim_id` ASC, `created_at` DESC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 27 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of cost_contract_progress_claim_activities
-- ----------------------------
BEGIN;
INSERT INTO `cost_contract_progress_claim_activities` (`id`, `cc_progress_claim_id`, `log_status_id`, `step_order`, `assigned_to`, `cc`, `due_date`, `remarks`, `created_by`, `created_at`, `updated_by`, `updated_at`, `deleted_by`, `deleted_at`) VALUES (1, 1, 1, 1, 3, NULL, '2026-08-18', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 2, '2026-07-13 14:21:18', 2, '2026-07-13 15:16:29', NULL, NULL), (2, 2, 1, 1, 3, '[6]', '2026-09-15', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry', 2, '2026-07-13 14:47:43', NULL, NULL, NULL, NULL), (3, 2, 2, 2, 4, NULL, '2026-09-30', NULL, 3, '2026-07-13 15:36:00', NULL, NULL, NULL, NULL), (4, 2, 5, 1, 2, NULL, NULL, NULL, 4, '2026-07-13 15:38:31', NULL, NULL, NULL, NULL), (5, 3, 1, 1, 3, NULL, '2026-11-01', 'Lorem ipsum', 2, '2026-07-14 11:43:02', NULL, NULL, NULL, NULL), (6, 4, 1, 1, 3, NULL, '2026-08-01', NULL, 2, '2026-07-23 09:44:45', NULL, NULL, NULL, NULL), (7, 4, 2, 2, 4, NULL, NULL, NULL, 3, '2026-07-23 13:58:28', NULL, NULL, NULL, NULL), (8, 3, 2, 2, 4, NULL, NULL, NULL, 3, '2026-07-23 13:58:37', NULL, NULL, NULL, NULL), (9, 1, 2, 2, 4, NULL, NULL, NULL, 3, '2026-07-23 13:58:47', NULL, NULL, NULL, NULL), (10, 4, 2, 3, 5, NULL, NULL, NULL, 4, '2026-07-23 13:59:22', NULL, NULL, NULL, NULL), (11, 3, 2, 3, 5, NULL, NULL, NULL, 4, '2026-07-23 13:59:31', NULL, NULL, NULL, NULL), (12, 1, 2, 3, 5, NULL, NULL, NULL, 4, '2026-07-23 13:59:40', NULL, NULL, NULL, NULL), (13, 4, 3, 3, NULL, NULL, NULL, NULL, 5, '2026-07-23 14:00:03', NULL, NULL, NULL, NULL), (14, 3, 3, 3, NULL, NULL, NULL, NULL, 5, '2026-07-23 14:00:15', NULL, NULL, NULL, NULL), (15, 1, 4, 3, NULL, NULL, NULL, NULL, 5, '2026-07-23 14:00:25', NULL, NULL, NULL, NULL), (16, 2, 1, 1, 3, NULL, NULL, 'Ok', 2, '2026-07-23 14:02:00', NULL, NULL, NULL, NULL), (17, 2, 2, 2, 4, NULL, NULL, NULL, 3, '2026-07-23 14:03:18', NULL, NULL, NULL, NULL), (18, 2, 2, 3, 5, NULL, NULL, NULL, 4, '2026-07-23 14:03:36', NULL, NULL, NULL, NULL), (19, 2, 3, 3, NULL, NULL, NULL, NULL, 5, '2026-07-23 14:04:01', NULL, NULL, NULL, NULL), (20, 5, 1, 1, 3, NULL, '2026-09-01', 'Lorem ipsum', 2, '2026-07-27 11:55:17', NULL, NULL, NULL, NULL), (21, 5, 2, 2, 4, NULL, NULL, NULL, 3, '2026-07-27 11:55:52', NULL, NULL, NULL, NULL), (22, 5, 2, 3, 5, NULL, NULL, NULL, 4, '2026-07-27 11:57:12', NULL, NULL, NULL, NULL), (23, 5, 3, 3, NULL, NULL, NULL, NULL, 5, '2026-07-27 11:57:40', NULL, NULL, NULL, NULL), (24, 6, 1, 1, 3, NULL, '2026-09-30', '01, 02', 2, '2026-08-13 15:15:28', NULL, NULL, NULL, NULL), (25, 6, 2, 2, 4, NULL, NULL, NULL, 3, '2026-09-15 14:47:56', NULL, NULL, NULL, NULL), (26, 6, 2, 3, 5, '[7, 6]', '2026-09-16', 'Lorem ipsum Progress Claim', 4, '2026-09-16 09:45:04', NULL, NULL, NULL, NULL);
COMMIT;

-- ----------------------------
-- Table structure for cost_contract_progress_claims
-- ----------------------------
DROP TABLE IF EXISTS `cost_contract_progress_claims`;
CREATE TABLE `cost_contract_progress_claims`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint UNSIGNED NOT NULL COMMENT 'projects',
  `cc_contract_document_id` bigint UNSIGNED NOT NULL COMMENT 'cost_contract_contract_documents',
  `cc_contract_document_term_of_payment_id` bigint UNSIGNED NULL DEFAULT NULL COMMENT 'cost_contract_contract_document_term_of_payments',
  `log_status_id` tinyint UNSIGNED NOT NULL COMMENT 'log_statuses',
  `issued_date` date NOT NULL,
  `progress_type` enum('physical','top') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `physical_progress` decimal(5, 2) NULL DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` bigint UNSIGNED NULL DEFAULT NULL,
  `updated_at` datetime NULL DEFAULT NULL,
  `deleted_by` bigint UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_progress_claims_project_id`(`project_id` ASC) USING BTREE,
  INDEX `fk_progress_claims_contract_document`(`cc_contract_document_id` ASC) USING BTREE,
  INDEX `fk_progress_claims_term_of_payment`(`cc_contract_document_term_of_payment_id` ASC) USING BTREE,
  CONSTRAINT `fk_progress_claims_contract_document` FOREIGN KEY (`cc_contract_document_id`) REFERENCES `cost_contract_contract_documents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_progress_claims_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_progress_claims_term_of_payment` FOREIGN KEY (`cc_contract_document_term_of_payment_id`) REFERENCES `cost_contract_contract_document_term_of_payments` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of cost_contract_progress_claims
-- ----------------------------
BEGIN;
INSERT INTO `cost_contract_progress_claims` (`id`, `project_id`, `cc_contract_document_id`, `cc_contract_document_term_of_payment_id`, `log_status_id`, `issued_date`, `progress_type`, `physical_progress`, `created_by`, `created_at`, `updated_by`, `updated_at`, `deleted_by`, `deleted_at`) VALUES (1, 2, 112, NULL, 4, '2026-08-17', 'physical', 50.00, 2, '2026-07-13 14:21:18', 5, '2026-07-23 14:00:25', NULL, NULL), (2, 2, 111, 1, 3, '2026-09-14', 'top', NULL, 2, '2026-07-13 14:47:43', 5, '2026-07-23 14:04:01', NULL, NULL), (3, 2, 111, 4, 3, '2026-10-01', 'top', NULL, 2, '2026-07-14 11:43:02', 5, '2026-07-23 14:00:15', NULL, NULL), (4, 2, 113, NULL, 3, '2026-08-01', 'physical', 15.00, 2, '2026-07-23 09:44:45', 5, '2026-07-23 14:00:03', NULL, NULL), (5, 2, 114, NULL, 3, '2026-09-01', 'physical', 30.00, 2, '2026-07-27 11:55:17', 5, '2026-07-27 11:57:40', NULL, NULL), (6, 2, 111, 1, 2, '2026-09-28', 'top', NULL, 2, '2026-08-13 15:15:28', 3, '2026-09-15 14:47:56', NULL, NULL);
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
