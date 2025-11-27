<?php
require 'config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

// شرط التخويل (Authentication)
if (!$user_id) {
    echo json_encode(['error'=>'not_authenticated']);
    exit;
}

$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// ---------- GET_USERS ----------
if ($action === 'get_users') {
    $q = $mysqli->query("SELECT id, name, phone, profile_image FROM users WHERE id != {$user_id} ORDER BY name ASC");
    $arr=[];
    while($r=$q->fetch_assoc()) $arr[]=$r;
    echo json_encode($arr); exit;
}

// ---------- CREATE_CHAT ----------
if ($action === 'create_chat' && $_SERVER['REQUEST_METHOD']==='POST') {
    $target_user_id = (int)$_POST['target_user_id'];
    $check_q = $mysqli->query("
        SELECT cp1.chat_id
        FROM chat_participants cp1
        JOIN chat_participants cp2 ON cp1.chat_id = cp2.chat_id
        JOIN chats c ON c.id = cp1.chat_id
        WHERE cp1.user_id = {$user_id} AND cp2.user_id = {$target_user_id} AND c.is_group = 0
        LIMIT 1
    ");
    if ($check_q->num_rows > 0) {
        $existing_chat = $check_q->fetch_assoc();
        echo json_encode(['error' => 'chat_exists', 'chat_id' => $existing_chat['chat_id']]);
        exit;
    }
    $mysqli->query("INSERT INTO chats (title, is_group) VALUES ('الدردشة الثنائية', 0)");
    $chat_id = $mysqli->insert_id;
    $mysqli->query("INSERT INTO chat_participants (chat_id, user_id) VALUES ({$chat_id}, {$user_id})");
    $mysqli->query("INSERT INTO chat_participants (chat_id, user_id) VALUES ({$chat_id}, {$target_user_id})");
    echo json_encode(['chat_id'=>$chat_id]); exit;
}

// ---------- GET_CHATS (with unread counts) ----------
if ($action === 'get_chats') {
    $q = $mysqli->query("SELECT c.id, c.title, c.is_group,
        (SELECT content FROM messages m WHERE m.chat_id=c.id ORDER BY m.id DESC LIMIT 1) AS last_msg,
        (SELECT MAX(sent_at) FROM messages m2 WHERE m2.chat_id=c.id) AS last_time,
        (SELECT COUNT(*) FROM messages m3 WHERE m3.chat_id=c.id AND m3.sender_id != {$user_id} AND m3.seen = 0) AS unread_count
        FROM chats c
        JOIN chat_participants cp ON cp.chat_id=c.id
        WHERE cp.user_id={$user_id}
        ORDER BY last_time DESC, c.id DESC
    ");
    $arr=[];
    while($r=$q->fetch_assoc()) {
        if ($r['is_group'] == 0 && $r['title'] === 'الدردشة الثنائية') {
            $partner_q = $mysqli->query("SELECT u.name, u.profile_image FROM users u 
                                        JOIN chat_participants cp ON cp.user_id = u.id 
                                        WHERE cp.chat_id = {$r['id']} AND u.id != {$user_id} LIMIT 1");
            if ($partner_row = $partner_q->fetch_assoc()) {
                $r['title'] = $partner_row['name'];
                $r['profile_image'] = $partner_row['profile_image'] ?? null;
            }
        }
        $arr[]=$r;
    }
    echo json_encode($arr); exit;
}

// ---------- GET_MESSAGES ----------
if ($action === 'get_messages') {
    $chat_id = (int)$_GET['chat_id'];
    $q = $mysqli->query("SELECT m.*, u.name, u.profile_image FROM messages m JOIN users u ON u.id=m.sender_id WHERE m.chat_id={$chat_id} ORDER BY m.id ASC");
    $arr=[]; while($r=$q->fetch_assoc()) $arr[]=$r;
    echo json_encode($arr); exit;
}

// ---------- MARK_SEEN (عند فتح الدردشة) ----------
if ($action === 'mark_seen' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat_id = (int)$_POST['chat_id'];
    $mysqli->query("UPDATE messages SET seen = 1 WHERE chat_id = {$chat_id} AND sender_id != {$user_id}");
    echo json_encode(['ok'=>true]); exit;
}

// ---------- SEND_MESSAGE ----------
if ($action === 'send_message' && $_SERVER['REQUEST_METHOD']==='POST') {
    $chat_id = (int)$_POST['chat_id'];
    $content = null;
    $type = $mysqli->real_escape_string($_POST['type'] ?? 'text');
    $file_path = null;

    if ($type === 'text') {
        $content = $mysqli->real_escape_string($_POST['content'] ?? '');
        if (empty($content)) { echo json_encode(['error'=>'empty_content']); exit; }
    } elseif ($type === 'audio' || $type === 'file') {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error'=>'file_upload_failed']); exit;
        }
        $file_name = uniqid('file_') . '_' . basename($_FILES['file']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
            $content = $mysqli->real_escape_string($file_path);
        } else {
            echo json_encode(['error'=>'file_move_failed']); exit;
        }
    } else {
        echo json_encode(['error'=>'invalid_message_type']); exit;
    }

    $mysqli->query("INSERT INTO messages (chat_id, sender_id, content, type, seen) VALUES ({$chat_id}, {$user_id}, '{$content}', '{$type}', 0)");
    $new_msg_id = $mysqli->insert_id;
    echo json_encode(['ok'=>true, 'new_msg_id' => $new_msg_id, 'file_path' => $file_path]); exit;
}

// ---------- DELETE_MESSAGE ----------
if ($action === 'delete_message' && $_SERVER['REQUEST_METHOD']==='POST') {
    $msg_id = (int)$_POST['msg_id'];
    $check_q = $mysqli->query("SELECT id, content, type FROM messages WHERE id = {$msg_id} AND sender_id = {$user_id}");
    if ($check_q->num_rows > 0) {
        $msg_row = $check_q->fetch_assoc();
        if (($msg_row['type'] === 'file' || $msg_row['type'] === 'audio') && file_exists($msg_row['content'])) {
            @unlink($msg_row['content']);
        }
        $mysqli->query("DELETE FROM messages WHERE id = {$msg_id}");
        echo json_encode(['ok'=>true]);
    } else {
        echo json_encode(['error'=>'unauthorized_or_not_found']);
    }
    exit;
}

// ---------- EDIT_MESSAGE ----------
if ($action === 'edit_message' && $_SERVER['REQUEST_METHOD']==='POST') {
    $msg_id = (int)$_POST['msg_id'];
    $new_content = $mysqli->real_escape_string($_POST['new_content'] ?? '');
    if (empty($new_content)) {
        echo json_encode(['error'=>'empty_content']); exit;
    }
    $check_q = $mysqli->query("SELECT id FROM messages WHERE id = {$msg_id} AND sender_id = {$user_id} AND type='text'");
    if ($check_q->num_rows > 0) {
        $mysqli->query("UPDATE messages SET content = '{$new_content}', is_edited = 1 WHERE id = {$msg_id}");
        echo json_encode(['ok'=>true]);
    } else {
        echo json_encode(['error'=>'unauthorized_or_invalid_type']);
    }
    exit;
}

// ---------- DELETE_CHAT ----------
if ($action === 'delete_chat' && $_SERVER['REQUEST_METHOD']==='POST') {
    $chat_id = (int)$_POST['chat_id'];
    $check_q = $mysqli->query("SELECT * FROM chat_participants WHERE chat_id = {$chat_id} AND user_id = {$user_id}");
    if ($check_q->num_rows > 0) {
        // حذف الملفات المرفقة (حذف آمن)
        $res = $mysqli->query("SELECT content, type FROM messages WHERE chat_id = {$chat_id}");
        while($r = $res->fetch_assoc()){
            if (($r['type'] === 'file' || $r['type'] === 'audio') && file_exists($r['content'])) {
                @unlink($r['content']);
            }
        }
        $mysqli->query("DELETE FROM messages WHERE chat_id = {$chat_id}");
        $mysqli->query("DELETE FROM chat_participants WHERE chat_id = {$chat_id}");
        $mysqli->query("DELETE FROM chats WHERE id = {$chat_id}");
        echo json_encode(['ok'=>true]);
    } else {
        echo json_encode(['error'=>'unauthorized_or_not_found']);
    }
    exit;
}

echo json_encode(['error'=>'unknown_action']);
