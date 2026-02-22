<template>
    <header class="tw-bg-white tw-shadow-sm tw-flex tw-items-center tw-justify-between tw-px-4 tw-py-3 tw-relative tw-z-30">
        <!-- Left: hamburger + logo -->
        <div class="tw-flex tw-items-center tw-gap-3">
            <button
                class="lg:tw-hidden tw-text-gray-600 hover:tw-text-gray-900 tw-text-xl tw-bg-transparent tw-border-0 tw-cursor-pointer"
                @click="$emit('toggle-sidebar')"
            >
                <i class="fas fa-bars"></i>
            </button>

            <a href="/dashboard" class="tw-flex tw-items-center tw-gap-2 tw-no-underline tw-text-[#364364]">
                <img
                    src="/assets/images/favicon.png"
                    alt="Logo"
                    class="tw-h-8 tw-w-8"
                />
                <span class="tw-font-semibold tw-text-lg tw-hidden sm:tw-inline">
                    Личный кабинет ИБ РК
                </span>
            </a>
        </div>

        <!-- Right: profile dropdown -->
        <div v-if="user" v-click-outside="closeDropdown" class="tw-relative">
            <button
                class="tw-flex tw-items-center tw-gap-2 tw-bg-transparent tw-border-0 tw-cursor-pointer tw-text-gray-600 hover:tw-text-gray-900 tw-py-1 tw-px-2 tw-rounded"
                @click="toggleDropdown"
            >
                <img
                    v-if="user.avatar_url"
                    :src="user.avatar_url"
                    :alt="user.full_name"
                    class="tw-h-8 tw-w-8 tw-rounded-full tw-object-cover"
                />
                <span
                    v-else
                    class="tw-h-8 tw-w-8 tw-rounded-full tw-bg-[#263238] tw-text-white tw-flex tw-items-center tw-justify-center tw-text-sm tw-font-medium"
                >
                    {{ initials }}
                </span>
                <span class="tw-text-sm tw-hidden md:tw-inline">{{ user.full_name }}</span>
                <i class="fas fa-chevron-down tw-text-xs"></i>
            </button>

            <Transition
                enter-active-class="tw-transition tw-duration-150 tw-ease-out"
                enter-from-class="tw-opacity-0 tw-scale-95"
                enter-to-class="tw-opacity-100 tw-scale-100"
                leave-active-class="tw-transition tw-duration-100 tw-ease-in"
                leave-from-class="tw-opacity-100 tw-scale-100"
                leave-to-class="tw-opacity-0 tw-scale-95"
            >
                <div
                    v-show="dropdownOpen"
                    class="tw-absolute tw-right-0 tw-top-full tw-mt-1 tw-w-48 tw-bg-white tw-rounded tw-shadow-lg tw-border tw-border-gray-200 tw-py-1 tw-z-50"
                >
                    <a
                        href="/profile"
                        class="tw-block tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 hover:tw-bg-gray-100 tw-no-underline"
                    >
                        <i class="fas fa-user tw-mr-2 tw-text-gray-400"></i>
                        Профиль
                    </a>
                    <button
                        class="tw-w-full tw-text-left tw-block tw-px-4 tw-py-2 tw-text-sm tw-text-gray-700 hover:tw-bg-gray-100 tw-bg-transparent tw-border-0 tw-cursor-pointer"
                        @click="logout"
                    >
                        <i class="fas fa-sign-out-alt tw-mr-2 tw-text-gray-400"></i>
                        Выход
                    </button>
                </div>
            </Transition>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    user: { type: Object, default: null },
});

defineEmits(['toggle-sidebar']);

const dropdownOpen = ref(false);

const initials = computed(() => {
    if (!props.user?.full_name) return '?';
    return props.user.full_name
        .split(' ')
        .slice(0, 2)
        .map(w => w[0])
        .join('')
        .toUpperCase();
});

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

function closeDropdown() {
    dropdownOpen.value = false;
}

function logout() {
    router.post('/logout');
}
</script>
