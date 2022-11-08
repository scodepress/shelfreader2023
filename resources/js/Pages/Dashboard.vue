<template>
    <Layout></Layout>
    <div
        class="flex justify-center mt-24 mb-4 text-3xl font-bold text-blue-900"
    >
        <span class="">Welcome to ShelfReader {{ name }}! So far you have:</span>
    </div>

    <div class="flex justify-center text-2xl text-blue-900">
        <ul class="list-disc marker:text-grey-900">
            <li>Scanned a total of {{ total }} items.</li>
            <li>
                Identified {{ alertCount }} items with incorrect location
                status.
            </li>
            <li>Corrected the location of {{ corrections }} items.</li>
        </ul>
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";

export default {
    components: {
        Layout,
    },
    props: {
        name: String,
        mapsCount: Number,
        lccCount: Number,
        alertCount: Number,
        total: Number,
        corrections: Number,
    },
};
</script>
