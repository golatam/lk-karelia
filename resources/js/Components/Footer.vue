<template>
    <footer class="tw-bg-white tw-border-t tw-border-gray-200 tw-py-4 tw-px-6">
        <div class="tw-flex tw-flex-col sm:tw-flex-row tw-items-center tw-justify-between tw-gap-2 tw-text-sm tw-text-gray-500">
            <span>&copy; {{ year }} Личный кабинет ИБ РК</span>
            <span>Министерство национальной и региональной политики Республики Карелия</span>
        </div>
    </footer>
</template>

<script setup>
const year = new Date().getFullYear();
</script>
