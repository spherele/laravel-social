<template>
    <div class="search-friends bg-white mb-3 p-6 rounded-lg shadow-sm">
        <h1 class="text-2xl font-bold mb-6">Friends</h1>
        <div class="flex items-center gap-2">
            <input
                :value="searchQuery"
                @input="updateSearchQuery"
                type="text"
                placeholder="Search for friends..."
                class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#111827]"
            />
            <button
                @click="handleSearch"
                class="p-2 bg-[#111827] text-white rounded-lg hover:bg-[#232b3d] transition-colors"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="lucide lucide-search">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    searchQuery: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['update:searchQuery']);

const updateSearchQuery = (event) => {
    emit('update:searchQuery', event.target.value);
};

const handleSearch = () => {
    emit('search', props.searchQuery);
};
</script>
