class ChatApp {
    constructor() {
        this.messagesContainer = document.getElementById('messages-container');
        this.inputField = document.getElementById('chat-input');
        this.sendButton = document.getElementById('send-btn');
        this.typingIndicator = null;
        
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadChatHistory();
        this.setupThemeToggle();
        this.setupVoiceInput();
    }

    bindEvents() {
        this.sendButton.addEventListener('click', () => this.sendMessage());
        this.inputField.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize textarea
        this.inputField.addEventListener('input', () => {
            this.inputField.style.height = 'auto';
            this.inputField.style.height = this.inputField.scrollHeight + 'px';
        });
    }

    async sendMessage() {
        const message = this.inputField.value.trim();
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        this.inputField.value = '';
        this.inputField.style.height = 'auto';

        // Show typing indicator
        this.showTypingIndicator();

        try {
            const response = await fetch('chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message=${encodeURIComponent(message)}`
            });

            const botReply = await response.text();
            this.hideTypingIndicator();
            this.addMessage(botReply, 'bot');
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
            console.error('Error:', error);
        }
    }

    addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = content;
        
        messageDiv.appendChild(messageContent);
        this.messagesContainer.appendChild(messageDiv);
        
        // Scroll to bottom
        this.scrollToBottom();
        
        // Save to localStorage
        this.saveMessage(content, sender);
    }

    showTypingIndicator() {
        this.typingIndicator = document.createElement('div');
        this.typingIndicator.className = 'message bot';
        this.typingIndicator.innerHTML = `
            <div class="message-content">
                <div class="typing-indicator">
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>
        `;
        this.messagesContainer.appendChild(this.typingIndicator);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        if (this.typingIndicator) {
            this.typingIndicator.remove();
            this.typingIndicator = null;
        }
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    saveMessage(content, sender) {
        const messages = this.getStoredMessages();
        messages.push({ content, sender, timestamp: Date.now() });
        localStorage.setItem('chatMessages', JSON.stringify(messages));
    }

    getStoredMessages() {
        const stored = localStorage.getItem('chatMessages');
        return stored ? JSON.parse(stored) : [];
    }

    loadChatHistory() {
        const messages = this.getStoredMessages();
        messages.forEach(msg => {
            this.addMessage(msg.content, msg.sender);
        });
    }

    setupThemeToggle() {
        const themeToggle = document.getElementById('theme-toggle');
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            this.setTheme(newTheme);
        });
    }

    setTheme(theme) {
        document.body.className = theme;
        localStorage.setItem('theme', theme);
        
        const themeIcon = document.querySelector('#theme-toggle i');
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    setupVoiceInput() {
        const voiceBtn = document.getElementById('voice-btn');
        if (!('webkitSpeechRecognition' in window)) {
            voiceBtn.style.display = 'none';
            return;
        }

        const recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'id-ID';

        voiceBtn.addEventListener('click', () => {
            recognition.start();
            voiceBtn.classList.add('listening');
        });

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            this.inputField.value = transcript;
            voiceBtn.classList.remove('listening');
        };

        recognition.onend = () => {
            voiceBtn.classList.remove('listening');
        };
    }

    clearChat() {
        this.messagesContainer.innerHTML = '';
        localStorage.removeItem('chatMessages');
    }

    exportChat() {
        const messages = this.getStoredMessages();
        const chatText = messages.map(msg => 
            `${msg.sender === 'user' ? 'Anda' : 'Bot'}: ${msg.content}`
        ).join('\n');
        
        const blob = new Blob([chatText], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'chat-history.txt';
        a.click();
        URL.revokeObjectURL(url);
    }
}

// Initialize the app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ChatApp();
});

// Additional utility functions
const Utils = {
    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    },

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    animateCSS(element, animationName, callback) {
        const node = element;
        node.classList.add('animated', animationName);

        function handleAnimationEnd() {
            node.classList.remove('animated', animationName);
            node.removeEventListener('animationend', handleAnimationEnd);
            if (typeof callback === 'function') callback();
        }

        node.addEventListener('animationend', handleAnimationEnd);
    }
};
