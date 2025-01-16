<template>
    <Card class="p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Chats</h2>
            <PrimaryButton
                @click="openCreateChatModal"
                class="p-2 bg-[#111827] text-white rounded-lg hover:bg-[#232b3d] transition-colors"
            >
                Create Chat
            </PrimaryButton>
        </div>
        <div class="space-y-4">
            <div
                v-for="chat in chats"
                :key="chat.id"
                @click="selectChat(chat)"
                class="p-4 hover:bg-gray-50 cursor-pointer transition-colors"
            >
                <div class="flex items-center">
                    <Avatar class="w-10 h-10">
                        <img :src="chat.avatar" :alt="chat.name" class="object-cover"/>
                    </Avatar>
                    <div class="ml-3">
                        <h3 class="font-medium">{{ chat.name }}</h3>
                        <p class="text-sm text-gray-500">{{ chat.lastMessage }}</p>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import Card from '@/Components/UI/Card.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    chats: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['selectChat', 'openCreateChatModal']);

const selectChat = (chat) => {
    emit('selectChat', chat);
};

const openCreateChatModal = () => {
    emit('openCreateChatModal');
};
</script>
