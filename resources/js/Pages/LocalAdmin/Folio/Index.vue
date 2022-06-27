<template>
    <div><layout></layout></div>

    <div style="margin: 50px">
        <div
            style="
                margin-left: 25%;
                margin-right: 25%;
                margin-top: 50px;
                font-size: 16px;
            "
        >
            <div v-if="showLink != 0">

            <form @submit.prevent="stopImpersonating">
               

                <button type="submit">Stop Impersonating</button>
            </form>
	    </div>
            <div class="mt-10 text-xl font-bold">Create an Access Token</div>
            <div>
                Generate your Folio authentication token. ShelfReader does not
                save your Folio user name or password. They are used only to
                generate an authentication token for the read-only user account
                you have created.
            </div>
            <br />

            <form @submit.prevent="postFolioTokenRequest">
                <div class="max-w-md grid grid-cols-1 gap-6">
                    <label class="block">
                        <span class="text-gray-700">Folio User Name</span>
                        <input
                            type="tel"
                            multiple
                            class="block w-full mt-1 form-input"
                            v-model="form.username"
                        />
                    </label>
                    <label class="block">
                        <span class="text-gray-700">Folio Tenant Name</span>
                        <input
                            type="tel"
                            multiple
                            class="block w-full mt-1 form-input"
                            v-model="form.tenant"
                        />
                    </label>
                    <label class="block">
                        <span class="text-gray-700">Folio Password</span>
                        <input
                            type="password"
                            multiple
                            class="block w-full mt-1 form-input"
                            v-model="form.password"
                        />
                    </label>
                </div>
                <br />

                <button
                    type="submit"
                    class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent shadow-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Create Token
                </button>
            </form>
            <br />

            <div>Your Current token:</div>
            <label class="block">
                <textarea
                    v-if="keyVisibility && token"
                    class="block w-full h-24 mt-1 form-textarea"
                    rows="3"
                    >{{ token }}</textarea
                ></label
            ><br />
            <button
                v-on:click="keyVisibility = !keyVisibility"
                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border ui button toggle rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Show/Hide Your Current Token
            </button>
        </div>
        <div
            style="
                margin-left: 25%;
                margin-right: 25%;
                margin-top: 50px;
                font-size: 16px;
            "
        >
            <div class="text-xl font-bold">Add an Api Service</div>
            <api-services-form
                :availableServices="availableServices"
            ></api-services-form>

            <div class="mb-5 text-xl font-bold mt-7">
                Approve User Candidates
            </div>

            <form @submit.prevent="approveUsers">
                <div class="max-w-2xl grid grid-cols-1 gap-2">
                    <div
                        class="form-group form-check"
                        v-for="(user, index) in userCandidates"
                        :key="index"
                    >
                        <label class="inline-flex items-center">
                            <input
                                class="form-checkbox"
                                type="checkbox"
                                v-model="form.checkedNames"
                                :value="user.email"
                            />
                            <div class="ml-2">
                                {{ user.name }} {{ user.email }}
                                <span
                                    v-for="(library, index) in libraries"
                                    :key="index"
                                >
                                    <span v-if="library.id === user.library_id"
                                        >for {{ library.library_name }}</span
                                    >
                                </span>
                            </div>
                        </label>
                        <div
                            style="color: red; font-size: 20px"
                            v-if="errors.checkedNames"
                        >
                            {{ errors.checkedNames }}
                        </div>
                    </div>
                    <div v-if="!userCandidates[0]">
                        There are no users to approve.
                    </div>

                    <button
                        class="justify-center max-w-md px-4 py-2 text-sm font-medium text-white bg-indigo-600 border nline-flex ui button toggle rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
import ApiServicesForm from "./ManageApiServicesForm";
import { Link } from '@inertiajs/inertia-vue3';
export default {
    components: {
        Layout,
        ApiServicesForm,
    },

    props: {
        userName: String,
        token: String,
        availableServices: String,
        userCandidates: String,
        libraries: String,
        errors: Object,
        showLink: Number,
    },

    data() {
        return {
            keyVisibility: true,
            form: this.$inertia.form({
                username: null,
                tenant: null,
                password: null,
            }),
            form: this.$inertia.form({
                checkedNames: [],
            }),
        };
    },
    mounted() {},
    methods: {
        stopImpersonating() {
            this.$inertia.get("/leave-impersonate");
        },
        postFolioTokenRequest() {
            this.$inertia.post("/store-token", {
                username: this.form.username,
                tenant: this.form.tenant,
                password: this.form.password,
            });
        },
        approveUsers() {
            this.$inertia.post("/approve-users", {
                checkedNames: this.form.checkedNames,
                preserveScroll: true,
            });
        },
        visibility() {
            if (this.keyVisibilty === 0) {
                this.keyVisibilty = 1;
            } else {
                this.keyVisibilty = 0;
            }
        },
        toggle() {
            if (!this.keyVisibilty) {
                this.keyVisibilty = true;
            } else {
                this.keyVisibilty = false;
            }
        },
    },
};
</script>
