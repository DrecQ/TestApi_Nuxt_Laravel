<script lang="ts" setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

// Références pour lier les champs du formulaire
const email = ref('')
const password = ref('')
const router = useRouter()

// Fonction de connexion
const handleLogin = async () => {
  try {
    // Important : envoie les credentials (cookie sanctum)
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
      credentials: 'include'
    })

    // Envoie les infos d'auth
    const response = await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: {
        email: email.value,
        password: password.value
      },
      credentials: 'include' // Nécessaire pour Sanctum
    })

    // Redirige ou gère la réponse
    console.log(response)
    router.push('/dashboard') // à adapter selon ta route
  } catch (error: any) {
    console.error('Erreur de connexion', error.data)
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0f172a] py-10">
    <form
      @submit.prevent="handleLogin"
      class="w-full max-w-sm bg-[#1e293b] mt-12 p-6 rounded-xl shadow-xl"
    >
      <div class="mb-5">
        <label for="email" class="block mb-2 text-sm font-medium text-white">Email</label>
        <input
          v-model="email"
          type="email"
          id="email"
          placeholder="eba@gmail.com"
          required
          class="bg-[#334155] border border-gray-500 text-white text-sm rounded-lg block w-full p-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div class="mb-2">
        <label for="password" class="block mb-2 text-sm font-medium text-white">Mot de passe</label>
        <input
          v-model="password"
          type="password"
          id="password"
          required
          class="bg-[#334155] border border-gray-500 text-white text-sm rounded-lg block w-full p-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
      </div>

      <div class="mb-5 text-right">
        <NuxtLink to="/passwordForget" class="text-sm text-blue-400 hover:underline">
          Mot de passe oublié ?
        </NuxtLink>
      </div>

      <div class="flex items-start mb-5">
        <input
          id="remember"
          type="checkbox"
          class="w-4 h-4 text-blue-500 bg-gray-700 border-gray-600 rounded focus:ring-blue-400"
        />
        <label for="remember" class="ms-2 text-sm font-medium text-white">Se souvenir de moi</label>
      </div>

      <button
        type="submit"
        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-500 font-semibold rounded-lg text-sm px-5 py-2.5 text-center"
      >
        Connexion
      </button>
    </form>
  </div>
</template>
