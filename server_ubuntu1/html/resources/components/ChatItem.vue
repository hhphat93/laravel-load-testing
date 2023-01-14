<template>
	<div class="message">
		<div class="message-item user-name">
			MTD
		</div>
		<div class="message-item timestamp">
			| 11:21:32: 
		</div>
		<div class="message-item text-message">
			Hello how are you
		</div>
	</div>
</template>

<script>
	export default {
	}
</script>

<style lang="scss" scoped>
	.message {
		display: flex;
		color: #00b8ff;
		.message-item:not(:last-child) {
			margin-right: 5px;
		}
	}
	.message:not(:last-child) {
		padding-bottom: 20px;
	}
	.is-current-user {
		color: #a900ff;
	}
</style>