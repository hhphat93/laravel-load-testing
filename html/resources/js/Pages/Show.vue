<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({ user: Object });

import { ref } from "vue";

// Khai báo biến state với ref
const count = ref(0);
const ajax = ref(0);

// Hàm xử lý sự kiện click
const increment = () => {
  count.value++;
};

const reset = () => {
  count.value = 0;
};

setTimeout(() => {
    ajax.value = 1
}, 2000);

</script>

<template>
  <div>
    <Head title="Welcome" />
    <h1>Welcome</h1>
    <p>Hello {{ user.name }}, welcome to your first Inertia app!</p>

    <h1>{{ count }}</h1>
    <button @click="increment">Tăng Giá Trị</button>
    <button v-show="ajax" @click="reset">Đặt Lại</button>
  </div>
</template>

<style>
button {
  margin: 5px;
  padding: 8px 12px;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #f0f0f0;
}
</style>
