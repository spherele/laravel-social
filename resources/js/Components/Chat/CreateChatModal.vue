<template>
    <transition name="modal">
        <div
            v-if="isCreateChatModalOpen"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 mt-unset"
        >
            <div class="bg-white rounded-lg p-6 max-w-md w-full">
                <h2 class="text-xl font-semibold mb-4">Create Chat</h2>
                <input
                    v-model="newChatName"
                    type="text"
                    placeholder="Chat name"
                    class="w-full p-2 border border-gray-200 rounded-lg mb-4"
                />
                <input
                    v-model="newChatAvatar"
                    type="text"
                    placeholder="Avatar URL (optional)"
                    class="w-full p-2 border border-gray-200 rounded-lg mb-4"
                />

                <!-- Список друзей -->
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-2">Select Friends</h3>
                    <div class="space-y-2  max-h-[300px] overflow-auto">
                        <div
                            v-for="friend in friends"
                            :key="friend.id"
                            class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg cursor-pointer"
                            @click="toggleFriendSelection(friend.id)"
                        >
                            <input
                                type="checkbox"
                                :checked="selectedFriends.includes(friend.id)"
                                class="form-checkbox h-4 w-4 text-[#111827] rounded"
                            />
                            <img :src="friend.avatar" :alt="friend.name" class="w-8 h-8 rounded-full" />
                            <span class="text-gray-900">{{ friend.name }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        @click="closeCreateChatModal"
                        class="p-2 text-gray-600 hover:text-gray-900"
                    >
                        Cancel
                    </button>
                    <PrimaryButton
                        @click="createChat"
                        class="p-2 bg-[#111827] text-white rounded-lg hover:bg-[#232b3d] ml-2"
                    >
                        Create
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    isCreateChatModalOpen: {
        type: Boolean,
        required: true,
    },
    friends: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['closeCreateChatModal', 'createChat']);

const newChatName = ref('');
const newChatAvatar = ref('');
const selectedFriends = ref([]);

const toggleFriendSelection = (friendId) => {
    const index = selectedFriends.value.indexOf(friendId);
    if (index === -1) {
        selectedFriends.value.push(friendId);
    } else {
        selectedFriends.value.splice(index, 1);
    }
};

const closeCreateChatModal = () => {
    emit('closeCreateChatModal');
    newChatName.value = '';
    newChatAvatar.value = '';
    selectedFriends.value = [];
};

const createChat = () => {
    emit('createChat', newChatName.value, newChatAvatar.value, selectedFriends.value);
    closeCreateChatModal();
};
</script>

<style scoped>
.mt-unset {
    margin-top: unset !important;
}
</style>
