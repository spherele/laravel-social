<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// Функция для обновления токена
const refreshToken = async () => {
    try {
        const response = await axios.post('/api/auth/refresh', {}, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('access_token')}`,
            },
        });
        localStorage.setItem('access_token', response.data.access_token);
        return response.data.access_token;
    } catch (error) {
        console.error('Failed to refresh token:', error);
        throw error;
    }
};

axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const originalRequest = error.config;

        if (error.response.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;

            try {
                const newToken = await refreshToken();
                originalRequest.headers.Authorization = `Bearer ${newToken}`;
                return axios(originalRequest);
            } catch (refreshError) {
                console.error('Failed to refresh token:', refreshError);
                localStorage.removeItem('access_token');
                router.visit('/login');
                return Promise.reject(refreshError);
            }
        }

        return Promise.reject(error);
    }
);

const login = () => {
    axios.post('/api/auth/login', {
        email: form.email,
        password: form.password,
    })
        .then(response => {
            localStorage.setItem('access_token', response.data.access_token);

            return axios.post('/api/auth/me', {}, {
                headers: {
                    Authorization: `Bearer ${response.data.access_token}`,
                },
            });
        })
        .then(userResponse => {
            const user = userResponse.data;
            console.log('User data:', user);
            router.visit('/news');
        })
        .catch(error => {
            if (error.response && error.response.data.error) {
                form.setError('email', error.response.data.error);
            } else {
                console.error('Login failed:', error);
            }
        });
};

const submit = () => {
    form.post(route('login'), {
        onSuccess: () => login(),
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4 super-last">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Forgot your password?
                </Link>

                <Link class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md
                            font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700
                            active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                            transition ease-in-out duration-150  ms-4"

                      :href="route('register')">Register
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scope>
.super-last {
    @media (max-width: 768px) {
        flex-direction: column-reverse;
        width: 100%;
        gap: 10px;
        & > a, button {
            width: 100%;
            margin: unset;
        }
    }
}
</style>
