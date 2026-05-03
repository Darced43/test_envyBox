// resources/js/store/index.js
import { createStore } from 'vuex';
import api from '../api';

export default createStore({
    state: {
        poll: null,           
        results: null,        
        loading: false,
        error: null
    },
    getters: {
        hasVoted: state => state.poll?.has_voted || state.results !== null
    },
    actions: {
        async fetchPoll({ commit }, code) {
            commit('setLoading', true);
            commit('setError', null);
            try {
                const { data } = await api.get(`/polls/${code}`);
                commit('setPoll', data);
                
                // Если уже голосовал — сразу подгружаем результаты
                if (data.has_voted) {
                    const res = await api.post(`/polls/${code}/vote-check`, {}); // или GET /results
                    commit('setResults', res.data.results);
                } else {
                    commit('setResults', null);
                }
            } catch (e) {
                commit('setError', e.response?.data?.message || 'Ошибка загрузки опроса');
            } finally {
                commit('setLoading', false);
            }
        },

        async createPoll({ commit }, payload) {
            try {
                const response = await api.post('/polls', payload);
                console.log('📦 Полный ответ от /api/polls:', response.data);
                
                // Laravel в dev-режиме может вернуть debug-объект с extra полями
                const shortCode = response.data.short_code;
                
                if (!shortCode) {
                throw new Error(`Бэкенд вернул данные без short_code: ${JSON.stringify(response.data)}`);
                }
                return shortCode;
            } catch (error) {
                console.error('🔥 Ошибка в Vuex createPoll:', error.response?.data || error.message);
                throw error; // Пробрасываем ошибку в компонент
            }
        },

        async submitVote({ commit, state }, optionId) {
            commit('setLoading', true);
            commit('setError', null);
            try {
                const { data } = await api.post(`/polls/${state.poll.short_code}/vote`, { option_id: optionId });
                commit('setResults', data.results);
                commit('setPollHasVoted', true); // Помечаем, что голосование завершено
            } catch (e) {
                if (e.response?.status === 409) {
                    // Уже голосовал: скрываем форму, загружаем результаты
                    commit('setPollHasVoted', true);
                    const res = await api.post(`/polls/${state.poll.short_code}/vote-check`, {});
                    commit('setResults', res.data.results);
                } else {
                    commit('setError', e.response?.data?.message || 'Ошибка голосования');
                }
            } finally {
                commit('setLoading', false);
            }
        }
    },
    mutations: {
        setPoll(state, poll) { state.poll = poll; },
        setResults(state, results) { state.results = results; },
        setPollHasVoted(state, val) { 
            if (state.poll) state.poll.has_voted = val; 
        },
        setLoading(state, val) { state.loading = val; },
        setError(state, msg) { state.error = msg; }
    }
});