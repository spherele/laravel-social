<template>
    <div class="friends-list">
        <!-- Сообщение, если друзей нет -->
        <div v-if="filteredFriends.length === 0" class="text-center py-4 text-gray-500">
            Not found.
        </div>

        <div v-else>
            <div
                v-for="friend in filteredFriends"
                :key="friend.id"
                class="friend-item bg-white mb-3 p-6 rounded-lg shadow-sm flex items-center justify-between border-b border-gray-200 hover:bg-gray-50 transition-colors"
            >
                <div class="flex items-center gap-4">
                    <img
                        :src="friend.avatar"
                        :alt="friend.name"
                        class="w-10 h-10 rounded-full"
                    />
                    <div>
                        <h3 class="font-semibold">{{ friend.name }}</h3>
                        <p class="text-sm text-gray-500">{{ friend.status }}</p>
                    </div>
                </div>

                <PrimaryButton
                    v-if="!friend.isAdded"
                    @click="$emit('add-friend', friend)"
                    class="p-2 bg-[#111827] text-white rounded-lg hover:bg-[#232b3d] transition-colors"
                >
                    Add Friend
                </PrimaryButton>
                <span v-else class="text-sm text-gray-500">Added</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    filteredFriends: {
        type: Array,
        required: true,
    },
});

defineEmits(['add-friend']);
</script>

<style scoped>
.friends-list {
    margin: 0 auto;
}

.friend-item {
    cursor: pointer;
}

/* Стили для скролла (если список длинный) */
.friends-list {
    max-height: 100vh;
    overflow-y: auto;
}

/* Стили для скроллбара */
.friends-list::-webkit-scrollbar {
    width: 8px;
}

.friends-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.friends-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.friends-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
