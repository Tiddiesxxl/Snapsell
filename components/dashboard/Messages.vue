<template>
  <div class="messages-container">
    <div class="messages-layout">
      <!-- Contacts Sidebar -->
      <div class="contacts-sidebar">
        <div class="search-bar">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Search contacts..." 
          />
          <Fa icon="search" />
        </div>
        
        <div class="contacts-list">
          <div 
            v-for="contact in filteredContacts" 
            :key="contact.id"
            :class="['contact-item', { active: selectedContact?.id === contact.id }]"
            @click="selectContact(contact)"
          >
            <img :src="contact.avatar" :alt="contact.name" class="contact-avatar" />
            <div class="contact-info">
              <h4>{{ contact.name }}</h4>
              <p class="last-message">{{ contact.lastMessage }}</p>
            </div>
            <div class="contact-meta">
              <span class="message-time">{{ formatTime(contact.lastMessageTime) }}</span>
              <span v-if="contact.unreadCount" class="unread-count">
                {{ contact.unreadCount }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Chat Area -->
      <div class="chat-area">
        <template v-if="selectedContact">
          <div class="chat-header">
            <div class="contact-details">
              <img :src="selectedContact.avatar" :alt="selectedContact.name" />
              <div>
                <h3>{{ selectedContact.name }}</h3>
                <span class="status" :class="selectedContact.status">
                  {{ selectedContact.status }}
                </span>
              </div>
            </div>
            <div class="header-actions">
              <button @click="startVideoCall">
                <Fa icon="video" />
              </button>
              <button @click="startAudioCall">
                <Fa icon="phone" />
              </button>
              <button @click="showContactInfo = !showContactInfo">
                <Fa icon="info-circle" />
              </button>
            </div>
          </div>

          <div class="chat-messages" ref="messagesContainer">
            <div 
              v-for="message in currentChat" 
              :key="message.id"
              :class="['message', message.sender === 'me' ? 'sent' : 'received']"
            >
              <div class="message-content">
                <p>{{ message.text }}</p>
                <span class="message-time">{{ formatTime(message.timestamp) }}</span>
              </div>
            </div>
          </div>

          <div class="chat-input">
            <button class="attach-btn">
              <Fa icon="paperclip" />
            </button>
            <textarea 
              v-model="newMessage" 
              placeholder="Type a message..."
              @keyup.enter.prevent="sendMessage"
            ></textarea>
            <button class="send-btn" @click="sendMessage">
              <Fa icon="paper-plane" />
            </button>
          </div>
        </template>

        <div v-else class="no-chat-selected">
          <Fa icon="comments" class="no-chat-icon" />
          <h3>Select a conversation to start messaging</h3>
        </div>
      </div>

      <!-- Contact Info Sidebar -->
      <div v-if="showContactInfo" class="contact-info-sidebar">
        <div class="info-header">
          <h3>Contact Info</h3>
          <button @click="showContactInfo = false">
            <Fa icon="times" />
          </button>
        </div>
        <div class="info-content" v-if="selectedContact">
          <img :src="selectedContact.avatar" :alt="selectedContact.name" class="large-avatar" />
          <h2>{{ selectedContact.name }}</h2>
          <div class="info-section">
            <h4>Contact Details</h4>
            <p><Fa icon="envelope" /> {{ selectedContact.email }}</p>
            <p><Fa icon="phone" /> {{ selectedContact.phone }}</p>
          </div>
          <div class="info-section">
            <h4>Shared Media</h4>
            <div class="media-grid">
              <!-- Shared media items -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
const searchQuery = ref('');
const selectedContact = ref(null);
const showContactInfo = ref(false);
const newMessage = ref('');
const messagesContainer = ref(null);
const currentChat = ref([]);

// Sample contacts data - replace with API call
const contacts = ref([
  {
    id: 1,
    name: 'John Doe',
    avatar: 'https://i.pravatar.cc/150?img=1',
    lastMessage: 'Hey, about those photos...',
    lastMessageTime: new Date(),
    unreadCount: 2,
    status: 'online',
    email: 'john@example.com',
    phone: '+1234567890'
  },
  // Add more contacts
]);

const filteredContacts = computed(() => {
  return contacts.value.filter(contact =>
    contact.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const selectContact = async (contact) => {
  selectedContact.value = contact;
  // Fetch chat history
  try {
    const response = await fetch(`your-api-endpoint/messages/${contact.id}`);
    const data = await response.json();
    currentChat.value = data.messages;
    scrollToBottom();
  } catch (error) {
    toast.error('Failed to load chat history');
  }
};

const sendMessage = async () => {
  if (!newMessage.value.trim()) return;

  const message = {
    id: Date.now(),
    text: newMessage.value,
    sender: 'me',
    timestamp: new Date()
  };

  currentChat.value.push(message);
  newMessage.value = '';
  
  try {
    await fetch(`your-api-endpoint/messages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        recipientId: selectedContact.value.id,
        message: message.text
      })
    });
    
    scrollToBottom();
  } catch (error) {
    toast.error('Failed to send message');
  }
};

const scrollToBottom = async () => {
  await nextTick();
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
};

const startVideoCall = () => {
  // Implement video call functionality
  toast.info('Video call feature coming soon');
};

const startAudioCall = () => {
  // Implement audio call functionality
  toast.info('Audio call feature coming soon');
};

onMounted(() => {
  // Initialize WebSocket connection for real-time messages
  // Implement WebSocket connection here
});
</script>

<style scoped>
.messages-container {
  height: calc(100vh - 80px);
  background: #f5f5f5;
}

.messages-layout {
  display: grid;
  grid-template-columns: 300px 1fr 300px;
  height: 100%;
}

.contacts-sidebar {
  background: white;
  border-right: 1px solid #ddd;
  display: flex;
  flex-direction: column;
}

.search-bar {
  padding: 15px;
  position: relative;
}

.search-bar input {
  width: 100%;
  padding: 8px 32px 8px 12px;
  border: 1px solid #ddd;
  border-radius: 20px;
}

.search-bar i {
  position: absolute;
  right: 25px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
}

.contacts-list {
  flex: 1;
  overflow-y: auto;
}

.contact-item {
  display: flex;
  align-items: center;
  padding: 15px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.contact-item:hover {
  background: #f5f5f5;
}

.contact-item.active {
  background: #e3f2fd;
}

.contact-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 12px;
}

.contact-info {
  flex: 1;
}

.last-message {
  color: #666;
  font-size: 0.9em;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.contact-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.message-time {
  font-size: 0.8em;
  color: #666;
}

.unread-count {
  background: #11101D;
  color: white;
  padding: 2px 6px;
  border-radius: 10px;
  font-size: 0.8em;
  margin-top: 4px;
}

.chat-area {
  display: flex;
  flex-direction: column;
  background: white;
}

.chat-header {
  padding: 15px;
  border-bottom: 1px solid #ddd;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.contact-details {
  display: flex;
  align-items: center;
  gap: 12px;
}

.status {
  font-size: 0.8em;
}

.status.online {
  color: #4CAF50;
}

.header-actions {
  display: flex;
  gap: 15px;
}

.header-actions button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.message {
  max-width: 70%;
  display: flex;
}

.message.sent {
  margin-left: auto;
}

.message-content {
  padding: 10px 15px;
  border-radius: 15px;
  background: #f0f0f0;
}

.message.sent .message-content {
  background: #11101D;
  color: white;
}

.chat-input {
  padding: 15px;
  border-top: 1px solid #ddd;
  display: flex;
  align-items: center;
  gap: 10px;
}

.chat-input textarea {
  flex: 1;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 20px;
  resize: none;
  max-height: 100px;
}

.attach-btn,
.send-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.contact-info-sidebar {
  background: white;
  border-left: 1px solid #ddd;
  padding: 15px;
}

.info-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.large-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  margin: 20px auto;
  display: block;
}

.info-section {
  margin: 20px 0;
}

.media-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  margin-top: 10px;
}

.no-chat-selected {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #666;
}

.no-chat-icon {
  font-size: 48px;
  margin-bottom: 15px;
}

@media (max-width: 1200px) {
  .messages-layout {
    grid-template-columns: 250px 1fr;
  }
  
  .contact-info-sidebar {
    position: fixed;
    right: 0;
    top: 0;
    height: 100%;
    width: 300px;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  }
  
  .contact-info-sidebar.show {
    transform: translateX(0);
  }
}

@media (max-width: 768px) {
  .messages-layout {
    grid-template-columns: 1fr;
  }
  
  .contacts-sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100%;
    width: 100%;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .contacts-sidebar.show {
    transform: translateX(0);
  }
}
</style>
