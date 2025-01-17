import axios from 'axios';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL;

const apiClient = axios.create({
    baseURL: API_BASE_URL,
});

apiClient.interceptors.response.use(
    (response) => {
        if (response.data) {
            return { data: response.data };
        }
        return response;
    },
    (error) => {
        console.error('There was a problem with the fetch operation:', error);
        return Promise.reject(error);
    }
);

const getCachedData = (key) => {
    const cachedData = localStorage.getItem(key);
    if (cachedData) {
        return JSON.parse(cachedData);
    }
    return null;
};

const setCachedData = (key, data) => {
    localStorage.setItem(key, JSON.stringify(data));
};

export const fetchPosts = async () => {
    const cacheKey = 'posts_data';
    const cacheDuration = 10 * 60 * 1000;

    const cachedData = getCachedData(cacheKey);
    const cachedTimestamp = getCachedData(`${cacheKey}_timestamp`);

    if (cachedData && cachedTimestamp && Date.now() - cachedTimestamp < cacheDuration) {
        return cachedData;
    }

    try {
        const response = await apiClient.get('/posts');

        setCachedData(cacheKey, response.data);
        setCachedData(`${cacheKey}_timestamp`, Date.now());

        return response.data;
    } catch (error) {
        throw error;
    }
};

export const createPost = async (postData) => {
    try {
        const response = await apiClient.post('/posts', postData);

        localStorage.removeItem('posts_data');
        localStorage.removeItem('posts_data_timestamp');

        return response.data;
    } catch (error) {
        throw error;
    }
};
