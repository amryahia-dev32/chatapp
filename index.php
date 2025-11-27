<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}
$current_user_id = $_SESSION['user_id'];
$current_user_name = $_SESSION['user_name'];
$user_initial = htmlspecialchars(substr($current_user_name,0,1));
// مسار افتراضي، سيتم استبداله بمسار محفوظ في الجافاسكربت
$user_profile_img = 'assets/img/profile_placeholder.jpg'; 
$JS_CURRENT_USER_ID = json_encode(intval($current_user_id));
$JS_CURRENT_USER_NAME = json_encode($current_user_name);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>واتساب ويب — تجريبي</title>
    <link rel="icon" type="image/jpg" href="https://www.emaratalyoum.com/polopoly_fs/1.1442684.1610536754!/image/image.jpg">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
/* === RESET & ROOT === */
:root{
    --bg: #f0f2f5; 
    --panel: #ffffff; 
    --muted: #66757f; 
    --accent: #25D366;
    --dark: #0b1418; 
    --chat-left: #fff; 
    --chat-right: #dcf8c6;
    --header-border: #eef2f3;
    --search-bg: #fbfdfe;
    --search-border: #e6eef2;
    --chat-hover: #f4f7f8;
    --chat-active: #e6f7ee;
    --sidebar-bg: #202c33;
    --sidebar-icon: #9aa7ad;
    --sidebar-icon-hover: #00a884;
    --chat-bg-gradient: linear-gradient(180deg,#f7fbf8, #f0faf5);
    --app-padding: 12px;
}

/* === DARK MODE VARIABLES === */
.dark-mode{
    --bg: #111b21;
    --panel: #202c33;
    --muted: #8696a0;
    --accent: #00a884;
    --dark: #e9edef;
    --chat-left: #2a3942;
    --chat-right: #005c4b;
    --header-border: #111b21;
    --search-bg: #2a3942;
    --search-border: #33444e;
    --chat-hover: #2a3942;
    --chat-active: #3a4c55;
    --sidebar-bg: #111b21;
    --sidebar-icon: #8696a0;
    --sidebar-icon-hover: #00a884;
    --chat-bg-gradient: #0b1418;
}

/* === GENERAL RESET & BODY === */
*{box-sizing:border-box;font-family: 'Segoe UI','Helvetica Neue',Arial, 'Noto Kufi Arabic', sans-serif;}
html,body{height:100%;margin:0;background:var(--bg);color:var(--dark);}

/* === LAYOUT === */
.app{
    display:flex;
    height:100vh;
    gap:var(--app-padding);
    padding:var(--app-padding);
    background-color: var(--bg);
    background-size: cover;
    background-position: center;
    transition: background-color 0.3s;
}
.sidebar{
    width:72px;
    background:var(--sidebar-bg);
    border-radius:10px;
    padding:12px 0;
    display:flex;
    flex-direction:column;
    gap:12px;
    align-items:center;
    color:var(--sidebar-icon);
    transition: background-color 0.3s;
}
.left-panel{
    width:360px;
    background:var(--panel);
    border-radius:10px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    box-shadow:0 2px 6px rgba(0,0,0,0.08);
    transition: background-color 0.3s;
}
.header-left{
    height:64px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 12px;
    border-bottom:1px solid var(--header-border);
}
.profile-info img.user-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    background: #ccd7db;
    cursor: pointer;
}
.search{padding:10px;border-bottom:1px solid var(--header-border)}
.search input{
    width:100%;
    padding:10px;
    border-radius:999px;
    border:1px solid var(--search-border);
    background:var(--search-bg);
    color:var(--dark);
}
.chat-list{overflow:auto;flex:1;background:transparent;padding:8px}
.chat-item{display:flex;align-items:center;gap:12px;padding:10px;border-radius:8px;cursor:pointer;transition:background .12s}
.chat-item:hover{background:var(--chat-hover)}
.chat-item.active{background:var(--chat-active)}
.chat-item .avatar{
    width:48px;height:48px;border-radius:50%;
    background:#cfd8dc;display:flex;align-items:center;justify-content:center;
    color:#fff;font-weight:700;flex-shrink:0;
    object-fit: cover;
}
.chat-item .meta{flex:1;display:flex;flex-direction:column;min-width:0}
.chat-item .meta .title{font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; color:var(--dark);}
.chat-item .meta .last{font-size:13px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.unread-badge{background:var(--accent);color:#fff;min-width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;border-radius:50%;font-size:12px;padding:0 6px;margin-inline-start:8px}

/* === CHAT AREA === */
.chat-area{
    flex:1;background:var(--panel);
    border-radius:10px;display:flex;flex-direction:column;
    overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,0.08);
    transition: background-color 0.3s;
}
.chat-header{height:70px;display:flex;align-items:center;gap:12px;padding:12px;border-bottom:1px solid var(--header-border)}
.chat-main{flex:1;display:flex;gap:0;overflow:hidden} /* إزالة الفجوة هنا */

/* تحديث منطقة الرسائل لتشغل المساحة بالكامل افتراضياً */
.messages{flex:1;padding:18px;overflow:auto;background:var(--chat-bg-gradient)}

.message{max-width:72%;padding:10px 12px;border-radius:12px;margin-bottom:10px;position:relative;word-wrap:break-word;line-height:1.35; color:var(--dark);}
.message.me{margin-left:auto;background:var(--chat-right)}
.message.other{margin-right:auto;background:var(--chat-left)}
.msg-meta{display:flex;align-items:center;gap:8px;margin-top:6px}
.msg-time{font-size:11px;color:var(--muted)}
.msg-edited{font-size:11px;color:var(--muted);margin-inline-start:6px}
.msg-menu-btn{position:absolute;top:8px;opacity:0;color:var(--muted);cursor:pointer}
.message:hover .msg-menu-btn{opacity:1}
.message.me .msg-menu-btn{left:-28px}
.message.other .msg-menu-btn{right:-28px}
.msg-menu{position:absolute;top:8px;background:var(--panel);border-radius:6px;box-shadow:0 6px 18px rgba(0,0,0,0.08);display:none;min-width:150px;z-index:50; color:var(--dark);}
.msg-menu div{padding:8px 12px;cursor:pointer;font-size:14px}
.msg-menu div:hover{background:var(--chat-hover)}
.input-area{display:flex;align-items:center;padding:12px;border-top:1px solid var(--header-border);gap:8px}
.input-area input[type="text"]{
    flex:1;padding:12px;border-radius:999px;
    border:1px solid var(--search-border);
    background:var(--search-bg);
    color:var(--dark);
}
.btn{background:var(--accent);color:#fff;padding:10px 14px;border-radius:999px;border:none;cursor:pointer;font-weight:700}
.media-btn{background:var(--panel);border:1px solid var(--search-border);padding:10px;border-radius:8px;cursor:pointer; color:var(--muted);}
.chat-options-btn{cursor:pointer;color:var(--muted);padding-inline:6px}
.chat-menu{position:absolute;background:var(--panel);border-radius:6px;box-shadow:0 6px 18px rgba(0,0,0,0.08);display:none;min-width:160px;right:-100px;top:70px;z-index:60; color:var(--dark);}
.chat-menu div{padding:10px 12px;cursor:pointer}
.chat-menu div:hover{background:var(--chat-hover)}

/* إخفاء لوحة المعلومات اليمنى افتراضياً وتصغير مساحتها */
.right-panel{
    width:0;
    opacity:0;
    padding:0;
    background:var(--panel);
    border-radius:10px;
    display:flex;
    flex-direction:column;gap:12px;
    overflow-y:auto;
    transition: all 0.2s ease-in-out; 
}
/* تنسيق ظهور لوحة المعلومات اليمنى */
.right-panel.active{
    width:320px; 
    opacity:1;
    padding:12px;
    border-inline-start: 1px solid var(--header-border);
}
/* جعل منطقة الرسائل تتوسع أو تنكمش عند ظهور/اختفاء لوحة المعلومات */
.chat-main.right-panel-active .messages {
    flex: 1; 
    padding-inline-end: 18px; 
}

/* تنسيق صورة الدردشة لإضافة مدخل الملف */
.chat-avatar-container {
    position: relative;
    cursor: pointer;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    overflow: hidden;
}
.chat-avatar-container .avatar {
    width: 100%;
    height: 100%;
    font-size: 18px;
    display: flex; /* لضمان توسيط الحرف */
    align-items: center;
    justify-content: center;
}
/* === LEFT PANEL SECTIONS (Status, Communities, Settings) === */
.section-content {
    flex: 1;
    overflow-y: auto;
    padding: 12px;
    display: none; 
}
.section-content.active {
    display: block;
}
.section-content h3 {
    color: var(--dark);
    border-bottom: 1px solid var(--header-border);
    padding-bottom: 10px;
    margin-top: 0;
}
/* تنسيق الإعدادات */
.settings-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--header-border);
}
.settings-option:last-child {
    border-bottom: none;
}
.settings-option label {
    font-weight: 600;
}
/* الوضع الداكن - زر التبديل */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}
.switch input {opacity: 0; width: 0; height: 0;}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}
input:checked + .slider {
  background-color: var(--accent);
}
input:checked + .slider:before {
  transform: translateX(26px);
}


