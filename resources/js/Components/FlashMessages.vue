<script setup>
import FlashMessage from '@/Components/FlashMessage.vue';
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

let notifications = ref(usePage().props.flash);

router.on('success', (event) => {
    let page_flash = usePage().props.flash;

    page_flash.forEach((flash_message) => {
        notifications.value.push(flash_message);
    });
});

Echo.private(`App.Models.User.${usePage().props.user.id}`).notification((notification) => {
    notifications.value.push(notification);
});
</script>

<template>
    <div class="pointer-events-none fixed inset-0 z-[90] px-4 py-6 sm:p-6">
        <div class="flex min-w-min flex-col items-end justify-start space-y-2">
            <FlashMessage v-for="(message, message_key) in notifications" :key="message_key" :message="message" />
        </div>
    </div>
</template>
