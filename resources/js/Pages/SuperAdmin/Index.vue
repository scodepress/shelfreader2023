<template>
    <div><layout></layout></div>
    <div style="margin: 25px">
        <div class="mt-5 mb-2 font-bold">Users</div>
        <div class="min-w-full overflow-auto h-100">
            <table class="w-full">
                <tr style="font-weight:bold" >
                    <td>Name</td>
                    <td>Role</td>
                    <td>Email</td>
                    <td>Library</td>
                </tr>
                <tr v-for="(user, index) in allUsers">
                    <td>{{ user.name }}</td>
                    <td>{{ user.privs }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.library_name }}</td>
                </tr>
            </table>
        </div>

        <div>
            <div class="mt-5 text-lg font-bold">Impersonate a User:</div>
            <form @submit.prevent="impersonate">
                <select v-model="form.user_id">
                    <option
                        v-for="(ausers, index) in allUsers"
                        :value="ausers.id"
                        :key="index"
                    >
                        {{ ausers.name }}
                    </option>
                </select>
                &nbsp;
		

                    <button
                        type="submit"
                        class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent sha dow-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
		    Impersonate
                    </button>
            </form>
        </div>
        
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
import { Link } from '@inertiajs/inertia-vue3'
export default {
    components: {
        Layout,
    },
    data() {
        return {
            form: this.$inertia.form({
                user_id: null,
            }),
            form: this.$inertia.form({
                institution_id: null,
            }),
            form: this.$inertia.form({
                new_institution_id: null,
                tenant: null,
                password: null,
            }),
        };
    },

    props: {
        users: Object,
        institutions: Object,
        allUsers: Object,
        owner: Object,
        unapprovedRequests: Object,
        userActionId: Number,
        newInstitutionId: Boolean,
        returned_institution_id: null,
        folioInstitutions: Object,
        impersonatedUserId: Number,
        aUser: Number,
    },

    methods: {
        impersonate() {
            this.$inertia.post("/users.impersonate", this.form);
        },

        renewFolioToken() {
            this.$inertia.post("/update-auth-token", {
                institution_id: this.form.institution_id,
                userActionId: this.userActionId,
            });
        },

        createFolioToken() {
            this.$inertia.post("/create-token", {
                institution_id: this.form.new_institution_id,
                tenant: this.form.tenant,
                password: this.form.password,
            });
        },
    },
};
</script>