/* small screens */
@media(max-width:900px){
    .left-panel{width:320px}
    .sidebar{display:none}
    .right-panel{display:none}
}
/* Sidebar icons */
.icon{width:50px;height:50px;margin:15px 0;display:flex;align-items:center;justify-content:center;border-radius:50%;font-size:22px;color:var(--sidebar-icon);cursor:pointer; transition: background 0.2s, color 0.2s;}
.icon:hover, .icon.active{background:#2a3942;color:var(--sidebar-icon-hover);}

/* Floating action button and menu */
.fab{position:fixed;bottom:20px;right:calc(var(--app-padding) + 20px);background:var(--accent);width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;cursor:pointer;color:#fff;box-shadow:0 3px 6px rgba(0,0,0,.3);z-index:20; transition: background-color 0.3s;}
.fab-menu{position:fixed;bottom:90px;right:calc(var(--app-padding) + 20px);background:var(--sidebar-bg);padding:10px;border-radius:10px;display:none;flex-direction:column;gap:10px;z-index:20;min-width:200px; color:var(--dark); transition: background-color 0.3s;}
.fab-menu div{padding:8px 12px;border-radius:6px;background:#2a3942;cursor:pointer;text-align:right; color:#e9edef;}
.fab-menu div:hover{background:#3a4c55;}
 
.main > div:not(.main-header, .messages, .input-area){ display: none; }
 
#fileUpload{display:none;}
#chatImageUpload{display:none;}
</style>
</head>
<body>
    <div class="fab" onclick="toggleFabMenu()"><i class="fa-solid fa-plus"></i></div>
    <div class="fab-menu" id="fabMenu">
        </div>
    
    <audio id="newMsgSound" src="assets/sounds/new_msg.mp3" preload="auto"></audio>

<div class="app" id="mainApp">
    <div class="sidebar" aria-hidden="true">
        <div class="icon active" title="الدردشات" data-section="chats" onclick="showSection('chats')"><i class="fa-solid fa-message"></i></div>
        <div class="icon" title="الحالة" data-section="status" onclick="showSection('status')"><i class="fa-solid fa-circle-notch"></i></div>
        <div class="icon" title="المجتمعات" data-section="communities" onclick="showSection('communities')"><i class="fa-solid fa-users"></i></div>
        <div class="icon" title="الإعدادات" data-section="settings" onclick="showSection('settings')"><i class="fa-solid fa-gear"></i></div> 
    </div>

    <div class="left-panel">
        <div class="header-left">
            <div class="profile-info" style="display:flex;align-items:center;gap:12px">
                <img src="<?php echo htmlspecialchars($user_profile_img); ?>" alt="صورة المستخدم" class="user-avatar" id="currentUserAvatar" title="صورة المستخدم الحالي" onclick="document.getElementById('profileImageUpload').click()">
                <input type="file" id="profileImageUpload" style="display:none" accept="image/*" onchange="setProfileImage(event)">
                
                <div style="display:flex;flex-direction:column">
                    <strong><?php echo htmlspecialchars($current_user_name); ?></strong>
                    <small style="color:var(--muted)">متصل الآن</small>
                </div>
            </div>
            <div style="position:relative">
                <i class="fa-solid fa-ellipsis-vertical chat-options-btn" id="globalChatOptionsBtn"></i>
                <div class="chat-menu" id="globalChatMenu" style="display:none">
                    <div onclick="window.location.href='logout.php'">تسجيل الخروج</div>
                    <div onclick="alert('Feature: Archive (soon)')">أرشفة الكل</div>
                    <div onclick="showSection('settings'); document.getElementById('globalChatMenu').style.display='none';">الإعدادات</div>
                </div>
            </div>
        </div>

        <div class="search" id="leftPanelSearch">
            <input id="searchInput" placeholder="ابحث أو ابدأ محادثة جديدة">
        </div>

        <div class="section-content active" id="chats">
            <div class="chat-list" id="chatList">
                <div style="text-align:center; color:var(--muted); padding:20px;">جاري تحميل الدردشات...</div>
            </div>
        </div>

        <div class="section-content" id="status">
            <h3><i class="fa-solid fa-circle-notch"></i> الحالة</h3>
            <p style="color:var(--muted)">لا توجد تحديثات حالة لعرضها في الوقت الحالي.</p>
            <button class="btn" style="width:100%; margin-top:10px;" onclick="addStatus()">إضافة حالتي</button>
        </div>

        <div class="section-content" id="communities">
            <h3><i class="fa-solid fa-users"></i> المجتمعات</h3>
            <p style="color:var(--muted)">قدم واتساب المجتمعات لجمع المجموعات ذات الصلة معًا.</p>
            <button class="btn" style="width:100%; margin-top:10px;" onclick="createCommunity()">إنشاء مجتمع جديد</button>
        </div>

        <div class="section-content" id="settings">
            <h3><i class="fa-solid fa-gear"></i> الإعدادات</h3>
            
            <div class="settings-option">
                <label for="darkModeSwitch">الوضع الداكن</label>
                <label class="switch">
                    <input type="checkbox" id="darkModeSwitch" onchange="toggleDarkMode(this.checked)">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="settings-option">
                <label for="backgroundColorPicker">لون الخلفية</label>
                <input type="color" id="backgroundColorPicker" value="#f0f2f5" onchange="changeAppBackground('color', this.value)">
            </div>
            
            <div class="settings-option" style="flex-direction: column; align-items: flex-start;">
                <label>صورة الخلفية</label>
                <div style="margin-top: 10px; display: flex; gap: 8px;">
                    <button class="media-btn" onclick="document.getElementById('bgImageUpload').click()">اختيار صورة</button>
                    <button class="media-btn" onclick="changeAppBackground('none', '')">إزالة</button>
                    <input type="file" id="bgImageUpload" style="display:none" accept="image/*" onchange="uploadAppBackgroundImage(event)">
                </div>
            </div>

            <div class="settings-option">
                <label>تسجيل الخروج</label>
                <button class="media-btn" onclick="window.location.href='logout.php'"><i class="fa-solid fa-right-from-bracket"></i></button>
            </div>
        </div>
        </div>

    <div class="chat-area">
        <div class="chat-header" id="chatHeader">
            <div style="display:flex;align-items:center;gap:12px">
                <div class="chat-avatar-container" onclick="if(currentChatId) document.getElementById('chatImageUpload').click()">
                    <div class="avatar" id="chatAvatar">م</div>
                    <input type="file" id="chatImageUpload" accept="image/*" onchange="setChatImage(event)" style="display:none">
                </div>

                <div style="display:flex;flex-direction:column">
                    <span id="chatTitle">اختر محادثة</span>
                    <small id="chatSubtitle" style="color:var(--muted)">آخر ظهور: -</small>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;position:relative">
                <i class="fa-solid fa-phone chat-options-btn" title="اتصال صوتي"></i>
                <i class="fa-solid fa-video chat-options-btn" title="مكالمة فيديو"></i>
                <i class="fa-solid fa-info-circle chat-options-btn" id="toggleRightPanelBtn" title="معلومات الدردشة" onclick="toggleRightPanel()"></i> 
                <i class="fa-solid fa-ellipsis-vertical chat-options-btn" id="chatOptionsBtn"></i>
                <div class="chat-menu" id="chatOptionsMenu" style="display:none">
                    <div onclick="renameChat(currentChatId)">إعادة تسمية</div>
                    <div onclick="deleteChat(currentChatId)">حذف الدردشة</div>
                    <div onclick="shareChat(currentChatId)">مشاركة</div>
                </div>
            </div>
        </div>

        <div class="chat-main" id="chatMain">
            <div class="messages" id="messages">
                <div style="text-align:center;color:var(--muted);padding:40px">مرحباً <strong><?php echo htmlspecialchars($current_user_name); ?></strong> — اختر محادثة للبدء</div>
            </div>

            <div class="right-panel" id="rightPanel">
                <div style="text-align:center">
                    <div style="width:80px;height:80px;border-radius:50%;background:#cfd8dc;margin:0 auto;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:28px" id="rightAvatar">م</div>
                    <h3 id="rightTitle">المعلومات</h3>
                    <p id="rightSubtitle" style="color:var(--muted)">اختر دردشة لعرض التفاصيل</p>
                </div>
                <hr>
                <div>
                    <strong>خيارات المحادثة</strong>
                    <div style="margin-top:8px">
                        <button class="media-btn" onclick="renameChat(currentChatId)">إعادة تسمية</button>
                        <button class="media-btn" onclick="deleteChat(currentChatId)">حذف</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="input-area">
            <label for="fileUpload" class="media-btn" title="أرفق ملف"><i class="fa-solid fa-paperclip"></i></label>
            <input type="file" id="fileUpload" style="display:none" onchange="uploadFile()" />
            <button class="media-btn" id="voiceBtn" title="تسجيل صوتي" onclick="toggleRecording()"><i class="fa-solid fa-microphone"></i></button>
            <input type="text" id="msgInput" placeholder="اكتب رسالة..." />
            <button class="btn" id="sendBtn" onclick="sendMessage()">إرسال</button>
        </div>
    </div>

</div>


<script>
// ====== إعدادات بداية (من PHP) ======
const CURRENT_USER_ID = <?php echo $JS_CURRENT_USER_ID; ?>;
const CURRENT_USER_NAME = <?php echo $JS_CURRENT_USER_NAME; ?>;
const DEFAULT_PROFILE_IMG = '<?php echo $user_profile_img; ?>';
const CHAT_IMAGE_STORAGE_KEY = 'chatImages'; // مفتاح تخزين صور الدردشات

// ====== متغيرات عامة ======
let currentChatId = null;
let currentChatTitle = 'اختر محادثة'; // تم تعيين قيمة ابتدائية
let pollingInterval = 2000;
let lastMsgIdByChat = {}; 
let soundNew = document.getElementById('newMsgSound');
let mediaRecorder = null, audioChunks = [];
const mainApp = document.getElementById('mainApp');
const allSections = document.querySelectorAll('.section-content');
const allSidebarIcons = document.querySelectorAll('.sidebar .icon');
const rightPanel = document.getElementById('rightPanel');
const chatMain = document.getElementById('chatMain');

// ----------------------------------------------------
// ====== الدوال الجديدة للتحكم في الواجهة والإعدادات ======
// ----------------------------------------------------

/**
 * دالة: تبديل عرض لوحة المعلومات اليمنى
 */
function toggleRightPanel() {
    if (!currentChatId) {
        alert("يجب اختيار دردشة أولاً لعرض معلوماتها.");
        return;
    }
    rightPanel.classList.toggle('active');
    chatMain.classList.toggle('right-panel-active');
    
    // حفظ حالة الإخفاء/الإظهار للدردشة الحالية
    let visibility = localStorage.getItem('rightPanelVisibility') || '{}';
    try { visibility = JSON.parse(visibility); } catch(e) { visibility = {}; }
    
    if (rightPanel.classList.contains('active')) {
        visibility[currentChatId] = true;
    } else {
        delete visibility[currentChatId];
    }
    
    localStorage.setItem('rightPanelVisibility', JSON.stringify(visibility));
}

/**
 * تحديث صورة الملف الشخصي للمستخدم وحفظها في التخزين المحلي.
 */
function setProfileImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const imageUrl = e.target.result;
        const avatarElement = document.getElementById('currentUserAvatar');
        
        avatarElement.src = imageUrl;
        localStorage.setItem('userProfileImage', imageUrl);
        event.target.value = null; 
    };
    reader.readAsDataURL(file);
}

/**
 * دالة: تغيير صورة الدردشة الحالية وحفظها محليًا.
 */
function setChatImage(event) {
    if (!currentChatId) return alert("يجب اختيار دردشة أولاً لتغيير صورتها.");

    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const imageUrl = e.target.result;
        
        const chatAvatarDiv = document.getElementById('chatAvatar');
        chatAvatarDiv.style.backgroundImage = `url('${imageUrl}')`;
        chatAvatarDiv.style.backgroundSize = 'cover';
        chatAvatarDiv.textContent = ''; 

        const rightAvatarDiv = document.getElementById('rightAvatar');
        rightAvatarDiv.style.backgroundImage = `url('${imageUrl}')`;
        rightAvatarDiv.style.backgroundSize = 'cover';
        rightAvatarDiv.textContent = '';

        let chatImages = localStorage.getItem(CHAT_IMAGE_STORAGE_KEY) || '{}';
        try { chatImages = JSON.parse(chatImages); } catch(e) { chatImages = {}; }
        chatImages[currentChatId] = imageUrl;
        localStorage.setItem(CHAT_IMAGE_STORAGE_KEY, JSON.stringify(chatImages));

        const listItem = document.querySelector(`.chat-item[data-chat-id="${currentChatId}"] .avatar`);
        if (listItem) {
            listItem.style.backgroundImage = `url('${imageUrl}')`;
            listItem.style.backgroundSize = 'cover';
            listItem.textContent = '';
            listItem.style.background = 'none';
        }
    };
    reader.readAsDataURL(file);
}

// 1. التبديل بين الأقسام (الدردشات، الحالة، المجتمعات، الإعدادات)
function showSection(sectionId) {
    allSections.forEach(section => section.classList.remove('active'));
    allSidebarIcons.forEach(icon => icon.classList.remove('active'));

    const targetSection = document.getElementById(sectionId);
    const targetIcon = document.querySelector(`.sidebar .icon[data-section="${sectionId}"]`);
    const searchBar = document.getElementById('leftPanelSearch');
    
    if (targetSection) {
        targetSection.classList.add('active');
    }
    if (targetIcon) {
        targetIcon.classList.add('active');
    }

    if (sectionId === 'chats') {
        searchBar.style.display = 'block';
    } else {
        searchBar.style.display = 'none';
    }
}

// 2. التحكم في الوضع الداكن
function toggleDarkMode(isDark) {
    if (isDark) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'false');
    }
}

