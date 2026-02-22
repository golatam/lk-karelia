<template>
    <li v-if="item.isPermitted">
        <a
            :href="item.url"
            :target="isExternal ? '_blank' : undefined"
            :rel="isExternal ? 'noopener noreferrer' : undefined"
            class="tw-flex tw-items-center tw-gap-2 tw-px-4 tw-py-2 tw-text-sm tw-no-underline tw-transition-colors tw-duration-150"
            :class="[
                item.isActive && !item.isSubmenu
                    ? 'tw-bg-white/10 tw-text-white'
                    : 'tw-text-gray-400 hover:tw-text-white hover:tw-bg-white/5',
                item.level === 0 ? 'tw-text-sm tw-font-medium' : 'tw-text-xs',
                item.level > 0 ? 'tw-pl-' + (4 + item.level * 4) : '',
            ]"
            :style="item.level > 0 ? { paddingLeft: (16 + item.level * 16) + 'px' } : {}"
            @click.prevent="handleClick"
        >
            <i
                v-if="item.icon && item.level === 0"
                :class="'fas fa-' + item.icon"
                class="tw-w-5 tw-text-center tw-text-gray-500"
            ></i>
            <span class="tw-flex-1">{{ item.name }}</span>
            <i
                v-if="item.isSubmenu"
                class="fas fa-chevron-right tw-text-xs tw-text-gray-500 tw-transition-transform tw-duration-200"
                :class="{ 'tw-rotate-90': open }"
            ></i>
        </a>

        <ul
            v-if="item.isSubmenu"
            v-show="open"
            class="tw-list-none tw-m-0 tw-p-0"
        >
            <SidebarItem
                v-for="(child, idx) in item.submenu"
                :key="idx"
                :item="child"
            />
        </ul>
    </li>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    item: { type: Object, required: true },
});

const open = ref(props.item.isOpen || false);

const isExternal = computed(() => {
    const url = props.item.url || '';
    return url.startsWith('//') || url.startsWith('http://') || url.startsWith('https://');
});

function handleClick() {
    if (props.item.isSubmenu) {
        open.value = !open.value;
    } else if (isExternal.value) {
        window.open(props.item.url, '_blank', 'noopener,noreferrer');
    } else if (props.item.url && props.item.url !== 'javascript:void(0);') {
        window.location.href = props.item.url;
    }
}
</script>
