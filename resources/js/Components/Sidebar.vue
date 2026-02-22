<template>
    <!-- Mobile overlay -->
    <Transition
        enter-active-class="tw-transition-opacity tw-duration-300"
        enter-from-class="tw-opacity-0"
        enter-to-class="tw-opacity-100"
        leave-active-class="tw-transition-opacity tw-duration-300"
        leave-from-class="tw-opacity-100"
        leave-to-class="tw-opacity-0"
    >
        <div
            v-if="open"
            class="tw-fixed tw-inset-0 tw-bg-black/50 tw-z-40 lg:tw-hidden"
            @click="$emit('close')"
        ></div>
    </Transition>

    <!-- Sidebar -->
    <aside
        class="tw-bg-[#263238] tw-overflow-y-auto tw-flex-shrink-0 tw-z-50"
        :class="[
            open
                ? 'tw-fixed tw-inset-y-0 tw-left-0 tw-w-[300px] lg:tw-static lg:tw-w-1/4'
                : 'tw-hidden lg:tw-block lg:tw-w-1/4',
        ]"
    >
        <!-- Mobile close button -->
        <div class="tw-flex tw-items-center tw-justify-end tw-p-3 lg:tw-hidden">
            <button
                class="tw-text-gray-400 hover:tw-text-white tw-text-xl tw-bg-transparent tw-border-0 tw-cursor-pointer"
                @click="$emit('close')"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <nav class="tw-py-2">
            <ul class="tw-list-none tw-m-0 tw-p-0">
                <SidebarItem
                    v-for="(item, idx) in items"
                    :key="idx"
                    :item="item"
                />
            </ul>
        </nav>
    </aside>
</template>

<script setup>
import SidebarItem from './SidebarItem.vue';

defineProps({
    items: { type: Array, default: () => [] },
    open: { type: Boolean, default: false },
});

defineEmits(['close']);
</script>
