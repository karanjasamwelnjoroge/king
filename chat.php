<?php
require_once '../../config/session.php';
require_login();
include '../../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ryvah Community Chat</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary: #667eea;
        --primary-dark: #5a67d8;
        --secondary: #764ba2;
        --accent: #f093fb;
        --success: #48bb78;
        --danger: #f56565;
        --warning: #ed8936;
        --light: #f7fafc;
        --lighter: #edf2f7;
        --dark: #2d3748;
        --darker: #1a202c;
        --text: #2d3748;
        --text-light: #718096;
        --border: #e2e8f0;
        --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: var(--text);
        height: 100vh;
        overflow: hidden;
    }

    .chat-app {
        display: flex;
        height: 100vh;
        background: #fff;
        position: relative;
    }

    /* Sidebar */
    .sidebar {
        width: 350px;
        background: #fff;
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-sm);
        z-index: 10;
    }

    .sidebar-header {
        padding: 24px 20px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #fff;
        border-bottom: 1px solid var(--border);
    }

    .sidebar-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 400;
    }

    /* Search */
    .search-container {
        padding: 20px;
        border-bottom: 1px solid var(--border);
        background: var(--light);
    }

    .search-box {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-size: 0.95rem;
        background: #fff;
        transition: all 0.2s ease;
        outline: none;
    }

    .search-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
        font-size: 1rem;
    }

    .search-clear {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-light);
        cursor: pointer;
        padding: 4px;
        border-radius: 50%;
        display: none;
        transition: all 0.2s ease;
    }

    .search-clear:hover {
        background: var(--lighter);
        color: var(--text);
    }

    /* Users List */
    .users-container {
        flex: 1;
        overflow-y: auto;
        padding: 8px 0;
    }

    .users-list {
        list-style: none;
    }

    .user-item {
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f8f9fa;
        position: relative;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .user-item:hover {
        background: var(--light);
        transform: translateX(2px);
    }

    .user-item.active {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-right: 3px solid var(--primary);
    }

    .user-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    }

    /* Avatar */
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        position: relative;
        box-shadow: var(--shadow-sm);
        flex-shrink: 0;
    }

    .user-avatar.online::after {
        content: '';
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        background: var(--success);
        border-radius: 50%;
        border: 2px solid #fff;
    }

    /* User Info */
    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 600;
        font-size: 1rem;
        color: var(--text);
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-status {
        font-size: 0.85rem;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--success);
    }

    /* Unread Badge */
    .unread-badge {
        background: linear-gradient(135deg, var(--danger) 0%, #ff6b6b 100%);
        color: #fff;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: 600;
        min-width: 24px;
        text-align: center;
        box-shadow: var(--shadow-sm);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Main Chat Area */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
        position: relative;
    }

    /* Chat Header */
    .chat-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        background: #fff;
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 80px;
        box-shadow: var(--shadow-sm);
        z-index: 5;
    }

    .chat-header-info {
        flex: 1;
    }

    .chat-user-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .chat-user-status {
        font-size: 0.9rem;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chat-actions {
        display: flex;
        gap: 12px;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 50%;
        background: var(--light);
        color: var(--text-light);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--primary);
        color: #fff;
        transform: scale(1.1);
    }

    /* Messages Area */
    .chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        display: flex;
        flex-direction: column;
        gap: 16px;
        scroll-behavior: smooth;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: var(--text-light);
    }

    /* Message Bubbles */
    .message-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-width: 75%;
    }

    .message-group.own {
        align-self: flex-end;
        align-items: flex-end;
    }

    .message-bubble {
        padding: 14px 18px;
        border-radius: 20px;
        font-size: 0.95rem;
        line-height: 1.4;
        word-wrap: break-word;
        position: relative;
        animation: messageSlideIn 0.3s ease-out;
    }

    @keyframes messageSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .message-group:not(.own) .message-bubble {
        background: #fff;
        color: var(--text);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    .message-group.own .message-bubble {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #fff;
        box-shadow: var(--shadow-md);
    }

    .message-time {
        font-size: 0.8rem;
        color: var(--text-light);
        margin-top: 4px;
        opacity: 0.8;
    }

    .message-group.own .message-time {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Chat Input */
    .chat-input-container {
        padding: 20px 24px;
        background: #fff;
        border-top: 1px solid var(--border);
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }

    .chat-input-form {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        max-width: 100%;
    }

    .input-wrapper {
        flex: 1;
        position: relative;
    }

    .chat-input {
        width: 100%;
        padding: 14px 50px 14px 18px;
        border: 2px solid var(--border);
        border-radius: 25px;
        font-size: 0.95rem;
        background: var(--light);
        outline: none;
        transition: all 0.2s ease;
        resize: none;
        min-height: 50px;
        max-height: 120px;
        font-family: inherit;
    }

    .chat-input:focus {
        border-color: var(--primary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .input-actions {
        position: absolute;
        right: 8px;
        bottom: 8px;
        display: flex;
        gap: 4px;
    }

    .input-action-btn {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 50%;
        background: transparent;
        color: var(--text-light);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .input-action-btn:hover {
        background: var(--lighter);
        color: var(--primary);
    }

    .send-btn {
        width: 50px;
        height: 50px;
        border: none;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-md);
        flex-shrink: 0;
    }

    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-lg);
    }

    .send-btn:active {
        transform: scale(0.95);
    }

    .send-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Empty State */
    .empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 40px;
        color: var(--text-light);
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text);
    }

    .empty-state-text {
        font-size: 1rem;
        max-width: 400px;
        line-height: 1.5;
    }

    /* No Results */
    .no-results {
        padding: 40px 20px;
        text-align: center;
        color: var(--text-light);
    }

    .no-results-icon {
        font-size: 3rem;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    .no-results-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text);
    }

    .no-results-text {
        font-size: 0.9rem;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .chat-app {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
            height: 50vh;
            border-right: none;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-header {
            padding: 16px 20px;
        }

        .sidebar-title {
            font-size: 1.3rem;
        }

        .search-container {
            padding: 16px 20px;
        }

        .user-item {
            padding: 12px 20px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }

        .chat-header {
            padding: 16px 20px;
            min-height: 70px;
        }

        .chat-messages {
            padding: 16px 20px;
        }

        .chat-input-container {
            padding: 16px 20px;
        }

        .message-group {
            max-width: 85%;
        }
    }

    @media (max-width: 480px) {
        .sidebar-header {
            padding: 12px 16px;
        }

        .search-container {
            padding: 12px 16px;
        }

        .user-item {
            padding: 10px 16px;
        }

        .chat-header {
            padding: 12px 16px;
        }

        .chat-messages {
            padding: 12px 16px;
        }

        .chat-input-container {
            padding: 12px 16px;
        }

        .message-group {
            max-width: 90%;
        }
    }

    /* Loading Animation */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(102, 126, 234, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 18px;
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--border);
        max-width: 100px;
        box-shadow: var(--shadow-sm);
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--text-light);
        animation: typingBounce 1.4s ease-in-out infinite both;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typingBounce {
        0%, 80%, 100% {
            transform: scale(0);
            opacity: 0.5;
        }
        40% {
            transform: scale(1);
            opacity: 1;
        }
    }
    </style>
