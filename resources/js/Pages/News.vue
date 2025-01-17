<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import Post from "@/Components/Posts/Index.vue";
import { onMounted, ref } from "vue";
import CreatePost from "@/Components/Posts/CreatePost.vue";
import { createPost, fetchPosts } from "@/Services/api";

const profile = ref({
    name: 'Sarah Anderson',
    role: 'Senior Software Engineer',
    avatar: 'https://masterpiecer-images.s3.yandex.net/34e974e177bb11ee9ad5ceda526c50ab:upscaled',
    about: 'Passionate software engineer with 5+ years of experience in full-stack development. Focused on creating elegant solutions to complex problems.',
    location: 'San Francisco, CA',
    email: 'sarah.anderson@example.com',
    company: 'Tech Innovations Inc',
    skills: ['React', 'TypeScript', 'Node.js', 'Python', 'AWS', 'Docker'],
    github: 'https://github.com',
    linkedin: 'https://linkedin.com',
})

const posts = ref([]);

const addPost = async (newPost) => {
    try {
        const createdPost = await createPost(newPost);
        posts.value.unshift(createdPost);
    } catch (error) {
        console.error('Failed to create post:', error);
    }
};

const handlePostDeleted = (postId) => {
    posts.value = posts.value.filter(post => post.id !== postId);
};

onMounted(async () => {
    const cacheKey = 'posts_data';
    const cacheTimestampKey = 'posts_data_timestamp';
    const cacheDuration = 10 * 60 * 1000;

    const cachedData = localStorage.getItem(cacheKey);
    const cachedTimestamp = localStorage.getItem(cacheTimestampKey);

    if (cachedData && cachedTimestamp && Date.now() - cachedTimestamp < cacheDuration) {
        posts.value = JSON.parse(cachedData);
    } else {
        try {
            posts.value = await fetchPosts();
            localStorage.setItem(cacheKey, JSON.stringify(posts.value));
            localStorage.setItem(cacheTimestampKey, Date.now());
        } catch (error) {
            console.error('Failed to fetch posts:', error);
        }
    }
});


</script>

<template>
    <Head title="News" />

    <AuthenticatedLayout>
        <div class="py-6">
            <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8 px-3">
                <div class="overflow-hidden sm:rounded-lg flex flex-col gap-6 bg-grey-800">
                    <CreatePost @post-created="addPost" />

                    <Post class="p-6 md:p-8"
                        v-for="post in posts"
                        :key="post.id"
                        :post="post"
                        :profile="profile"
                        @post-deleted="handlePostDeleted"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
