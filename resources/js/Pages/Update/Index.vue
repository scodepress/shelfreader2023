<template>
<layout />
    <div class="mt-32 ml-32">{{ message }}</div>
</template>

<script setup>
import Layout from '@/Layouts/Layout';
const props = defineProps ({
		'message': String,
	})
</script>
