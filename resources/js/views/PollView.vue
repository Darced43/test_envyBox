<template>
  <div class="container">
    <div v-if="store.state.loading" class="loading">Загрузка...</div>
    
    <div v-else-if="store.state.error" class="error-box">
      <p class="error">{{ store.state.error }}</p>
      <button @click="loadPoll">Повторить</button>
    </div>

    <div v-else-if="store.state.poll" class="poll-card">
      <h1>{{ store.state.poll.title }}</h1>

      <!-- РЕЗУЛЬТАТЫ + ССЫЛКА -->
      <div v-if="store.getters.hasVoted || store.state.results" class="results">
        <h2>Результаты</h2>
        <ul class="results-list">
          <li v-for="r in store.state.results" :key="r.id" class="result-item">
            <span class="option-text">{{ r.text }}</span>
            <span class="vote-count">{{ r.votes }} голос(ов)</span>
          </li>
        </ul>
        
        <div class="share-box">
          <p>🎉 Спасибо за ваш голос! Поделитесь опросом с друзьями:</p>
          <div class="share-input-group">
            <!-- ✅ Теперь value берётся из реактивной переменной -->
            <input type="text" :value="currentUrl" readonly />
            <button @click="copyLink" class="btm btm__create">📋 Копировать</button>
          </div>
        </div>
      </div>

      <!-- ФОРМА ГОЛОСОВАНИЯ -->
      <form v-else @submit.prevent="submit" class="vote-form">
        <label v-for="opt in store.state.poll.options" :key="opt.id" class="option-label">
          <input type="radio" v-model="selected" :value="opt.id" required />
          <span>{{ opt.text }}</span>
        </label>
        
        <button type="submit" :disabled="!selected || store.state.loading" class="btm__create">
          {{ store.state.loading ? 'Отправка...' : 'Проголосовать' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRoute } from 'vue-router';

const store = useStore();
const route = useRoute();
const selected = ref(null);
const currentUrl = ref('');

onMounted(() => {
  currentUrl.value = window.location.href;
  store.dispatch('fetchPoll', route.params.code);
});

const submit = () => {
  if (!selected.value) return;
  store.dispatch('submitVote', selected.value);
};

// ✅ 3. Функция копирования
const copyLink = async () => {
  if (!currentUrl.value) return;
  try {
    await navigator.clipboard.writeText(currentUrl.value);
    alert('✅ Ссылка скопирована в буфер обмена!');
  } catch {
    // Fallback для старых браузеров
    const input = document.querySelector('.share-input-group input');
    input?.select();
    document.execCommand('copy');
    alert('✅ Ссылка скопирована!');
  }
};
</script>

<style scoped>
.container { 
  max-width: 600px; 
  margin: 0 auto; 
  padding: 2rem; 
  font-family: system-ui, sans-serif; 
}
.loading, .error-box {
  text-align: center; 
  padding: 2rem; 
}
.error { 
  color: #e53e3e; 
  background: #fff5f5; 
  padding: 1rem; 
  border-radius: 8px; 
}
.results-list {
   list-style: none; 
  padding: 0; 
}
.result-item { 
  display: flex; 
  justify-content: space-between; 
  padding: 0.75rem 0; 
  border-bottom: 1px solid #eee; 
}
.vote-form { 
  display: flex; 
  flex-direction: column; 
  gap: 1rem; 
  margin-top: 1.5rem; 
}
.option-label { 
  display: flex; 
  align-items: center; 
  gap: 0.5rem; 
  cursor: pointer; 
  padding: 0.5rem; 
  border-radius: 6px; transition: background 0.2s; 
}
.option-label:hover { 
  background: #f7fafc; 
}
.btm__create { 
  align-self: flex-start; 
  background: #42b983; 
  color: white; 
  border: none; 
  padding: 12px 24px; 
  border-radius: 6px; 
  cursor: pointer; 
  font-weight: 500; 
  margin-top: 1rem; 
}
.btm__create:disabled { 
  opacity: 0.6; 
  cursor: not-allowed; 
}
.hint { 
  margin-top: 1.5rem; 
  color: #718096; 
  font-size: 0.9rem; 
}
</style>