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
import { deletePost } from "@/Services/api";

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

const isPopupOpen = ref(false);
const isDeleteModalOpen = ref(false);

const togglePopup = () => {
    isPopupOpen.value = !isPopupOpen.value;
};


const openDeleteModal = () => {
    isDeleteModalOpen.value = true;
    isPopupOpen.value = false;
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
};

const confirmDelete = async () => {
    try {
        await deletePost(props.post.id);
        emit('post-deleted', props.post.id);
        closeDeleteModal();
    } catch (error) {
        console.error('Failed to delete post:', error);
    }
};

const emit = defineEmits(['post-deleted']);

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
            <div class="relative">
                <!-- Кнопка с троеточием -->
                <button
                    @click="togglePopup"
                    class="p-2 rounded-full hover:bg-gray-200 transition-colors rotate-90 btn"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-gray-800"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"
                        />
                    </svg>
                </button>

                <!-- Выпадающее меню -->
                <div
                    v-if="isPopupOpen"
                    class="absolute right-0 mt-2 w-24 bg-white rounded-lg shadow-lg z-10 border border-gray-200 pointer-events-auto"
                >
                    <ul>
                        <li>
                            <button
                                @click="openDeleteModal"
                                class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                                Delete
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <transition name="modal">
            <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Delete Post</h2>
                        <button
                            @click="closeDeleteModal"
                            class="absolute -top-10 -right-10 text-gray-600 hover:text-gray-900 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg"
                            style="font-size: 24px"
                        >
                            &times;
                        </button>
                    </div>

                    <p class="text-gray-600 mb-6">
                        Are you sure you want to delete this post? This action cannot be undone.
                    </p>

                    <div class="flex justify-end gap-4">
                        <button
                            @click="closeDeleteModal"
                            class="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg"
                        >
                            Cancel
                        </button>
                        <PrimaryButton
                            @click="confirmDelete"
                        >
                            Delete
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </transition>

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
.btn:hover {
    background-color: rgba(229, 231, 235, 1);
}
div.absolute {
    pointer-events: auto;
}
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
