<script lang="ts" setup>
const lastname = ref('')
const firstname = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const acceptTerms = ref(false)

const handleRegister = async () => {
  if (password.value !== passwordConfirmation.value) {
    alert('Les mots de passe ne correspondent pas')
    return
  }

  try {
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
      credentials: 'include'
    })

    await $fetch('http://localhost:8000/api/register', {
      method: 'POST',
      body: {
        lastname: lastname.value,
        firstname: firstname.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
      },
      credentials: 'include'
    })

    navigateTo('/auth/login')
  } catch (error) {
    console.error('Registration error', error)
    alert('Une erreur est survenue. Vérifiez vos informations.')
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 p-4">
    <form
      @submit.prevent="handleRegister"
      class="w-full max-w-md bg-slate-800 p-8 rounded-xl shadow-lg"
    >
      <h2 class="text-2xl font-bold text-white mb-6 text-center">Inscription</h2>

      <div class="space-y-4">
        <!-- Nom et Prénom sur la même ligne -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Nom</label>
            <input
              v-model="lastname"
              type="text"
              required
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Prénom</label>
            <input
              v-model="firstname"
              type="text"
              required
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>

        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

        <!-- Mot de passe et Confirmation -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Mot de passe</label>
            <input
              v-model="password"
              type="password"
              required
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Confirmation</label>
            <input
              v-model="passwordConfirmation"
              type="password"
              required
              class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
        </div>

        <!-- Conditions -->
        <div class="flex items-center pt-2">
          <input
            id="terms"
            v-model="acceptTerms"
            type="checkbox"
            required
            class="w-4 h-4 text-blue-500 bg-slate-700 border-slate-600 rounded focus:ring-blue-500"
          />
          <label for="terms" class="ml-2 text-sm text-slate-300">
            J'accepte les <a href="#" class="text-blue-400 hover:underline">conditions</a>
          </label>
        </div>

        <!-- Bouton -->
        <button
          type="submit"
          class="w-full mt-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
        >
          S'inscrire
        </button>
      </div>

      <div class="mt-4 text-center text-sm text-slate-400">
        Déjà un compte ? 
        <NuxtLink to="/auth/login" class="text-blue-400 hover:underline ml-1">
          Se connecter
        </NuxtLink>
      </div>
    </form>
  </div>
</template>