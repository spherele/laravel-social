<template>
    <Head title="Chats"/>
    <AuthenticatedLayout>
        <div class="bg-gray-100 pt-6">
            <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
                <ChatList
                    v-if="!selectedChat"
                    :chats="chats"
                    @selectChat="selectChat"
                    @openCreateChatModal="openCreateChatModal"
                />
                <ChatWindow
                    v-else
                    :selectedChat="selectedChat"
                    :messages="messages"
                    @goBackToChatList="goBackToChatList"
                    @sendMessage="sendMessage"
                    @handleFileUpload="handleFileUpload"
                    @editMessage="editMessage"
                    @openImageModal="openImageModal"
                />
                <CreateChatModal
                    :isCreateChatModalOpen="isCreateChatModalOpen"
                    @closeCreateChatModal="closeCreateChatModal"
                    @createChat="createChat"
                    :friends="friends"
                />
                <ImageModal
                    :isImageModalOpen="isImageModalOpen"
                    :selectedImage="selectedImage"
                    @close="closeImageModal"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import ChatList from '@/Components/Chat/ChatList.vue';
import ChatWindow from '@/Components/Chat/ChatWindow.vue';
import CreateChatModal from '@/Components/Chat/CreateChatModal.vue';
import ImageModal from "@/Components/Posts/ImageModal.vue";

const chats = ref([
    {
        id: 1,
        name: 'John Doe',
        avatar: 'https://via.placeholder.com/150',
        lastMessage: 'Hey, how are you?',
    },
    {
        id: 2,
        name: 'Jane Smith',
        avatar: 'https://via.placeholder.com/150',
        lastMessage: 'See you later!',
    },
    {
        id: 3,
        name: 'Alice Johnson',
        avatar: 'https://via.placeholder.com/150',
        lastMessage: 'Thanks for the update.',
    },
]);

const friends = ref([
    {
        id: 1,
        name: "John Doe",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 2,
        name: "Jane Smith",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 3,
        name: "Alice Johnson",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 4,
        name: "John Doe",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 5,
        name: "Jane Smith",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 6,
        name: "Alice Johnson",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 7,
        name: "John Doe",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 8,
        name: "Jane Smith",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 9,
        name: "Alice Johnson",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 10,
        name: "John Doe",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 11,
        name: "Jane Smith",
        avatar: "https://via.placeholder.com/150",
    },
    {
        id: 12,
        name: "Alice Johnson",
        avatar: "https://via.placeholder.com/150",
    },

]);

const selectedChat = ref(null);
const messages = ref([
    {
        id: 1,
        sender: 'other',
        text: 'Hey, how are you?',
        time: '10:00 AM',
        avatar: 'https://via.placeholder.com/150',
        isEdited: false,
    },
    {
        id: 2,
        sender: 'me',
        text: 'I\'m good, thanks! How about you?',
        time: '10:01 AM',
        isEdited: false,
    },
    {
        id: 3,
        sender: 'other',
        text: 'I\'m doing great! Just working on some projects.',
        time: '10:02 AM',
        avatar: 'https://via.placeholder.com/150',
        isEdited: false,
    },
]);

const handleCreateChat = (name, avatar, selectedFriends) => {
    console.log('Creating chat:', name, avatar, selectedFriends);
};

const newMessage = ref('');
const file = ref(null);
const isImageModalOpen = ref(false);
const selectedImage = ref('');
const isCreateChatModalOpen = ref(false);
const newChatName = ref('');
const newChatAvatar = ref('');
const editingMessage = ref(null);

const selectChat = (chat) => {
    selectedChat.value = chat;
};

const goBackToChatList = () => {
    selectedChat.value = null;
};

const sendMessage = (message, editingMessage) => {
    if (message.trim()) {
        if (editingMessage) {
            editingMessage.text = message;
            editingMessage.isEdited = true;
        } else {
            messages.value.push({
                id: messages.value.length + 1,
                sender: 'me',
                text: message.trim(),
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                isEdited: false,
            });
        }
    }
};

const handleFileUpload = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        file.value = selectedFile;
        sendFile();
    }
};

const sendFile = () => {
    if (file.value) {
        const reader = new FileReader();
        reader.onload = (e) => {
            messages.value.push({
                id: messages.value.length + 1,
                sender: 'me',
                file: {
                    name: file.value.name,
                    url: e.target.result,
                    type: file.value.type,
                },
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            });
            file.value = null;
        };
        reader.readAsDataURL(file.value);
    }
};

const openImageModal = (imageUrl) => {
    selectedImage.value = imageUrl;
    isImageModalOpen.value = true;
};

const closeImageModal = () => {
    isImageModalOpen.value = false;
    selectedImage.value = '';
};

const editMessage = (message) => {
    editingMessage.value = message;
    newMessage.value = message.text;
};

const openCreateChatModal = () => {
    isCreateChatModalOpen.value = true;
};

const closeCreateChatModal = () => {
    isCreateChatModalOpen.value = false;
    newChatName.value = '';
    newChatAvatar.value = '';
};

const createChat = (name, avatar) => {
    if (name.trim()) {
        chats.value.push({
            id: chats.value.length + 1,
            name: name,
            avatar: avatar || 'https://via.placeholder.com/150',
            lastMessage: '',
        });
        closeCreateChatModal();
    }
};
</script>
<style scoped>
.bg-gray-100.pt-6 {
    @media (max-width: 768px) {
        padding-top: unset;
    }
}
</style>
