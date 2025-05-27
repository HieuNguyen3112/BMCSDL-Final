<?php
// controller/AuditLogController.php
require_once __DIR__ . '/../model/AuditLogModel.php';

class AuditLogController {
    /** @var AuditLogModel */
    protected $auditModel;

    public function __construct(mysqli $conn) {
        $this->auditModel = new AuditLogModel($conn);
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * GET /api/audit-log
     */
    public function apiGetAll() {
        header('Content-Type: application/json; charset=utf-8');

        // 1) Phải đã login
        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['success'=>false,'error'=>'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 2) Chỉ Trưởng phòng nhân sự & Giám đốc mới xem được
        $role = $_SESSION['user']['TenRole'] ?? '';
        if (! in_array($role, ['TruongPhongNhanSuRole','GiamDocRole'])) {
            http_response_code(403);
            echo json_encode(['success'=>false,'error'=>'Không có quyền xem audit log'], JSON_UNESCAPED_UNICODE);
            return;
        }

        // 3) Trả về dữ liệu
        $logs = $this->auditModel->getAll();
        echo json_encode(['success'=>true,'data'=>$logs], JSON_UNESCAPED_UNICODE);
    }

    // AuditLogController.php
    public function log($actionType, $description, $tableName = null, $recordId = null)
    {
        \DB::table('audit_log')->insert([
            'user_id'      => auth()->id(),
            'action_type' => $actionType,
            'description' => $description,
            'table_name'  => $tableName,
            'record_id'   => $recordId,
            'created_at'  => now(),
        ]);
    }


}
