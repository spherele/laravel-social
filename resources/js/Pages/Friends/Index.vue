<template>
    <Head title="Friends" />

    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8 px-3 py-6">
            <FriendSearch v-model:searchQuery="searchQuery" />
            <FriendList
                :filtered-friends="filteredFriends"
                @add-friend="addFriend"
            />
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import FriendSearch from "@/Components//Friends/FriendSearch.vue";
import FriendList from "@/Components/Friends/FriendsList.vue";

const users = ref([
    {
        id: 1,
        name: "John Doe",
        avatar: "https://via.placeholder.com/150",
        status: "Online",
        isAdded: false,
    },
    {
        id: 2,
        name: "Jane Smith",
        avatar: "https://via.placeholder.com/150",
        status: "Offline",
        isAdded: false,
    },
    {
        id: 3,
        name: "Alice Johnson",
        avatar: "https://via.placeholder.com/150",
        status: "Online",
        isAdded: false,
    },
    {
        id: 4,
        name: "Bob Brown",
        avatar: "https://via.placeholder.com/150",
        status: "Away",
        isAdded: false,
    },
    {
        id: 5,
        name: "Charlie Davis",
        avatar: "https://via.placeholder.com/150",
        status: "Online",
        isAdded: false,
    },
]);


const searchQuery = ref('');

const filteredFriends = computed(() => {
    if (!searchQuery.value.trim()) {
        return users.value;
    }
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const addFriend = (friend) => {
    const user = users.value.find(u => u.id === friend.id);
    if (user) {
        user.isAdded = true;
    }
    console.log('Friend added:', friend.name);
};
</script>
