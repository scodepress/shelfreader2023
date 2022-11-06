<template>
    <div><layout></layout></div>
    <div style="margin: 25px">
        

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
        };
    },

    props: {
        impersonatedUserId: Number,
        allUsers: Object,
        impersonatedUserId: Number,
    },

    methods: {
        impersonate() {
            this.$inertia.post("/users.impersonate", this.form);
        },

    },
};
</script>