// 3. التحكم في خلفية التطبيق (لون أو صورة)
function changeAppBackground(type, value) {
    if (type === 'color') {
        mainApp.style.backgroundImage = 'none';
        mainApp.style.backgroundColor = value;
        localStorage.setItem('appBackground', JSON.stringify({type: 'color', value: value}));
    } else if (type === 'image') {
        mainApp.style.backgroundImage = `url('${value}')`;
        mainApp.style.backgroundColor = 'transparent'; 
        localStorage.setItem('appBackground', JSON.stringify({type: 'image', value: value}));
    } else if (type === 'none') {
        mainApp.style.backgroundImage = 'none';
        mainApp.style.backgroundColor = 'var(--bg)'; 
        localStorage.removeItem('appBackground');
    }
}

// 4. رفع صورة للخلفية
function uploadAppBackgroundImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        changeAppBackground('image', e.target.result);
    };
    reader.readAsDataURL(file);
}

// 5. تطبيق الإعدادات المحفوظة عند التحميل
function applySavedSettings() {
    const savedProfileImage = localStorage.getItem('userProfileImage');
    if (savedProfileImage) {
        document.getElementById('currentUserAvatar').src = savedProfileImage;
    } else {
        document.getElementById('currentUserAvatar').src = DEFAULT_PROFILE_IMG;
    }

    const isDark = localStorage.getItem('darkMode') === 'true';
    document.getElementById('darkModeSwitch').checked = isDark;
    toggleDarkMode(isDark);

    const savedBg = localStorage.getItem('appBackground');
    if (savedBg) {
        try {
            const bg = JSON.parse(savedBg);
            if (bg.type === 'color') {
                document.getElementById('backgroundColorPicker').value = bg.value;
                changeAppBackground('color', bg.value);
            } else if (bg.type === 'image') {
                changeAppBackground('image', bg.value);
            }
        } catch(e) {
            console.error('Error applying saved background:', e);
            localStorage.removeItem('appBackground'); 
        }
    }
    
    showSection('chats');
}

