<?php

class WpforoUcfDBManager implements WpforoUcfConstants {

    public function alterProfilesTable($networkWide) {
        if (is_multisite() && $networkWide) {
            global $wpdb;
            $blogIds = $wpdb->get_col("SELECT `blog_id` FROM {$wpdb->blogs}");
            foreach ($blogIds as $blogId) {
                switch_to_blog($blogId);
                $this->addFieldsColumn();
                restore_current_blog();
            }
        } else {
            $this->addFieldsColumn();
        }
    }

    public function addFieldsColumn() {
        global $wpdb;
        $columns = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}wpforo_profiles`;");
        $hasFildsColumn = false;
        if ($columns && is_array($columns)) {
            foreach ($columns as $column) {
                if ($column->Field == 'fields') {
                    $hasFildsColumn = true;
                    break;
                }
            }
        }

        if (!$hasFildsColumn) {
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpforo_profiles` ADD COLUMN `fields` longtext DEFAULT NULL;");
        }
    }

    public function addFields($userId, $data) {
        if ($userId && $data && is_array($data)) {
            global $wpdb;
            $data = array_filter($data);
            $data = WpforoUcfHelper::unescapeData($data);
            $data = WpforoUcfHelper::decodeData($data);
            $data = WpforoUcfHelper::encodeData($data);
            $oldData = wpfucfGetFields($userId);
            if ($oldData && is_array($oldData)) {
                $data = wp_parse_args($data, $oldData);
            }
            $fieldsJson = WpforoUcfHelper::unescapeData(json_encode($data, JSON_UNESCAPED_UNICODE));
            $sql = "UPDATE `{$wpdb->prefix}wpforo_profiles` SET `fields` = %s WHERE `userid` = %d;";
            $sql = $wpdb->prepare($sql, $fieldsJson, $userId);
            $wpdb->query($sql);
        }
    }

    public function getField($userId, $fieldName) {
        $data = '';
        if ($userId && $fieldName) {
            $fields = $this->getFields($userId);
            if ($fields && is_array($fields) && isset($fields[$fieldName])) {
                $data = trim(esc_attr($fields[$fieldName]));
            }
        };
        return $data;
    }

    public function getFields($userId) {
        $data = array();
        if ($userId) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT `fields` FROM `{$wpdb->prefix}wpforo_profiles` WHERE userid = %d", $userId);
            $result = $wpdb->get_col($sql);
            if ($result && ($obj = $result[0])) {
                $data = (array) json_decode($obj, true);
            }
            $data = WpforoUcfHelper::unescapeData($data);
        }
        return $data;
    }

    /* === FIELD VALUES FUNCTIONS === */

    public function getFieldValue($userId, $colName) {
        if (($uId = intval($userId)) && ($colummn = trim($colName))) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT $colummn FROM `{$wpdb->prefix}wpforo_profiles` WHERE userid = %d", $uId);
            return $wpdb->get_var($sql);
        }
    }

    /* === FIELD VALUES FUNCTIONS === */

    /**
     * check if table exists in database
     * return true if exists false otherwise
     */
    public function isTableExists($tableName, $withPrefix = true) {
        global $wpdb;
        $tbl = $withPrefix ? $tableName : $wpdb->prefix . $tableName;
        return $wpdb->get_var("SHOW TABLES LIKE '{$tbl}'");
    }

}
