<script lang="ts" setup>
import { ref } from 'vue' 
import { useRouter } from 'vue-router' 

const email = ref('')
const password = ref('')
const router = useRouter()

const loading = ref(false)
const serverErrors = ref<string[]>([])
const showEmailVerificationMessage = ref(false)

const handleLogin = async () => {
  serverErrors.value = []
  showEmailVerificationMessage.value = false 
  loading.value = true

  try {
   
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
      credentials: 'include'
    })

  
    const response: any = await $fetch('http://localhost:8000/api/login', {
      method: 'POST',
      body: { email: email.value, password: password.value },
      credentials: 'include'
    })

    
    if (response && response.status === true && response.user && response.user.role) {
      const userRole = response.user.role; 

      // Redirection basée sur le rôle
      if (userRole === 'admin') {
        router.push('/dashboard/dashboardAdmin'); 
      } else if (userRole === 'user') {
        router.push('/dashboard/dashboardUser');
      } else {
        // Gérer les rôles inconnus ou par défaut
        router.push('/');
      }
    } else {
      
      serverErrors.value.push('Une erreur inattendue est survenue lors de la connexion.');
    }

  } catch (error: any) {
    console.error('Erreur de connexion:', error);

    if (error.response && error.response._data) {
      
      if (error.response.status === 403 && error.response._data.message === 'Votre email n\'est pas vérifié. Veuillez vérifier votre boîte de réception.') {
        showEmailVerificationMessage.value = true;
        router.push('/auth/email-verification-error');
      }
     
      else if (error.response.status === 422 && error.response._data.errors) {
        
        serverErrors.value.push(error.response._data.errors);
      }
   
      else if (error.response._data.message) {
        serverErrors.value.push(error.response._data.message);
      }
    } else {
      
      serverErrors.value.push('Une erreur inattendue est survenue. Veuillez réessayer.');
    }
  } finally {
    loading.value = false;
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

      <div v-if="showEmailVerificationMessage" class="bg-yellow-500 bg-opacity-20 border border-yellow-400 text-yellow-300 px-4 py-3 rounded-md mb-4">
        <p class="text-sm">
          Votre compte n'est pas encore vérifié. Veuillez valider votre inscription en cliquant sur le lien envoyé à votre adresse email.
        </p>
      </div>

      <div v-if="serverErrors.length" class="bg-red-500 bg-opacity-20 border border-red-400 text-red-300 px-4 py-3 rounded-md mb-4">
        <p v-for="(error, index) in serverErrors" :key="index" class="text-sm">
          {{ error }}
        </p>
      </div>

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
        :disabled="loading"
        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
        :class="{ 'opacity-50 cursor-not-allowed': loading }"
      >
        {{ loading ? 'Connexion en cours...' : 'Se connecter' }}
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
