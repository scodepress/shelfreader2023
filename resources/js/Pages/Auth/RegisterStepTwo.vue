<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import JetButton from "@/Jetstream/Button.vue";
import JetInput from "@/Jetstream/Input.vue";
import JetCheckbox from "@/Jetstream/Checkbox.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";

const props = defineProps({
    user: Object,
    libraries: Object,
    sortSchemes: Object,
});

const form = useForm({
    libraryId: "",
    lcc: "",
    maps: "",
});

const submit = () => {
    form.post(route("register.step2"));
};
</script>

<template>
    <Head title="Register Step Two" />

    <JetAuthenticationCard>
        <template #logo>
                            <div class="flex-shrink-0">
			    	<img class="mx-auto" width="300" src="/assets/images/logo2.png" />
				<div class="flex justify-center mt-2 ml-2 text-2xl font-bold text-navy-800">
				We need a little more information...
				</div>

                            </div>
        </template>

        <JetValidationErrors class="mb-4" />

    <div class="flex justify-center mt-6" v-if="errors">
        
        <div v-for="error in errors">
            <span class="text-xl text-red-700">{{ error }}</span>
        </div>
    </div>
    <div class="flex justify-center mt-6" v-if="status != 'Available'">
        <span class="text-xl text-red-700">{{ statusAlert }}</span>
        
    </div>
    <div class="flex justify-center mt-6" v-if="$page.props.flash.message">
        
        <span class="text-xl text-red-700">{{
            $page.props.flash.message
        }}</span>
    </div>
        <form class="" @submit.prevent="submit">
            <div class="w-full mt-4">
                <JetLabel class="mb-2 text-2xl" for="Library" value="Choose your library" />
                <select v-model="form.libraryId" class="w-full rounded-md">
                    <option value="" disabled>Libraries:</option>
                    <option :value="library.id" v-for="(library, index) in libraries">
                        {{ library.library_name }}
                    </option>
                </select>
            </div>

            <div class="w-full mt-4">
                <JetLabel
		    class="mb-2 text-2xl"
                    for="Sort Scheme"
                    value="Choose the sorting method(s) &nbsp; &nbsp; &nbsp; &nbsp; you will use:"
                />
                <div>
                    <JetLabel class="mr-6 text-xl">LCC</JetLabel
                    ><JetInput class="text-2xl" type="checkbox" :value="lcc" v-model="form.lcc"></JetInput>
                </div>
                <div>
                    <JetLabel class="mr-6 text-xl">Maps</JetLabel
                    ><JetInput class="text-2xl" type="checkbox" :value="maps" v-model="form.maps"></JetInput>
                </div>
            </div>

            <div class="">
                <JetButton
                    class="justify-center w-full mt-4 text-xl"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Complete Registration
                </JetButton>
            </div>
        </form>
    </JetAuthenticationCard>
</template>
