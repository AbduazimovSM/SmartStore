<template>
    <div class="card">
        <h2>{{ name }}</h2>
        <p>Цена: {{ price }}$</p>

        <button v-on:click = "favoriteApp">
          <span v-if="favorite">❤️ В Избранное</span>
          <span v-else >🤍 В Избранное</span>
        </button>

        <button v-on:click="trashApp">
          <span v-if="trash">✅ В корзине</span>
          <span v-else>🛒 Добавить в корзину</span>
        </button>
      </div>
</template>

<script setup>

import {ref} from 'vue'

defineProps({
  name: String,
  price: Number
})

const favorite = ref(false)
const trash = ref(false)

function favoriteApp(){
  favorite.value = !favorite.value
}

function trashApp(){
  trash.value = !trash.value
}

</script>

<style scoped>
  .card{
    width:300px;
    border:1px solid black;
    border-radius: 10px;
    padding:10px;
    margin-bottom: 20px;

    display:flex;
    flex-direction: column;
    gap:10px;
  }

  button{
    width:100%;
    cursor: pointer;
  }

  p{
    margin:0;
  }

  h2{
    text-align: center;
    margin:0;
  }
</style>