// 6. دالة وهمية: إضافة حالة جديدة.
function addStatus() {
    const statusText = prompt("اكتب نص الحالة الجديدة:");
    if (statusText) {
        alert(`تم محاولة إضافة الحالة: "${statusText}". (تحتاج ربط API)`);
    }
}

// 7. دالة وهمية: إنشاء مجتمع جديد.
function createCommunity() {
    const communityName = prompt("أدخل اسم المجتمع الجديد:");
    if (communityName) {
        alert(`تم محاولة إنشاء المجتمع: "${communityName}". (تحتاج ربط API)`);
    }
}

// ----------------------------------------------------
// ====== الدوال الأساسية (التي لا تحتاج لتغيير) ======
// ----------------------------------------------------
function formatTime(ts){
    if(!ts) return '';
    const d = new Date(ts);
    const hh = String(d.getHours()).padStart(2,'0');
    const mm = String(d.getMinutes()).padStart(2,'0');
    return `${hh}:${mm}`;
}
function escapeHtml(s){ return s ? s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') : ''; }
function escapeJs(s){ return String(s||'').replace(/'/g,"\\'").replace(/"/g,'\"'); }

// ====== الأحداث العامة للـ menus ======
document.getElementById('globalChatOptionsBtn').addEventListener('click', (e)=>{
    const m = document.getElementById('globalChatMenu');
    m.style.display = (m.style.display === 'block' ? 'none' : 'block');
});
document.getElementById('chatOptionsBtn').addEventListener('click', (e)=>{
    if(!currentChatId) return;
    const m = document.getElementById('chatOptionsMenu');
    m.style.display = (m.style.display === 'block' ? 'none' : 'block');
});
document.addEventListener('click', (e)=>{
    if(!e.target.closest('.chat-options-btn') && !e.target.closest('.chat-menu')) {
        document.querySelectorAll('.chat-menu').forEach(x=>x.style.display='none');
    }
    if(!e.target.closest('.msg-menu-btn') && !e.target.closest('.msg-menu')) {
        document.querySelectorAll('.msg-menu').forEach(x=>x.style.display='none');
    }
});

// ====== جلب الدردشات و renderChats و openChat ... ======
async function fetchChats(){
    try {
        const r = await fetch('api.php?action=get_chats');
        const chats = await r.json();
        renderChats(chats);
    } catch(e){ 
        console.error('fetchChats failed:', e); 
        document.getElementById('chatList').innerHTML = '<div style="text-align:center; color:red; padding:20px;">فشل جلب الدردشات. تأكد من عمل api.php.</div>';
    }
}

function renderChats(chats){
    const list = document.getElementById('chatList');
    list.innerHTML = '';
    
    if (!Array.isArray(chats) || chats.length === 0) {
        list.innerHTML = '<div style="text-align:center; color:var(--muted); padding:20px;">لا توجد محادثات لعرضها.</div>';
        return;
    }

    let chatImages = localStorage.getItem(CHAT_IMAGE_STORAGE_KEY) || '{}';
    try { chatImages = JSON.parse(chatImages); } catch(e) { chatImages = {}; }

    for(const c of chats){
        const div = document.createElement('div');
        div.className = 'chat-item' + (c.id === currentChatId ? ' active' : '');
        div.dataset.chatId = c.id;
        
        const storedImage = chatImages[c.id];
        let avatarStyle = '';
        let avatarContent = escapeHtml((c.title||'دردشة').charAt(0));

        if (storedImage) {
             avatarStyle = `background-image: url('${storedImage}'); background-size: cover; background: none;`;
             avatarContent = ''; 
        } else if (c.avatar_url) {
            avatarStyle = `background-image: url('${escapeHtml(c.avatar_url)}'); background-size: cover; background: none;`;
            avatarContent = '';
        }
        
        div.innerHTML = `
            <div class="avatar" style="${avatarStyle}">${avatarContent}</div>
            <div class="meta">
                <div class="title">${escapeHtml(c.title || 'دردشة')}</div>
                <div class="last">${escapeHtml(c.last_msg || '')}</div>
            </div>
            ${c.unread_count && parseInt(c.unread_count) > 0 ? `<div class="unread-badge">${c.unread_count}</div>` : ''}
        `;
        div.addEventListener('click', ()=> openChat(c.id, c.title));
        list.appendChild(div);
    }
}

async function openChat(id, title){
    // تعيين المتغيرات الرئيسية
    currentChatId = id;
    currentChatTitle = title;

    document.getElementById('chatTitle').textContent = title || 'دردشة';
    
    // تحديث صور الدردشة (في الرأس واللوحة اليمنى)
    const chatAvatarDiv = document.getElementById('chatAvatar');
    const rightAvatarDiv = document.getElementById('rightAvatar');
    const chatImages = JSON.parse(localStorage.getItem(CHAT_IMAGE_STORAGE_KEY) || '{}');
    const storedImage = chatImages[id];
    
    // إعادة تعيين الأسلوب الافتراضي
    chatAvatarDiv.style.backgroundImage = '';
    rightAvatarDiv.style.backgroundImage = '';
    chatAvatarDiv.textContent = (title||'د').charAt(0);
    rightAvatarDiv.textContent = (title||'د').charAt(0);

    if (storedImage) {
        chatAvatarDiv.style.backgroundImage = `url('${storedImage}')`;
        chatAvatarDiv.style.backgroundSize = 'cover';
        chatAvatarDiv.textContent = '';
        rightAvatarDiv.style.backgroundImage = `url('${storedImage}')`;
        rightAvatarDiv.style.backgroundSize = 'cover';
        rightAvatarDiv.textContent = '';
    }

    document.getElementById('rightTitle').textContent = title || 'دردشة';
    document.getElementById('rightSubtitle').textContent = 'قيد التحميل...';

    // تحديث حالة تفعيل الدردشة في القائمة
    document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
    const activeChat = document.querySelector(`.chat-item[data-chat-id="${id}"]`);
    if(activeChat) activeChat.classList.add('active');

    // تطبيق حالة لوحة المعلومات اليمنى المحفوظة لهذه الدردشة
    let visibility = localStorage.getItem('rightPanelVisibility') || '{}';
    try { visibility = JSON.parse(visibility); } catch(e) { visibility = {}; }
    
    if (visibility[id]) {
        rightPanel.classList.add('active');
        chatMain.classList.add('right-panel-active');
    } else {
        rightPanel.classList.remove('active');
        chatMain.classList.remove('right-panel-active');
    }


    await loadMessages(true);

    try {
        await fetch('api.php?action=mark_seen', { method:'POST', body: new URLSearchParams({chat_id: currentChatId})});
        fetchChats();
    } catch(e){ console.warn('mark_seen failed', e); }
}

function addMessageToDOM(msg){
    if(!msg || !msg.id) return false;
    const messagesBox = document.getElementById('messages');
    if (messagesBox.querySelector(`[data-msg-id="${msg.id}"]`)) return false;

    const isMe = (msg.sender_id == CURRENT_USER_ID);
    const wrapper = document.createElement('div');
    wrapper.className = 'message ' + (isMe ? 'me' : 'other');
    wrapper.setAttribute('data-msg-id', msg.id);

    let content = '';
    if (msg.type === 'audio') {
        content = `<audio controls src="${escapeHtml(msg.content)}"></audio>`;
    } else if (msg.type === 'file') {
        const fname = msg.content.split('/').pop();
        content = `<a href="${escapeHtml(msg.content)}" target="_blank">${escapeHtml(fname)}</a>`;
    } else {
        content = escapeHtml(msg.content);
    }

    const edited = (msg.is_edited == 1) ? `<span class="msg-edited">(مُعدل)</span>` : '';

    wrapper.innerHTML = `
        ${!isMe ? `<div style="font-size:12px;color:var(--accent);margin-bottom:6px">${escapeHtml(msg.name||'مستخدم')}</div>` : ''}
        <div class="bubble">
            <div class="text">${content}</div>
            <div class="msg-meta">
                <span class="msg-time">${formatTime(msg.sent_at)}</span>
                ${edited}
            </div>
        </div>
        <i class="fa-solid fa-ellipsis-vertical msg-menu-btn"></i>
        <div class="msg-menu">
            <div onclick="onEditMessage(${msg.id})">تعديل</div>
            <div onclick="onCopyMessage(${msg.id})">نسخ</div>
            <div onclick="onDeleteMessage(${msg.id})">حذف</div>
            <div onclick="onShareMessage(${msg.id})">مشاركة</div>
        </div>
    `;

    const btn = wrapper.querySelector('.msg-menu-btn');
    btn.addEventListener('click', (e)=>{
        e.stopPropagation();
        const menu = wrapper.querySelector('.msg-menu');
        document.querySelectorAll('.msg-menu').forEach(m=>{ if(m!==menu) m.style.display='none'; });
        menu.style.display = (menu.style.display==='block'?'none':'block');
    });

    messagesBox.appendChild(wrapper);
    messagesBox.scrollTop = messagesBox.scrollHeight;
    return true;
}

async function loadMessages(rebuild=false){
    if(!currentChatId) {
        document.getElementById('messages').innerHTML = '<div style="text-align:center;color:var(--muted);padding:40px">الرجاء اختيار محادثة</div>';
        return;
    }
    try {
        const r = await fetch(`api.php?action=get_messages&chat_id=${currentChatId}`);
        const msgs = await r.json();
        
        const container = document.getElementById('messages');
        if(rebuild){
            container.innerHTML = '';
            lastMsgIdByChat[currentChatId] = 0;
        }

        if(!Array.isArray(msgs)) {
            container.innerHTML = `<div style="text-align:center;color:red;padding:40px;">فشل جلب الرسائل. تأكد من أن chat_id=${currentChatId} صحيح وأن API يعمل.</div>`;
            return;
        }

        let newIncoming = 0;
        let lastId = lastMsgIdByChat[currentChatId] || 0;

        for(const m of msgs){
            const added = addMessageToDOM(m);
            if(added && m.id > lastId){
                lastId = Math.max(lastId, m.id);
                if(m.sender_id != CURRENT_USER_ID) newIncoming++;
            }
        }
        lastMsgIdByChat[currentChatId] = lastId;

        if(newIncoming > 0){
            try{ soundNew.play().catch(()=>{}); } catch(e){}
            fetchChats();
        }
    } catch(e){ 
        console.error('loadMessages failed:', e); 
        document.getElementById('messages').innerHTML = '<div style="text-align:center;color:red;padding:40px;">خطأ في الاتصال بالخادم لجلب الرسائل.</div>';
    }
}

async function sendMessage(){
    if(!currentChatId) return alert('اختر محادثة أولاً.');
    const txt = document.getElementById('msgInput').value.trim();
    if(!txt) return;
    const fd = new FormData();
    fd.append('chat_id', currentChatId);
    fd.append('type', 'text');
    fd.append('content', txt);

    try {
        const r = await fetch('api.php?action=send_message', { method:'POST', body: fd });
        const j = await r.json();
        if(j.ok){
            document.getElementById('msgInput').value = '';
            const temp = { id: j.new_msg_id, content: txt, type: 'text', sender_id: CURRENT_USER_ID, name: CURRENT_USER_NAME, sent_at: new Date().toISOString(), is_edited:0 };
            addMessageToDOM(temp);
            fetchChats();
        } else alert('فشل الإرسال: '+(j.error||'خطأ'));
    } catch(e){ console.error(e); alert('خطأ في الإرسال'); }
}

async function uploadFile(){
    if(!currentChatId) return alert('اختر محادثة أولاً.');
    const input = document.getElementById('fileUpload');
    const file = input.files[0];
    if(!file) return;
    const fd = new FormData();
    fd.append('chat_id', currentChatId);
    fd.append('type', 'file');
    fd.append('file', file);

    try {
        const r = await fetch('api.php?action=send_message', { method:'POST', body: fd });
        const j = await r.json();
        if(j.ok){
            const temp = { id: j.new_msg_id, content: j.file_path, type: 'file', sender_id: CURRENT_USER_ID, name: CURRENT_USER_NAME, sent_at: new Date().toISOString(), is_edited:0 };
            addMessageToDOM(temp);
            input.value = '';
            fetchChats();
        } else alert('فشل الرفع: '+(j.error||''));
    } catch(e){ console.error(e); alert('فشل الرفع'); }
}

function toggleRecording(){
    const voiceBtn = document.getElementById('voiceBtn');
    if(mediaRecorder && mediaRecorder.state === 'recording'){
        mediaRecorder.stop();
        voiceBtn.innerHTML = '<i class="fa-solid fa-microphone"></i>';
        voiceBtn.classList.remove('recording');
        return;
    }
    navigator.mediaDevices.getUserMedia({ audio:true }).then(stream=>{
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        mediaRecorder.ondataavailable = (e)=> audioChunks.push(e.data);
        mediaRecorder.onstop = async ()=>{
            const blob = new Blob(audioChunks, { type:'audio/webm' });
            const fd = new FormData();
            fd.append('chat_id', currentChatId);
            fd.append('type', 'audio');
            fd.append('file', blob, 'voice.webm');
            try {
                const r = await fetch('api.php?action=send_message', { method:'POST', body: fd });
                const j = await r.json();
                if(j.ok){
                    const temp = { id: j.new_msg_id, content: j.file_path, type: 'audio', sender_id: CURRENT_USER_ID, name: CURRENT_USER_NAME, sent_at: new Date().toISOString(), is_edited:0 };
                    addMessageToDOM(temp);
                    fetchChats();
                } else alert('فشل رفع الصوت: '+(j.error||''));
            } catch(e){ console.error(e); alert('فشل رفع الصوت'); }
        };
        mediaRecorder.start();
        voiceBtn.innerHTML = '<i class="fa-solid fa-stop"></i>';
        voiceBtn.classList.add('recording');
    }).catch(err=>alert('لا يمكن الوصول إلى الميكروفون: '+err.message));
}

function onCopyMessage(msgId){
    const el = document.querySelector(`[data-msg-id="${msgId}"] .text`);
    if(!el) return;
    const txt = el.textContent || el.innerText || '';
    navigator.clipboard.writeText(txt);
    alert('تم النسخ');
}

function onEditMessage(msgId){
    const el = document.querySelector(`[data-msg-id="${msgId}"] .text`);
    if(!el) return;
    const old = el.textContent || el.innerText || '';
    const nv = prompt('حرّر رسالتك:', old);
    if(nv === null) return;
    fetch('api.php?action=edit_message', { method:'POST', body: new URLSearchParams({ msg_id: msgId, new_content: nv })})
        .then(r=>r.json()).then(res=>{
            if(res.ok){
                el.innerHTML = escapeHtml(nv);
                const editedSpan = document.createElement('span'); editedSpan.className='msg-edited'; editedSpan.textContent='(مُعدل)';
                if(!el.parentElement.querySelector('.msg-edited')) el.parentElement.querySelector('.msg-meta').appendChild(editedSpan);
            } else alert('فشل التعديل: '+(res.error||''));
        }).catch(e=>console.error(e));
}

function onDeleteMessage(msgId){
    if(!confirm('هل تريد حذف هذه الرسالة؟')) return;
    fetch('api.php?action=delete_message', { method:'POST', body: new URLSearchParams({ msg_id: msgId })})
        .then(r=>r.json()).then(res=>{
            if(res.ok){
                const el = document.querySelector(`[data-msg-id="${msgId}"]`);
                if(el) el.remove();
                fetchChats();
            } else alert('فشل الحذف: '+(res.error||''));
        }).catch(e=>console.error(e));
}

function onShareMessage(msgId){
    const el = document.querySelector(`[data-msg-id="${msgId}"] .text`);
    if(!el) return;
    const txt = el.textContent || el.innerText || '';
    if(navigator.share){ navigator.share({ text: txt }).catch(()=>{}); }
    else { navigator.clipboard.writeText(txt); alert('نُسخ النص للمشاركة'); }
}

function renameChat(chatId){
    const nv = prompt('أعد تسمية الدردشة:');
    if(!nv) return;
    alert('خاصية إعادة التسمية غير مفعله على السيرفر الآن.');
}
function deleteChat(chatId){
    if(!confirm('هل تريد حذف هذه الدردشة نهائياً؟')) return;
    fetch('api.php?action=delete_chat', { method:'POST', body: new URLSearchParams({ chat_id: chatId })})
        .then(r=>r.json()).then(res=>{
            if(res.ok){
                if(chatId == currentChatId){
                    document.getElementById('messages').innerHTML = '<div style="text-align:center;color:var(--muted);padding:40px">اختر محادثة للبدء</div>';
                    currentChatId = null;
                    rightPanel.classList.remove('active');
                    chatMain.classList.remove('right-panel-active');
                }
                fetchChats();
            } else alert('فشل حذف الدردشة: '+(res.error||''));
        }).catch(e=>console.error(e));
}
function shareChat(chatId){
    alert('رابط الدردشة تم نسخه (تجريبي).');
}

async function fetchUsersForChat() {
    try {
        const response = await fetch('api.php?action=get_users');
        const users = await response.json();
        if(users.error === 'not_authenticated'){
            console.error('Failed to fetch users: User not authenticated.');
            return [];
        }
        return users;
    } catch (error) {
        console.error('Error fetching users:', error);
        return [];
    }
}

async function toggleFabMenu() {
    const fabMenu = document.getElementById("fabMenu");
    if (fabMenu.style.display === "flex") {
        fabMenu.style.display = "none";
        return;
    }
    
    fabMenu.style.display = "flex";
    fabMenu.innerHTML = '<div style="color:#aaa; text-align:center;">جاري تحميل المستخدمين...</div>';

    const users = await fetchUsersForChat();
    fabMenu.innerHTML = "";
    
    if (!Array.isArray(users) || users.length === 0) {
        fabMenu.innerHTML = '<div style="color:#8696a0; text-align:center;">لا يوجد مستخدمين آخرين لبدء محادثة.</div>';
        return;
    }

    users.forEach(user => {
        const div = document.createElement("div");
        div.textContent = `💬 ${user.name} (${user.phone})`;
        div.onclick = () => {  
            createChat(user.id, user.name);  
            fabMenu.style.display = "none";
        };
        fabMenu.appendChild(div);
    });
}

async function createChat(targetUserId, targetUserName) {
    const formData = new FormData();
    formData.append('target_user_id', targetUserId);

    try {
        const response = await fetch('api.php?action=create_chat', {
            method: 'POST',
            body: formData
        });
        const result = await response.json();

        if (result.chat_id) {
            fetchChats();  
            openChat(result.chat_id, targetUserName);
        } else if (result.error === 'chat_exists') {
            alert(`الدردشة مع ${targetUserName} موجودة بالفعل.`);
            fetchChats();  
            openChat(result.chat_id, targetUserName);
        } else {
            alert('فشل إنشاء المحادثة: ' + (result.error || 'غير معروف'));
        }
    } catch (error) {
        console.error('Error creating chat:', error);
        alert('فشل الاتصال بالخادم.');
    }
}

// ====== Polling ذكي كل X ثانية ======
setInterval(async ()=>{
    if(document.getElementById('chats').classList.contains('active')){
         await fetchChats();
    }
    if(currentChatId) await loadMessages(false);
}, pollingInterval);

// دالة تهيئة التطبيق
function initApp() {
    applySavedSettings(); 
    fetchChats(); 
}

// ====== init ======
initApp(); 

</script>
    
    
    <!-- HTML -->
<div id="app-popup" class="popup">
  <div class="popup-content">
    <span class="close-btn" onclick="closePopup()">&times;</span>
    <h2>📱 حمّل تطبيقنا الآن!</h2>
    <p>استمتع بتجربة أسرع وأسهل عبر تطبيقنا على هاتفك.</p>
    <a href="واتس المعدل.apk" class="download-btn">تنزيل التطبيق</a>
  </div>
</div>

<!-- CSS -->
<style>
  .popup {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100%; height: 100%;
     /* background: url('https://amrweb2.kesug.com/pc.jpg');
      background-repeat: no-repeat;
      background-position: center center;
      background-size: cover;*/
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
    font-family: 'Tajawal', sans-serif;
  }

  .popup-content {
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    text-align: center;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
  }

  .popup-content h2 {
    color: #333;
  }

  .popup-content p {
    color: #555;
    margin: 15px 0;
  }

  .download-btn {
    display: inline-block;
    padding: 12px 20px;
    background-color: #0078ff;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
  }

  .download-btn:hover {
    background-color: #005fcc;
  }

  .close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 60px;
    color: red;
    cursor: pointer;
  }
</style>

<!-- JavaScript -->
<script>
  function showPopup() {
    document.getElementById("app-popup").style.display = "flex";
  }

  function closePopup() {
    document.getElementById("app-popup").style.display = "none";
  }

  // إظهار الرسالة بعد 5 ثوانٍ من دخول الموقع
  window.onload = function() {
    setTimeout(showPopup, 5000);
  }
    //
    
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/service-worker.js')
  .then(() => console.log('✅ Service Worker Registered'))
  .catch((error) => console.log('❌ Service Worker Error:', error));
}


</script>
</body>
</html>