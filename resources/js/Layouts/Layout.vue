<template>
    <div>
        <jet-banner />

        <div>
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="flex justify-start h-16 w-max">
                        <div class="flex justify-start text-2xl">
                            <!-- Logo -->
                            <div class="flex items-center flex-shrink-0">
                                <inertia-link :href="route('dashboard')">
                                    <jet-application-mark
                                        class="block w-auto h-9"
                                    />
                                </inertia-link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <jet-nav-link
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </jet-nav-link>
                            </div>

                            <div
                                v-if="$page.props.user.scheme_id !== 3"
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <jet-nav-link
                                    :href="route('shelf')"
                                    :active="route().current('shelf')"
                                >
                                    My Shelf
                                </jet-nav-link>
                            </div>
                            <div
                                v-if="$page.props.user.scheme_id === 3"
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <jet-nav-link
                                    :href="route('maps')"
                                    :active="route().current('maps')"
                                >
                                    My Maps
                                </jet-nav-link>
                            </div>
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <form @submit.prevent="masterShelf" class="mt-4">
                                    <jet-dropdown-link as="button">
                                        Inventory List
                                    </jet-dropdown-link>
                                </form>
                            </div>
                            <div
                                v-if="$page.props.user.privs === 2"
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <form @submit.prevent="masterShelf" class="mt-4">
                                    <jet-dropdown-link as="button">
                                        Inventory List
                                    </jet-dropdown-link>
                                </form>
                            </div>

                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <jet-nav-link
                                    :href="route('instructions')"
                                    :active="
                                        $page.props.currentRouteName ==
                                        'instructions'
                                    "
                                >
                                    Instructions
                                </jet-nav-link>
                            </div>
                            <div
                                v-if="$page.props.user.privs === 1"
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <jet-nav-link
                                    :href="route('super.admin')"
                                    :active="
                                        $page.props.currentRouteName ==
                                        'super.admin'
                                    "
                                >
                                    Admin Page
                                </jet-nav-link>
                            </div>

                            <div
                                class="pt-4 space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <form @submit.prevent="emptyTables">
                                    <jet-dropdown-link as="button">
                                        Clear Tables
                                    </jet-dropdown-link>
                                </form>
                            </div>

                            <div
                                class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <jet-nav-link
                                    :href="route('inventory-report')"
                                    :active="
                                        route().current('inventory-report')
                                    "
                                >
                                    Missing Item Report
                                </jet-nav-link>
                            </div>
                            <div
                                class="pt-4 space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <form @submit.prevent="logout">
                                    <jet-dropdown-link as="button">
                                        Log Out
                                    </jet-dropdown-link>
                                </form>
                            </div>
                            <div
                                class="pt-6 space-x-8 sm:-my-px sm:ml-10 sm:flex"
                            >
                                <span class="text-base">{{
                                    $page.props.user.name
                                }}</span>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            <div class="relative ml-3">
                                <!-- Teams Dropdown -->
                                <jet-dropdown
                                    align="right"
                                    width="60"
                                    v-if="$page.props.jetstream.hasTeamFeatures"
                                >
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-transparent leading-4 rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition"
                                            >
                                                {{
                                                    $page.props.user
                                                        .current_team.name
                                                }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template
                                                v-if="
                                                    $page.props.jetstream
                                                        .hasTeamFeatures
                                                "
                                            >
                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    Manage Team
                                                </div>

                                                <!-- Team Settings -->
                                                <jet-dropdown-link
                                                    :href="
                                                        route(
                                                            'teams.show',
                                                            $page.props.user
                                                                .current_team
                                                        )
                                                    "
                                                >
                                                    Team Settings
                                                </jet-dropdown-link>

                                                <jet-dropdown-link
                                                    :href="
                                                        route('teams.create')
                                                    "
                                                    v-if="
                                                        $page.props.jetstream
                                                            .canCreateTeams
                                                    "
                                                >
                                                    Create New Team
                                                </jet-dropdown-link>

                                                <div
                                                    class="border-t border-gray-100"
                                                ></div>

                                                <!-- Team Switcher -->
                                                <div
                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                >
                                                    Switch Teams
                                                </div>

                                                <template
                                                    v-for="team in $page.props
                                                        .user.all_teams"
                                                    :key="team.id"
                                                >
                                                    <form
                                                        @submit.prevent="
                                                            switchToTeam(team)
                                                        "
                                                    >
                                                        <jet-dropdown-link
                                                            as="button"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <svg
                                                                    v-if="
                                                                        team.id ==
                                                                        $page
                                                                            .props
                                                                            .user
                                                                            .current_team_id
                                                                    "
                                                                    class="w-5 h-5 mr-2 text-green-400"
                                                                    fill="none"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    stroke="currentColor"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                    ></path>
                                                                </svg>
                                                                <div>
                                                                    {{
                                                                        team.name
                                                                    }}
                                                                </div>
                                                            </div>
                                                        </jet-dropdown-link>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ml-3">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                        >
                                            <img
                                                class="object-cover w-8 h-8 rounded-full"
                                                :src="
                                                    $page.props.user
                                                        .profile_photo_url
                                                "
                                                :alt="$page.props.user.name"
                                            />
                                        </button>

                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-transparent leading-4 rounded-md hover:text-gray-700 focus:outline-none transition"
                                            >
                                                {{ $page.props.user.name }}

                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Manage Account
                                        </div>

                                        <jet-dropdown-link
                                            :href="route('profile.show')"
                                        >
                                            Profile
                                        </jet-dropdown-link>

                                        <jet-dropdown-link
                                            :href="route('api-tokens.index')"
                                            v-if="
                                                $page.props.jetstream
                                                    .hasApiFeatures
                                            "
                                        >
                                            API Tokens
                                        </jet-dropdown-link>

                                        <div
                                            class="border-t border-gray-100"
                                        ></div>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button">
                                                Log Out
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center -mr-2 sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
                            >
                                <svg
                                    class="w-6 h-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <jet-responsive-nav-link
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </jet-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div
                                v-if="
                                    $page.props.jetstream.managesProfilePhotos
                                "
                                class="flex-shrink-0 mr-3"
                            >
                                <img
                                    class="object-cover w-10 h-10 rounded-full"
                                    :src="$page.props.user.profile_photo_url"
                                    :alt="$page.props.user.name"
                                />
                            </div>

                            <div>
                                <div
                                    class="text-base font-medium text-gray-800"
                                >
                                    {{ $page.props.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500">
                                    {{ $page.props.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <jet-responsive-nav-link
                                :href="route('profile.show')"
                                :active="route().current('profile.show')"
                            >
                                Profile
                            </jet-responsive-nav-link>

                            <jet-responsive-nav-link
                                :href="route('api-tokens.index')"
                                :active="route().current('api-tokens.index')"
                                v-if="$page.props.jetstream.hasApiFeatures"
                            >
                                API Tokens
                            </jet-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <jet-responsive-nav-link as="button">
                                    Log Out
                                </jet-responsive-nav-link>
                            </form>

                            <!-- Team Management -->
                            <template
                                v-if="$page.props.jetstream.hasTeamFeatures"
                            >
                                <div class="border-t border-gray-200"></div>

                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Manage Team
                                </div>

                                <!-- Team Settings -->
                                <jet-responsive-nav-link
                                    :href="
                                        route(
                                            'teams.show',
                                            $page.props.user.current_team
                                        )
                                    "
                                    :active="route().current('teams.show')"
                                >
                                    Team Settings
                                </jet-responsive-nav-link>

                                <jet-responsive-nav-link
                                    :href="route('teams.create')"
                                    :active="route().current('teams.create')"
                                >
                                    Create New Team
                                </jet-responsive-nav-link>

                                <div class="border-t border-gray-200"></div>

                                <!-- Team Switcher -->
                                <div
                                    class="block px-4 py-2 text-xs text-gray-400"
                                >
                                    Switch Teams
                                </div>

                                <template
                                    v-for="team in $page.props.user.all_teams"
                                    :key="team.id"
                                >
                                    <form @submit.prevent="switchToTeam(team)">
                                        <jet-responsive-nav-link as="button">
                                            <div class="flex items-center">
                                                <svg
                                                    v-if="
                                                        team.id ==
                                                        $page.props.user
                                                            .current_team_id
                                                    "
                                                    class="w-5 h-5 mr-2 text-green-400"
                                                    fill="none"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    >
                                                        <template>
                                                            <div>
                                                                <jet-banner />

                                                                <div
                                                                    class="min-h-screen bg-gray-100"
                                                                >
                                                                    <nav
                                                                        class="bg-white border-b border-gray-100"
                                                                    >
                                                                        <!-- Primary Navigation Menu -->
                                                                        <div
                                                                            class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8"
                                                                        >
                                                                            <div
                                                                                class="flex justify-between h-16"
                                                                            >
                                                                                <div
                                                                                    class="flex"
                                                                                >
                                                                                    <!-- Logo -->
                                                                                    <div
                                                                                        class="flex items-center flex-shrink-0"
                                                                                    >
                                                                                        <inertia-link
                                                                                            :href="
                                                                                                route(
                                                                                                    'dashboard'
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            <jet-application-mark
                                                                                                class="block w-auto h-9"
                                                                                            />
                                                                                        </inertia-link>
                                                                                    </div>

                                                                                    <!-- Navigation Links -->
                                                                                    <div
                                                                                        class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                                                                                    >
                                                                                        <jet-nav-link
                                                                                            :href="
                                                                                                route(
                                                                                                    'dashboard'
                                                                                                )
                                                                                            "
                                                                                            :active="
                                                                                                route().current(
                                                                                                    'dashboard'
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            Dashboard
                                                                                        </jet-nav-link>
                                                                                    </div>

                                                                                    <div
                                                                                        class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                                                                                    >
                                                                                        <jet-nav-link
                                                                                            :href="
                                                                                                route(
                                                                                                    'shelf'
                                                                                                )
                                                                                            "
                                                                                            :active="
                                                                                                route().current(
                                                                                                    'shelf'
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            My
                                                                                            Shelf
                                                                                        </jet-nav-link>
                                                                                    </div>
                                                                                    <div
                                                                                        v-if="
                                                                                            $page
                                                                                                .props
                                                                                                .user
                                                                                                .privs ===
                                                                                                1 ||
                                                                                            $page
                                                                                                .props
                                                                                                .user
                                                                                                .privs ===
                                                                                                2
                                                                                        "
                                                                                        class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex"
                                                                                    >
                                                                                        <jet-nav-link
                                                                                            :href="
                                                                                                route(
                                                                                                    'local-admin'
                                                                                                )
                                                                                            "
                                                                                            :active="
                                                                                                $page
                                                                                                    .props
                                                                                                    .currentRouteName ==
                                                                                                'shelf'
                                                                                            "
                                                                                        >
                                                                                            Admin
                                                                                            Page
                                                                                        </jet-nav-link>
                                                                                    </div>
                                                                                </div>

                                                                                <div
                                                                                    class="hidden sm:flex sm:items-center sm:ml-6"
                                                                                >
                                                                                    <div
                                                                                        class="relative ml-3"
                                                                                    >
                                                                                        <!-- Teams Dropdown -->
                                                                                        <jet-dropdown
                                                                                            align="right"
                                                                                            width="60"
                                                                                            v-if="
                                                                                                $page
                                                                                                    .props
                                                                                                    .jetstream
                                                                                                    .hasTeamFeatures
                                                                                            "
                                                                                        >
                                                                                            <template
                                                                                                #trigger
                                                                                            >
                                                                                                <span
                                                                                                    class="inline-flex rounded-md"
                                                                                                >
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-transparent leading-4 rounded-md hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition"
                                                                                                    >
                                                                                                        {{
                                                                                                            $page
                                                                                                                .props
                                                                                                                .user
                                                                                                                .current_team
                                                                                                                .name
                                                                                                        }}

                                                                                                        <svg
                                                                                                            class="ml-2 -mr-0.5 h-4 w-4"
                                                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                                                            viewBox="0 0 20 20"
                                                                                                            fill="currentColor"
                                                                                                        >
                                                                                                            <path
                                                                                                                fill-rule="evenodd"
                                                                                                                d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                                                                                                clip-rule="evenodd"
                                                                                                            />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </span>
                                                                                            </template>

                                                                                            <template
                                                                                                #content
                                                                                            >
                                                                                                <div
                                                                                                    class="w-60"
                                                                                                >
                                                                                                    <!-- Team Management -->
                                                                                                    <template
                                                                                                        v-if="
                                                                                                            $page
                                                                                                                .props
                                                                                                                .jetstream
                                                                                                                .hasTeamFeatures
                                                                                                        "
                                                                                                    >
                                                                                                        <div
                                                                                                            class="block px-4 py-2 text-xs text-gray-400"
                                                                                                        >
                                                                                                            Manage
                                                                                                            Team
                                                                                                        </div>

                                                                                                        <!-- Team Settings -->
                                                                                                        <jet-dropdown-link
                                                                                                            :href="
                                                                                                                route(
                                                                                                                    'teams.show',
                                                                                                                    $page
                                                                                                                        .props
                                                                                                                        .user
                                                                                                                        .current_team
                                                                                                                )
                                                                                                            "
                                                                                                        >
                                                                                                            Team
                                                                                                            Settings
                                                                                                        </jet-dropdown-link>

                                                                                                        <jet-dropdown-link
                                                                                                            :href="
                                                                                                                route(
                                                                                                                    'teams.create'
                                                                                                                )
                                                                                                            "
                                                                                                            v-if="
                                                                                                                $page
                                                                                                                    .props
                                                                                                                    .jetstream
                                                                                                                    .canCreateTeams
                                                                                                            "
                                                                                                        >
                                                                                                            Create
                                                                                                            New
                                                                                                            Team
                                                                                                        </jet-dropdown-link>

                                                                                                        <div
                                                                                                            class="border-t border-gray-100"
                                                                                                        ></div>

                                                                                                        <!-- Team Switcher -->
                                                                                                        <div
                                                                                                            class="block px-4 py-2 text-xs text-gray-400"
                                                                                                        >
                                                                                                            Switch
                                                                                                            Teams
                                                                                                        </div>

                                                                                                        <template
                                                                                                            v-for="team in $page
                                                                                                                .props
                                                                                                                .user
                                                                                                                .all_teams"
                                                                                                            :key="
                                                                                                                team.id
                                                                                                            "
                                                                                                        >
                                                                                                            <form
                                                                                                                @submit.prevent="
                                                                                                                    switchToTeam(
                                                                                                                        team
                                                                                                                    )
                                                                                                                "
                                                                                                            >
                                                                                                                <jet-dropdown-link
                                                                                                                    as="button"
                                                                                                                >
                                                                                                                    <div
                                                                                                                        class="flex items-center"
                                                                                                                    >
                                                                                                                        <svg
                                                                                                                            v-if="
                                                                                                                                team.id ==
                                                                                                                                $page
                                                                                                                                    .props
                                                                                                                                    .user
                                                                                                                                    .current_team_id
                                                                                                                            "
                                                                                                                            class="w-5 h-5 mr-2 text-green-400"
                                                                                                                            fill="none"
                                                                                                                            stroke-linecap="round"
                                                                                                                            stroke-linejoin="round"
                                                                                                                            stroke-width="2"
                                                                                                                            stroke="currentColor"
                                                                                                                            viewBox="0 0 24 24"
                                                                                                                        >
                                                                                                                            <path
                                                                                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                                                                            ></path>
                                                                                                                        </svg>
                                                                                                                        <div>
                                                                                                                            {{
                                                                                                                                team.name
                                                                                                                            }}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </jet-dropdown-link>
                                                                                                            </form>
                                                                                                        </template>
                                                                                                    </template>
                                                                                                </div>
                                                                                            </template>
                                                                                        </jet-dropdown>
                                                                                    </div>

                                                                                    <!-- Settings Dropdown -->
                                                                                    <div
                                                                                        class="relative ml-3"
                                                                                    >
                                                                                        <jet-dropdown
                                                                                            align="right"
                                                                                            width="48"
                                                                                        >
                                                                                            <template
                                                                                                #trigger
                                                                                            >
                                                                                                <button
                                                                                                    v-if="
                                                                                                        $page
                                                                                                            .props
                                                                                                            .jetstream
                                                                                                            .managesProfilePhotos
                                                                                                    "
                                                                                                    class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                                                                                >
                                                                                                    <img
                                                                                                        class="object-cover w-8 h-8 rounded-full"
                                                                                                        :src="
                                                                                                            $page
                                                                                                                .props
                                                                                                                .user
                                                                                                                .profile_photo_url
                                                                                                        "
                                                                                                        :alt="
                                                                                                            $page
                                                                                                                .props
                                                                                                                .user
                                                                                                                .name
                                                                                                        "
                                                                                                    />
                                                                                                </button>

                                                                                                <span
                                                                                                    v-else
                                                                                                    class="inline-flex rounded-md"
                                                                                                >
                                                                                                    <button
                                                                                                        type="button"
                                                                                                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-transparent leading-4 rounded-md hover:text-gray-700 focus:outline-none transition"
                                                                                                    >
                                                                                                        {{
                                                                                                            $page
                                                                                                                .props
                                                                                                                .user
                                                                                                                .name
                                                                                                        }}

                                                                                                        <svg
                                                                                                            class="ml-2 -mr-0.5 h-4 w-4"
                                                                                                            xmlns="http://www.w3.org/2000/svg"
                                                                                                            viewBox="0 0 20 20"
                                                                                                            fill="currentColor"
                                                                                                        >
                                                                                                            <path
                                                                                                                fill-rule="evenodd"
                                                                                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                                                                clip-rule="evenodd"
                                                                                                            />
                                                                                                        </svg>
                                                                                                    </button>
                                                                                                </span>
                                                                                            </template>

                                                                                            <template
                                                                                                #content
                                                                                            >
                                                                                                <!-- Account Management -->
                                                                                                <div
                                                                                                    class="block px-4 py-2 text-xs text-gray-400"
                                                                                                >
                                                                                                    Manage
                                                                                                    Account
                                                                                                </div>

                                                                                                <jet-dropdown-link
                                                                                                    :href="
                                                                                                        route(
                                                                                                            'profile.show'
                                                                                                        )
                                                                                                    "
                                                                                                >
                                                                                                    Profile
                                                                                                </jet-dropdown-link>

                                                                                                <jet-dropdown-link
                                                                                                    :href="
                                                                                                        route(
                                                                                                            'api-tokens.index'
                                                                                                        )
                                                                                                    "
                                                                                                    v-if="
                                                                                                        $page
                                                                                                            .props
                                                                                                            .jetstream
                                                                                                            .hasApiFeatures
                                                                                                    "
                                                                                                >
                                                                                                    API
                                                                                                    Tokens
                                                                                                </jet-dropdown-link>

                                                                                                <div
                                                                                                    class="border-t border-gray-100"
                                                                                                ></div>

                                                                                                <!-- Authentication -->
                                                                                                <form
                                                                                                    @submit.prevent="
                                                                                                        logout
                                                                                                    "
                                                                                                >
                                                                                                    <jet-dropdown-link
                                                                                                        as="button"
                                                                                                    >
                                                                                                        Log
                                                                                                        Out
                                                                                                    </jet-dropdown-link>
                                                                                                </form>
                                                                                            </template>
                                                                                        </jet-dropdown>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Hamburger -->
                                                                                <div
                                                                                    class="flex items-center -mr-2 sm:hidden"
                                                                                >
                                                                                    <button
                                                                                        @click="
                                                                                            showingNavigationDropdown =
                                                                                                !showingNavigationDropdown
                                                                                        "
                                                                                        class="inline-flex items-center justify-center p-2 text-gray-400 rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition"
                                                                                    >
                                                                                        <svg
                                                                                            class="w-6 h-6"
                                                                                            stroke="currentColor"
                                                                                            fill="none"
                                                                                            viewBox="0 0 24 24"
                                                                                        >
                                                                                            <path
                                                                                                :class="{
                                                                                                    hidden: showingNavigationDropdown,
                                                                                                    'inline-flex':
                                                                                                        !showingNavigationDropdown,
                                                                                                }"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M4 6h16M4 12h16M4 18h16"
                                                                                            />
                                                                                            <path
                                                                                                :class="{
                                                                                                    hidden: !showingNavigationDropdown,
                                                                                                    'inline-flex':
                                                                                                        showingNavigationDropdown,
                                                                                                }"
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M6 18L18 6M6 6l12 12"
                                                                                            />
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Responsive Navigation Menu -->
                                                                        <div
                                                                            :class="{
                                                                                block: showingNavigationDropdown,
                                                                                hidden: !showingNavigationDropdown,
                                                                            }"
                                                                            class="sm:hidden"
                                                                        >
                                                                            <div
                                                                                class="pt-2 pb-3 space-y-1"
                                                                            >
                                                                                <jet-responsive-nav-link
                                                                                    :href="
                                                                                        route(
                                                                                            'dashboard'
                                                                                        )
                                                                                    "
                                                                                    :active="
                                                                                        route().current(
                                                                                            'dashboard'
                                                                                        )
                                                                                    "
                                                                                >
                                                                                    Dashboard
                                                                                </jet-responsive-nav-link>
                                                                            </div>

                                                                            <!-- Responsive Settings Options -->
                                                                            <div
                                                                                class="pt-4 pb-1 border-t border-gray-200"
                                                                            >
                                                                                <div
                                                                                    class="flex items-center px-4"
                                                                                >
                                                                                    <div
                                                                                        v-if="
                                                                                            $page
                                                                                                .props
                                                                                                .jetstream
                                                                                                .managesProfilePhotos
                                                                                        "
                                                                                        class="flex-shrink-0 mr-3"
                                                                                    >
                                                                                        <img
                                                                                            class="object-cover w-10 h-10 rounded-full"
                                                                                            :src="
                                                                                                $page
                                                                                                    .props
                                                                                                    .user
                                                                                                    .profile_photo_url
                                                                                            "
                                                                                            :alt="
                                                                                                $page
                                                                                                    .props
                                                                                                    .user
                                                                                                    .name
                                                                                            "
                                                                                        />
                                                                                    </div>

                                                                                    <div>
                                                                                        <div
                                                                                            class="text-base font-medium text-gray-800"
                                                                                        >
                                                                                            {{
                                                                                                $page
                                                                                                    .props
                                                                                                    .user
                                                                                                    .name
                                                                                            }}
                                                                                        </div>
                                                                                        <div
                                                                                            class="text-sm font-medium text-gray-500"
                                                                                        >
                                                                                            {{
                                                                                                $page
                                                                                                    .props
                                                                                                    .user
                                                                                                    .email
                                                                                            }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div
                                                                                    class="mt-3 space-y-1"
                                                                                >
                                                                                    <jet-responsive-nav-link
                                                                                        :href="
                                                                                            route(
                                                                                                'profile.show'
                                                                                            )
                                                                                        "
                                                                                        :active="
                                                                                            route().current(
                                                                                                'profile.show'
                                                                                            )
                                                                                        "
                                                                                    >
                                                                                        Profile
                                                                                    </jet-responsive-nav-link>

                                                                                    <jet-responsive-nav-link
                                                                                        :href="
                                                                                            route(
                                                                                                'api-tokens.index'
                                                                                            )
                                                                                        "
                                                                                        :active="
                                                                                            route().current(
                                                                                                'api-tokens.index'
                                                                                            )
                                                                                        "
                                                                                        v-if="
                                                                                            $page
                                                                                                .props
                                                                                                .jetstream
                                                                                                .hasApiFeatures
                                                                                        "
                                                                                    >
                                                                                        API
                                                                                        Tokens
                                                                                    </jet-responsive-nav-link>

                                                                                    <!-- Authentication -->
                                                                                    <form
                                                                                        method="POST"
                                                                                        @submit.prevent="
                                                                                            logout
                                                                                        "
                                                                                    >
                                                                                        <jet-responsive-nav-link
                                                                                            as="button"
                                                                                        >
                                                                                            Log
                                                                                            Out
                                                                                        </jet-responsive-nav-link>
                                                                                    </form>

                                                                                    <!-- Team Management -->
                                                                                    <template
                                                                                        v-if="
                                                                                            $page
                                                                                                .props
                                                                                                .jetstream
                                                                                                .hasTeamFeatures
                                                                                        "
                                                                                    >
                                                                                        <div
                                                                                            class="border-t border-gray-200"
                                                                                        ></div>

                                                                                        <div
                                                                                            class="block px-4 py-2 text-xs text-gray-400"
                                                                                        >
                                                                                            Manage
                                                                                            Team
                                                                                        </div>

                                                                                        <!-- Team Settings -->
                                                                                        <jet-responsive-nav-link
                                                                                            :href="
                                                                                                route(
                                                                                                    'teams.show',
                                                                                                    $page
                                                                                                        .props
                                                                                                        .user
                                                                                                        .current_team
                                                                                                )
                                                                                            "
                                                                                            :active="
                                                                                                route().current(
                                                                                                    'teams.show'
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            Team
                                                                                            Settings
                                                                                        </jet-responsive-nav-link>

                                                                                        <jet-responsive-nav-link
                                                                                            :href="
                                                                                                route(
                                                                                                    'teams.create'
                                                                                                )
                                                                                            "
                                                                                            :active="
                                                                                                route().current(
                                                                                                    'teams.create'
                                                                                                )
                                                                                            "
                                                                                        >
                                                                                            Create
                                                                                            New
                                                                                            Team
                                                                                        </jet-responsive-nav-link>

                                                                                        <div
                                                                                            class="border-t border-gray-200"
                                                                                        ></div>

                                                                                        <!-- Team Switcher -->
                                                                                        <div
                                                                                            class="block px-4 py-2 text-xs text-gray-400"
                                                                                        >
                                                                                            Switch
                                                                                            Teams
                                                                                        </div>

                                                                                        <template
                                                                                            v-for="team in $page
                                                                                                .props
                                                                                                .user
                                                                                                .all_teams"
                                                                                            :key="
                                                                                                team.id
                                                                                            "
                                                                                        >
                                                                                            <form
                                                                                                @submit.prevent="
                                                                                                    switchToTeam(
                                                                                                        team
                                                                                                    )
                                                                                                "
                                                                                            >
                                                                                                <jet-responsive-nav-link
                                                                                                    as="button"
                                                                                                >
                                                                                                    <div
                                                                                                        class="flex items-center"
                                                                                                    >
                                                                                                        <svg
                                                                                                            v-if="
                                                                                                                team.id ==
                                                                                                                $page
                                                                                                                    .props
                                                                                                                    .user
                                                                                                                    .current_team_id
                                                                                                            "
                                                                                                            class="w-5 h-5 mr-2 text-green-400"
                                                                                                            fill="none"
                                                                                                            stroke-linecap="round"
                                                                                                            stroke-linejoin="round"
                                                                                                            stroke-width="2"
                                                                                                            stroke="currentColor"
                                                                                                            viewBox="0 0 24 24"
                                                                                                        >
                                                                                                            <path
                                                                                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                                                            ></path>
                                                                                                        </svg>
                                                                                                        <div>
                                                                                                            {{
                                                                                                                team.name
                                                                                                            }}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </jet-responsive-nav-link>
                                                                                            </form>
                                                                                        </template>
                                                                                    </template>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </nav>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </path>
                                                </svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </jet-responsive-nav-link>
                                    </form>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</template>
<script>
import JetNavLink from "@/Jetstream/NavLink";
import JetApplicationMark from "@/Jetstream/ApplicationMark";
import JetBanner from "@/Jetstream/Banner";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import JetResponsiveNavLink from "@/Jetstream/ResponsiveNavLink";

export default {
    components: {
        JetNavLink,
        JetDropdownLink,
        JetResponsiveNavLink,
        JetBanner,
        JetApplicationMark,
    },
    computed: {
        sortSchemeId() {
            return this.$page.props.user.scheme_id;
        },
    },

    methods: {
        logout() {
            this.$inertia.post(route("logout"));
        },
        emptyTables() {
            this.$inertia.post(route("empty_tables"));
        },
        masterShelf() {
            this.$inertia.get(
                route("master.shelf", { sortSchemeId: this.sortSchemeId })
            );
        },
    },
};
</script>
