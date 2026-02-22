<template>
    <div
        class="tw-flex tw-flex-col tw-h-screen"
        style="font-family: Arial, Helvetica, sans-serif; color: #364364; background-color: #eceef3;"
    >
        <Header :user="page.props.auth?.user" @toggle-sidebar="sidebarOpen = !sidebarOpen" />

        <div class="tw-flex tw-flex-1 tw-overflow-hidden">
            <Sidebar
                :items="page.props.sidebar"
                :open="sidebarOpen"
                @close="sidebarOpen = false"
            />

            <main class="tw-flex-1 tw-overflow-y-auto tw-p-6">
                <slot />
            </main>
        </div>

        <Footer />
        <FlashMessages />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Header from '../Components/Header.vue';
import Sidebar from '../Components/Sidebar.vue';
import Footer from '../Components/Footer.vue';
import FlashMessages from '../Components/FlashMessages.vue';

const page = usePage();
const sidebarOpen = ref(false);
</script>
