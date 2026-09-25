<?php
header('Content-Type: application/json');
require_once '../includes/config.php';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC LIMIT 50");
            $stats = $pdo->query("SELECT COALESCE(SUM(amount),0) as total, COUNT(*) as cnt FROM expenses WHERE MONTH(expense_date)=MONTH(CURDATE())")->fetch();
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll(), 'monthly_total' => $stats['total'], 'count' => $stats['cnt']]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true) ?: $_POST;
            $required = ['title','category','amount','expense_date'];
            foreach ($required as $f) {
                if (empty($data[$f])) { echo json_encode(['success'=>false,'message'=>"Field '$f' is required."]); exit; }
            }
            $stmt = $pdo->prepare("INSERT INTO expenses (title,category,amount,payment_method,notes,status,expense_date) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$data['title'],$data['category'],$data['amount'],$data['payment_method']??'Cash',$data['notes']??null,$data['status']??'Paid',$data['expense_date']]);
            echo json_encode(['success'=>true,'message'=>'Expense recorded successfully!','id'=>$pdo->lastInsertId()]);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) { echo json_encode(['success'=>false,'message'=>'ID required.']); exit; }
            $pdo->prepare("DELETE FROM expenses WHERE id=?")->execute([$id]);
            echo json_encode(['success'=>true,'message'=>'Expense deleted.']);
            break;

        default:
            echo json_encode(['success'=>false,'message'=>'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}
