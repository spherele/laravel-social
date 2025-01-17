<script setup>
import {onMounted, ref} from 'vue'
import { Mail, MapPin, Briefcase, Github, Linkedin } from 'lucide-vue-next'
import Avatar from '@/Components/UI/Avatar.vue'
import Card from '@/Components/UI/Card.vue'
import Badge from '@/Components/UI/Badge.vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import {Head} from "@inertiajs/vue3"
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Post from "@/Components/Posts/Index.vue"
import {fetchPosts} from "@/Services/api";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const profile = ref(props.user);

const handleAvatarUploaded = (newAvatar) => {
    profile.value.avatar = newAvatar;
};

const posts = ref([]);

const isModalOpen = ref(false)

const openModal = () => {
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
}
</script>

<template>
    <Head title="Profile"/>

    <AuthenticatedLayout>
        <div class="min-h-screen bg-grey-100 py-6">
            <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8 px-3">
                <!-- Profile Header -->
                <Card class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                        <Avatar :editable="true" class="w-60 h-60" @avatar-uploaded="handleAvatarUploaded">
                            <img
                                :src="profile.avatar"
                                :alt="profile.name"
                                class="object-cover w-full h-full"
                            />
                        </Avatar>

                        <div class="flex-1 text-center md:text-left">
                            <h1 class="text-3xl font-bold text-gray-900">
                                {{ profile.name }}
                            </h1>

                            <p v-if="profile.role" class="text-lg text-gray-600 mt-1">
                                {{ profile.role }}
                            </p>

                            <div class="flex flex-wrap gap-2 mt-4 justify-center md:justify-start">
                                <!-- Location -->
                                <div v-if="profile.location" class="flex items-center text-gray-600">
                                    <MapPin class="w-4 h-4 mr-1"/>
                                    {{ profile.location }}
                                </div>
                                <!-- Company -->
                                <div v-if="profile.company" class="flex items-center text-gray-600">
                                    <Briefcase class="w-4 h-4 mr-1"/>
                                    {{ profile.company }}
                                </div>
                            </div>

                            <!-- Соц. ссылки -->
                            <div class="flex gap-3 mt-6 justify-center md:justify-start">
                                <a v-if="profile.github" :href="profile.github" class="text-gray-600 hover:text-gray-900 transition-colors"
                                   target="_blank" rel="noopener noreferrer">
                                    <Github class="w-6 h-6"/>
                                </a>
                                <a v-if="profile.linkedin" :href="profile.linkedin" class="text-gray-600 hover:text-gray-900 transition-colors"
                                   target="_blank" rel="noopener noreferrer">
                                    <Linkedin class="w-6 h-6"/>
                                </a>
                            </div>

                            <!-- Кнопка для открытия модального окна -->
                            <PrimaryButton @click="openModal" class="mt-6 px-4 py-2 transition-colors">
                                Show more information
                            </PrimaryButton>
                        </div>
                    </div>
                </Card>

                <transition name="modal">
                    <div v-if="isModalOpen"
                         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                         style="margin-top: unset">
                        <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
                            <div class="flex justify-between items-center mb-4 relative">
                                <h2 class="text-xl font-semibold">Information</h2>
                                <button
                                    @click="closeModal"
                                    class="absolute -top-10 -right-10 text-gray-600 hover:text-gray-900 bg-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg"
                                    style="font-size: 24px"
                                >
                                    &times;
                                </button>
                            </div>

                            <div class="mb-6 flex gap-2 items-flex-start">
                                <h3 class="text-lg font-semibold mb-2 flex gap-2 items-center"><Mail /> Mail</h3>
                                <a :href="`mailto:${profile.email}`"  class="text-gray-600 leading-relaxed text-lg">{{ profile.email}}</a>
                            </div>


                            <div class="mb-6" v-if="profile.about">
                                <h3 class="text-lg font-semibold mb-2">About</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ profile.about }}
                                </p>
                            </div>

                            <div v-if="profile.skills">
                                <h3 class="text-lg font-semibold mb-2">Skills & Expertise</h3>
                                <div class="flex flex-wrap gap-2">
                                    <Badge v-for="(item, index) in profile.skills" :key="index" variant="secondary"
                                           class="text-sm">
                                        {{ item.skill }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>

                <!-- Посты -->
                <Post
                    v-for="post in posts"
                    :key="post.id"
                    :post="post"
                    :profile="profile"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .modal-content,
.modal-leave-active .modal-content {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
    transform: translateY(-20px) scale(0.9);
    opacity: 0;
}
</style>
