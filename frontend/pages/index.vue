<script lang="ts" setup>
const email = ref('')
const password = ref('')
const router = useRouter()

const handleLogin = async () => {
  try {
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
      credentials: 'include'
    })

    await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: { email: email.value, password: password.value },
      credentials: 'include'
    })

    router.push('/dashboard')
  } catch (error) {
    console.error('Login error', error)
  }
}
</script>

<template>
  <div class="min-h-screen grid place-items-center bg-slate-900 p-4">
    <form 
      @submit.prevent="handleLogin"
      class="w-full max-w-md space-y-6 bg-slate-800 p-8 rounded-xl"
    >
      <h1 class="text-2xl font-bold text-center text-white">Connexion</h1>
      
      <div class="space-y-2">
        <label class="block text-sm font-medium text-slate-300">Email</label>
        <input
          v-model="email"
          type="email"
          required
          placeholder="votre@email.com"
          class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div class="space-y-2">
        <label class="block text-sm font-medium text-slate-300">Mot de passe</label>
        <input
          v-model="password"
          type="password"
          required
          class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input
            id="remember"
            type="checkbox"
            class="w-4 h-4 text-blue-500 bg-slate-700 border-slate-600 rounded focus:ring-blue-500"
          />
          <label for="remember" class="ml-2 text-sm text-slate-300">Se souvenir de moi</label>
        </div>
        
        <NuxtLink 
          to="/passwordForget" 
          class="text-sm text-blue-400 hover:underline"
        >
          Mot de passe oublié ?
        </NuxtLink>
      </div>

      <button
        type="submit"
        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
      >
        Se connecter
      </button>

      <div class="text-center text-sm text-slate-400">
        Pas de compte ? 
        <NuxtLink 
          to="/auth/register" 
          class="text-blue-400 hover:underline ml-1"
        >
          S'inscrire
        </NuxtLink>
      </div>
    </form>
  </div>
</template>