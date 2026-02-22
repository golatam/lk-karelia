<script>
import AppLayout from '../Layouts/AppLayout.vue';
export default { layout: AppLayout };
</script>

<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Главная" />

    <div>
        <h1 class="tw-text-2xl tw-font-bold tw-mb-4" style="color: #364364;">Главная страница</h1>

        <!-- Panel with external links -->
        <div class="tw-bg-white tw-rounded tw-shadow-sm tw-mb-6">
            <div class="tw-flex tw-flex-wrap tw-items-center tw-justify-between tw-px-4 tw-py-3 tw-border-b tw-border-gray-200">
                <span class="tw-text-sm tw-text-gray-600 tw-font-medium">Республика Карелия</span>
                <div class="tw-flex tw-flex-wrap tw-gap-2">
                    <a
                        href="https://асмо-карелия.рф/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-no-underline tw-px-2 tw-py-1"
                    >
                        <i class="fas fa-globe"></i>
                        АСМО Карелия
                    </a>
                    <a
                        href="https://vk.com/ppmi_rk"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-no-underline tw-px-2 tw-py-1"
                    >
                        <i class="fab fa-vk"></i>
                        ППМИ ВКонтакте
                    </a>
                    <a
                        href="https://инициативы-карелии.рф"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-no-underline tw-px-2 tw-py-1"
                    >
                        <i class="fas fa-globe"></i>
                        Инициативы Карелии
                    </a>
                    <a
                        href="https://vk.com/tosrk"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="tw-inline-flex tw-items-center tw-gap-1 tw-text-sm tw-text-blue-600 hover:tw-text-blue-800 tw-no-underline tw-px-2 tw-py-1"
                    >
                        <i class="fab fa-vk"></i>
                        ТОС ВКонтакте
                    </a>
                </div>
            </div>
        </div>

        <!-- Main content text -->
        <div class="tw-bg-white tw-rounded tw-shadow-sm tw-p-6 tw-text-sm tw-leading-relaxed" style="color: #364364;">
            <h3 class="tw-text-lg tw-font-semibold tw-mb-3">Дорогие друзья!</h3>

            <p class="tw-mb-3">Вы находитесь в личном кабинете инициативного бюджетирования (ЛК ИБ).</p>
            <p class="tw-mb-3">Что такое практики инициативного бюджетирования Республики Карелия?</p>
            <p class="tw-mb-3">Это совокупность проектов, внедренных в жизнь региона в разные годы.</p>
            <p class="tw-mb-3">В Республике Карелия реализуется несколько практик инициативного бюджетирования.</p>

            <p class="tw-mb-3">
                <strong>ППМИ</strong> - программа поддержки местных инициатив, стартовала в Республике Карелия в 2014 году.
                ППМИ предусматривает выделение на конкурсной основе субсидий из республиканского бюджета на реализацию проектов,
                наиболее важных для территорий, направленных на решение вопросов местного значения.
            </p>

            <p class="tw-mb-3">
                <strong>ТОС</strong> (Территориальное общественное самоуправление) - это форма самоорганизации граждан по месту
                их жительства на части территории муниципального образования. К таким территориям относятся: микрорайон, квартал,
                улица, двор, дом. С 2018 года реализуются при активном участии жителей различные конкурсы ТОС.
            </p>

            <p class="tw-mb-3">
                <strong>НАРОДНЫЙ БЮДЖЕТ</strong> - это возможность жителей Республики Карелия самостоятельно распределить
                часть бюджетных средств. Вы предлагаете идеи, как изменить место, где вы живете, вы работаете над своими
                проектами - и вы выбираете инициативы, которые в итоге воплощаете в жизнь вместе с органами власти.
                Практика реализуется в республике с 2020 года.
            </p>

            <p class="tw-mb-3">
                Кроме того, Ассоциация &laquo;Совет муниципальных образований&raquo; предлагает органам местного самоуправления
                участвовать в различных конкурсах, целью которых является поддержка и развитие общественных инициатив, решение
                социально &ndash; экономических вопросов на местном уровне, улучшение качества жизни населения.
            </p>

            <p class="tw-mb-3">
                Личный кабинет ИБ дает возможность администрациям муниципальных образований при непосредственном участии населения
                подать в электронном виде заявки для участия в конкурсах:
            </p>

            <ul class="tw-list-disc tw-pl-6 tw-mb-3 tw-space-y-1">
                <li>ППМИ,</li>
                <li>социально значимых проектов ТОС,</li>
                <li>&laquo;Лучшая практика гражданских инициатив&raquo;,</li>
                <li>&laquo;Лучшее ТОС&raquo;,</li>
                <li>&laquo;Лучший специалист местного самоуправления&raquo;,</li>
                <li>&laquo;Самое красивое село (поселок, деревня) Республики Карелия&raquo;,</li>
                <li>&laquo;Лучший праздник села (поселка, деревни)&raquo;.</li>
            </ul>

            <p class="tw-mb-3">При организации новых конкурсов в ЛК ИБ будут появляться новые формы электронных заявок.</p>

            <p class="tw-mb-3">Кроме того, в ЛК ИБ планируется реализация управления реестром ТОС Республики Карелия, а именно:</p>

            <ul class="tw-list-disc tw-pl-6 tw-space-y-1">
                <li>регистрация ТОС,</li>
                <li>изменение данных ТОС,</li>
                <li>возможность распечатать необходимые документы по ТОС.</li>
            </ul>
        </div>
    </div>
</template>
