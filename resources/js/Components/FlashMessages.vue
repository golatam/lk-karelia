<template>
    <div class="tw-fixed tw-top-4 tw-right-4 tw-z-50 tw-flex tw-flex-col tw-gap-2 tw-max-w-sm">
        <TransitionGroup
            enter-active-class="tw-transition tw-duration-300 tw-ease-out"
            enter-from-class="tw-translate-x-full tw-opacity-0"
            enter-to-class="tw-translate-x-0 tw-opacity-100"
            leave-active-class="tw-transition tw-duration-200 tw-ease-in"
            leave-from-class="tw-translate-x-0 tw-opacity-100"
            leave-to-class="tw-translate-x-full tw-opacity-0"
        >
            <div
                v-for="msg in messages"
                :key="msg.id"
                :class="typeClasses[msg.type]"
                class="tw-rounded tw-px-4 tw-py-3 tw-shadow-lg tw-text-white tw-text-sm tw-flex tw-items-start tw-gap-2"
            >
                <i :class="typeIcons[msg.type]" class="tw-mt-0.5"></i>
                <span class="tw-flex-1">{{ msg.text }}</span>
                <button
                    class="tw-ml-2 tw-text-white tw-opacity-70 hover:tw-opacity-100"
                    @click="dismiss(msg.id)"
                >&times;</button>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const messages = ref([]);
let nextId = 0;

const typeClasses = {
    success: 'tw-bg-green-600',
    error: 'tw-bg-red-600',
    warning: 'tw-bg-orange-500',
};

const typeIcons = {
    success: 'fas fa-check-circle',
    error: 'fas fa-exclamation-circle',
    warning: 'fas fa-exclamation-triangle',
};

function addMessage(type, text) {
    if (!text) return;
    const id = nextId++;
    messages.value.push({ id, type, text });
    setTimeout(() => dismiss(id), 5000);
}

function dismiss(id) {
    messages.value = messages.value.filter(m => m.id !== id);
}

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) addMessage('success', flash.success);
        if (flash?.error) addMessage('error', flash.error);
        if (flash?.warning) addMessage('warning', flash.warning);
    },
    { immediate: true }
);
</script>
