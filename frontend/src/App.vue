<template>
  <ProductCard v-for="product in products" :key="product.id" :name="product.name" :price="product.price"/>

</template>

<script setup>
  import ProductCard from './components/ProductCard.vue';

  const products = [
    {
      name: "Iphone 15",
      price:1500
    },
    {
      name: "Iphone 16",
      price:1600
    },
    {
      name: "Iphone 17",
      price:1700
    }
  ]


</script>

<style scoped>

</style>