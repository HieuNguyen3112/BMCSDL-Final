<?php
// models/AuditLogModel.php

class AuditLogModel {
    protected $db;
    public function __construct($db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Ghi một bản audit.
     *
     * @param string $action    UPDATE|LOGIN|LOGOUT|VIEW_LIST|VIEW_DETAIL|VIEW_PROFILE|etc
     * @param string $tableName NHANVIEN, TAIKHOAN, ...
     * @param int    $recordId  id record bị tác động (0 nếu không áp dụng)
     * @param array|null $oldData  Dữ liệu trước (assoc array) hoặc null
     * @param array|null $newData  Dữ liệu sau  (assoc array) hoặc null
     */
    public function write(
        string $action,
        string $tableName,
        int    $recordId,
        ?array $oldData = null,
        ?array $newData = null
    ) {
        $user = $_SESSION['user'] ?? ['MaNhanVien'=>0,'HoTen'=>'','TenRole'=>''];
        $ip   = $_SERVER['REMOTE_ADDR'] ?? '';

        $sql = "
          INSERT INTO audit_log 
            (action, table_name, record_id, old_data, new_data, user_id, user_name, user_role, ip_address)
          VALUES (?,?,?,?,?,?,?,?,?)
        ";
        $stmt = $this->db->prepare($sql);

        $oldJson = $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null;
        $newJson = $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null;

        $stmt->bind_param(
            'ssississs',
            $action,
            $tableName,
            $recordId,
            $oldJson,
            $newJson,
            $user['MaNhanVien'],
            $user['HoTen'],
            $user['TenRole'],
            $ip
        );
        $stmt->execute();
    }

    /**
     * Lấy tất cả audit log (dành cho endpoint)
     *
     * @return array
     */
    public function getAll(): array {
        $stmt = $this->db->prepare("SELECT * FROM audit_log ORDER BY created_at DESC");
        $stmt->execute();
        $res = $stmt->get_result();
        $out = [];
        while ($r = $res->fetch_assoc()) {
            $out[] = $r;
        }
        return $out;
    }
}
