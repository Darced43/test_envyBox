<template>
  <div class="container">
    <h1>Создать опрос</h1>
    <form @submit.prevent="create">
      <input v-model="title" placeholder="Название (5-100)" required />
      <div v-for="(opt, i) in options" :key="i">
        <input v-model="options[i]" :placeholder="`Вариант ${i+1}`" required />
        <button type="button" @click="removeOption(i)" v-if="options.length > 2">❌</button>
      </div>
      <button class="btm btm__option" type="button" @click="addOption" :disabled="options.length >= 4">+ Вариант</button>
      <button class="btm btm__create" type="submit">Создать</button>
    </form>
    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<style scoped lang="scss">
.btm{
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
    margin-right: 10px;
    color: #2f3c36;   
    &__create{
        background: #42b983;
    }
    &__option{
        background: #f0f0f0;
    }
}
</style>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

const store = useStore();
const router = useRouter();
const title = ref('');
const options = ref(['', '']);
const error = ref(null);

const addOption = () => { if (options.value.length < 4) options.value.push(''); };
const removeOption = (i) => { options.value.splice(i, 1); };

const create = async () => {
  error.value = null;
  if (title.value.trim().length < 5) return error.value = 'Минимум 5 символов';
  if (options.value.some(o => !o.trim())) return error.value = 'Заполните все варианты';

  try {
    const code = await store.dispatch('createPoll', { 
      title: title.value, 
      options: options.value.map(o => o.trim()) 
    });
    
    console.log('✅ Бэкенд вернул код:', code);
    if (!code) throw new Error('Пустой код от сервера');
    
    router.push(`/poll/${code}`);
  } catch (e) {
    console.error('❌ Ошибка создания:', e);
    error.value = e.response?.data?.message || e.message || 'Не удалось создать опрос';
  }
};
</script>