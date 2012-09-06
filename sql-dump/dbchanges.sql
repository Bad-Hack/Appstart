/**
 * Customer Table altered for user_id and removing customer name and password
 * @author Tirth Bodawala
 * @date 3/9/2012
 */
ALTER TABLE `customer`
DROP COLUMN `customer_user_name`,
CHANGE COLUMN `password` `user_id`  int(11) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `app_access_id`;

ALTER TABLE `customer`
MODIFY COLUMN `status`  tinyint(1) NULL DEFAULT NULL AFTER `contact_person_phone`;

ALTER TABLE `user`
CHANGE COLUMN `user_name` `username`  varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `user_id`;