</head>

<body>
    <div class="chat-app">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-title">
                    <i class="fas fa-comments"></i>
                    Community Chat
                </div>
                <div class="sidebar-subtitle">Connect with your community</div>
            </div>

            <!-- Search -->
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Search conversations..." autocomplete="off">
                    <button type="button" class="search-clear" id="searchClear">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Users List -->
            <div class="users-container">
                <ul class="users-list" id="usersList"></ul>
                <div class="no-results" id="noResults" style="display: none;">
                    <i class="fas fa-search no-results-icon"></i>
                    <div class="no-results-title">No results found</div>
                    <div class="no-results-text">Try searching with different keywords</div>
                </div>
            </div>
        </aside>

        <!-- Main Chat Area -->
        <main class="chat-main">
            <!-- Empty State -->
            <div class="empty-state" id="emptyState">
                <i class="fas fa-comments empty-state-icon"></i>
                <div class="empty-state-title">Welcome to Community Chat</div>
                <div class="empty-state-text">Select a conversation from the sidebar to start chatting with your community members</div>
            </div>

            <!-- Chat Header -->
            <div class="chat-header" id="chatHeader" style="display: none;">
                <div class="user-avatar" id="chatAvatar"></div>
                <div class="chat-header-info">
                    <div class="chat-user-name" id="chatUserName"></div>
                    <div class="chat-user-status">
                        <div class="status-dot"></div>
                        <span>Online</span>
                    </div>
                </div>
                <div class="chat-actions">
                    <button class="action-btn" title="Call">
                        <i class="fas fa-phone"></i>
                    </button>
                    <button class="action-btn" title="Video Call">
                        <i class="fas fa-video"></i>
                    </button>
                    <button class="action-btn" title="More Options">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chatMessages" style="display: none;"></div>

            <!-- Chat Input -->
            <div class="chat-input-container" id="chatInputContainer" style="display: none;">
                <form class="chat-input-form" id="chatForm">
                    <div class="input-wrapper">
                        <textarea class="chat-input" id="chatInput" placeholder="Type your message..." rows="1"></textarea>
                        <div class="input-actions">
                            <button type="button" class="input-action-btn" title="Attach File">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <button type="button" class="input-action-btn" title="Emoji">
                                <i class="fas fa-smile"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="send-btn" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>
    // DOM Elements
    const usersList = document.getElementById('usersList');
    const searchInput = document.getElementById('searchInput');
    const searchClear = document.getElementById('searchClear');
    const noResults = document.getElementById('noResults');
    const emptyState = document.getElementById('emptyState');
    const chatHeader = document.getElementById('chatHeader');
    const chatAvatar = document.getElementById('chatAvatar');
    const chatUserName = document.getElementById('chatUserName');
    const chatMessages = document.getElementById('chatMessages');
    const chatInputContainer = document.getElementById('chatInputContainer');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');

    // State
    let currentUserId = null;
    let pollingInterval = null;
    let allUsers = [];
    let filteredUsers = [];

    // Utility Functions
    function getInitials(name) {
        return name.split(' ')
            .map(word => word[0])
            .join('')
            .substring(0, 2)
            .toUpperCase();
    }

    function formatTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInHours = (now - date) / (1000 * 60 * 60);
        
        if (diffInHours < 24) {
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else if (diffInHours < 48) {
            return 'Yesterday ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else {
            return date.toLocaleDateString();
        }
    }

    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
    }

    // Search Functionality
    function filterUsers(query) {
        const searchTerm = query.toLowerCase().trim();
        
        if (!searchTerm) {
            filteredUsers = [...allUsers];
            searchClear.style.display = 'none';
        } else {
            filteredUsers = allUsers.filter(user => 
                user.full_name.toLowerCase().includes(searchTerm)
            );
            searchClear.style.display = 'block';
        }
        
        renderUsers();
    }

    function clearSearch() {
        searchInput.value = '';
        filterUsers('');
        searchInput.focus();
    }

    // Render Functions
    function renderUsers() {
        usersList.innerHTML = '';
        
        if (filteredUsers.length === 0 && searchInput.value.trim()) {
            noResults.style.display = 'block';
            return;
        }
        
        noResults.style.display = 'none';
        
        filteredUsers.forEach(user => {
            const li = document.createElement('li');
            li.className = 'user-item';
            li.dataset.userid = user.user_id;
            
            li.innerHTML = `
                <div class="user-avatar online">${getInitials(user.full_name)}</div>
                <div class="user-info">
                    <div class="user-name">${user.full_name}</div>
                    <div class="user-status">
                        <div class="status-dot"></div>
                        <span>Online</span>
                    </div>
                </div>
                ${user.unread_count > 0 ? `<div class="unread-badge">${user.unread_count}</div>` : ''}
            `;
            
            li.addEventListener('click', () => selectUser(user));
            usersList.appendChild(li);
        });
    }

    function renderMessages(messages) {
        chatMessages.innerHTML = '';
        const myId = <?php echo (int)$_SESSION['user_id']; ?>;
        
        messages.forEach(msg => {
            const isOwn = msg.sender_id === myId;
            const messageGroup = document.createElement('div');
            messageGroup.className = `message-group ${isOwn ? 'own' : ''}`;
            
            messageGroup.innerHTML = `
                <div class="message-bubble">${msg.message}</div>
                <div class="message-time">${formatTime(msg.sent_at)}</div>
            `;
            
            chatMessages.appendChild(messageGroup);
        });
        
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // API Functions
    async function fetchUsers() {
        try {
            const response = await fetch('../../php/chat/fetch_users.php');
            const data = await response.json();
            
            if (data.success) {
                allUsers = data.users;
                filterUsers(searchInput.value);
            }
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    }

    async function fetchMessages() {
        if (!currentUserId) return;
        
        try {
            const response = await fetch(`../../php/chat/fetch_messages.php?user_id=${currentUserId}`);
            const data = await response.json();
            
            if (data.success) {
                renderMessages(data.messages);
                // Refresh user list to update unread counters
                fetchUsers();
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    async function sendMessage(message) {
        if (!message.trim() || !currentUserId) return false;
        
        try {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<div class="loading"></div>';
            
            const response = await fetch('../../php/chat/send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `receiver_id=${encodeURIComponent(currentUserId)}&message=${encodeURIComponent(message)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                chatInput.value = '';
                autoResizeTextarea(chatInput);
                fetchMessages();
                return true;
            }
            
            return false;
        } catch (error) {
            console.error('Error sending message:', error);
            return false;
        } finally {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        }
    }

    // User Selection
    function selectUser(user) {
        currentUserId = user.user_id;
        
        // Update UI
        document.querySelectorAll('.user-item').forEach(item => item.classList.remove('active'));
        const activeItem = document.querySelector(`[data-userid="${user.user_id}"]`);
        if (activeItem) activeItem.classList.add('active');
        
        // Show chat interface
        emptyState.style.display = 'none';
        chatHeader.style.display = 'flex';
        chatMessages.style.display = 'flex';
        chatInputContainer.style.display = 'block';
        
        // Update header
        chatAvatar.textContent = getInitials(user.full_name);
        chatUserName.textContent = user.full_name;
        
        // Load messages
        fetchMessages();
        
        // Start polling
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(fetchMessages, 2000);
        
        // Focus input
        chatInput.focus();
    }

    // Event Listeners
    searchInput.addEventListener('input', (e) => {
        filterUsers(e.target.value);
    });

    searchClear.addEventListener('click', clearSearch);

    chatInput.addEventListener('input', (e) => {
        autoResizeTextarea(e.target);
    });

    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (message) {
            await sendMessage(message);
        }
    });

    // Initialize
    window.addEventListener('load', () => {
        fetchUsers();
        chatInput.focus();
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
    </script>
</body>

</html>