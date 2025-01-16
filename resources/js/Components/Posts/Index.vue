<script setup>
import Avatar from '@/Components/UI/Avatar.vue';
import Card from '@/Components/UI/Card.vue';
import Heart from '@/Components/UI/Icons/Heart.vue';
import Comment from '@/Components/UI/Icons/Comment.vue';
import Eye from "@/Components/UI/Icons/Eye.vue";
import { onMounted, ref } from 'vue';
import ImageModal from './ImageModal.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    profile: {
        type: Object,
        required: true,
    },
    imageMaxWidth: {
        type: String,
        default: '800px',
    },
    imageMaxHeight: {
        type: String,
        default: '600px',
    },
});

const isImageModalOpen = ref(false);
const selectedImage = ref('');

const likedPosts = ref([]);

const comments = ref([]);
const newComment = ref('');

const toggleLike = (postId) => {
    if (likedPosts.value.includes(postId)) {
        likedPosts.value = likedPosts.value.filter(id => id !== postId);
    } else {
        likedPosts.value.push(postId);
    }
};

const isLiked = (postId) => likedPosts.value.includes(postId);

const addComment = () => {
    if (newComment.value.trim()) {
        comments.value.push({
            id: Date.now(),
            author: props.profile.name,
            avatar: props.profile.avatar,
            text: newComment.value.trim(),
            date: new Date().toLocaleDateString('en-GB', {day: 'numeric', month: 'long', year: 'numeric'}),
        });

        props.post.commentsCount += 1;
        newComment.value = '';
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

onMounted(() => {
    const videoPlayers = document.querySelectorAll('video');
    videoPlayers.forEach((player) => {
        new Plyr(player);
    });
});
</script>

<template>
    <Card class="bg-gray-800 p-4 shadow-sm">
        <div class="flex items-center mb-4">
            <Avatar class="w-14 h-14 mr-3">
                <img :src="profile.avatar" :alt="profile.name" class="object-cover h-full w-full"/>
            </Avatar>
            <div class="flex-1">
                <div class="flex gap-1 flex-col items-start">
                    <span class="font-semibold text-md">{{ profile.name }}</span>
                    <span class="text-sm">{{ post.date }}</span>
                </div>
            </div>
        </div>

        <div class="overflow-hidden mb-4 flex justify-center max-h-[600px] rounded-2xl" v-if="post.file_url">
            <img
                v-if="post.file_type.startsWith('image')"
                :src="post.file_url"
                alt=""
                class="object-cover rounded cursor-pointer"
                :style="{
                    maxWidth: '100%',
                    maxHeight: '600px',
                    height: 'auto',
                    objectFit: 'cover',
                    borderRadius: '14px',
                }"
                @click="(event) => { event.preventDefault(); openImageModal(post.file_url); }"
            />
            <video
                v-else-if="post.file_type.startsWith('video')"
                ref="videoPlayer"
                controls
                class="rounded-lg w-full"
            >
                <source :src="post.file_url" :type="post.file_type"/>
                Your browser does not support the video tag.
            </video>
        </div>

        <p class="text-gray-900 whitespace-pre-line mb-4 leading-relaxed">
            {{ post.content }}
        </p>

        <div class="flex items-center justify-between text-sm text-gray-400">
            <div class="flex items-center gap-4">

                <button @click="toggleLike(post.id)" class="inline-flex items-center gap-1">
                    <Heart :class="{ 'text-red-500': isLiked(post.id) }"/>
                    <span :class="{ 'text-red-500': isLiked(post.id) }">
                        {{ isLiked(post.id) ? post.likes + 1 : post.likes }}
                    </span>
                </button>

                <span class="inline-flex items-center gap-1"><Comment/>{{ post.comments || 0 }}</span>
            </div>

            <span class="flex gap-1 items-center"><Eye/>{{ post.watches || 0 }}</span>
        </div>

        <div class="mt-4">
            <TextArea
                v-model="newComment"
                placeholder="Write a comment..."
                :rows="2"
                :required="true"
            />
            <PrimaryButton
                @click="addComment"
            >
                Add Comment
            </PrimaryButton>
        </div>

        <div class="mt-4 space-y-4">
            <div v-for="comment in comments" :key="comment.id"
                 class="flex items-start gap-3 p-3 bg-[#3741510d] rounded-lg shadow-sm">
                <Avatar class="w-8 h-8">
                    <img :src="comment.avatar" :alt="comment.author" class="object-cover"/>
                </Avatar>
                <div>
                    <div class="flex gap-1">
                        <div class="text-sm font-semibold">{{ comment.author }}</div>
                        <div class="text-sm text-gray-600">{{ comment.date }}</div>
                    </div>
                    <p class="text-sm text-gray-900">{{ comment.text }}</p>
                </div>
            </div>
        </div>
    </Card>

    <ImageModal
        :isImageModalOpen="isImageModalOpen"
        :selectedImage="selectedImage"
        @close="closeImageModal"
        style="margin-top: unset"
    />
</template>
<style scoped>
.plyr__video-wrapper {
    border-radius: 14px;
}
</style>
