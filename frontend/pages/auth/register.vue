<script lang="ts" setup>

const lastname = ref('')
const firstname = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const acceptTerms = ref(false) 

// Ajout d'un état de chargement pour le bouton
const loading = ref(false)
// Ajout d'un ref pour les messages d'erreur du serveur
const serverErrors = ref<string[]>([])

// Utilisation de useRouter pour la navigation, bien que navigateTo soit aussi une option
const router = useRouter()

const handleRegister = async () => {
  // Réinitialiser les erreurs précédentes
  serverErrors.value = []

  // Validation côté client pour la confirmation du mot de passe
  if (password.value !== passwordConfirmation.value) {
    serverErrors.value.push('Les mots de passe ne correspondent pas.')
    return
  }

  // Validation côté client pour les termes et conditions
  if (!acceptTerms.value) {
    serverErrors.value.push('Vous devez accepter les conditions pour vous inscrire.')
    return
  }

  loading.value = true // Activer l'état de chargement

  try {
    // 1. Obtenir le cookie CSRF de Sanctum
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
      credentials: 'include' // Important pour inclure les cookies
    })

    // 2. Envoyer la requête d'enregistrement à l'API
    await $fetch('http://localhost:8000/api/register', {
      method: 'POST',
      body: {
        // Laravel s'attend souvent à un champ 'name' par défaut
        name: `${firstname.value} ${lastname.value}`,
        lastname: lastname.value,
        firstname: firstname.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
      },
      credentials: 'include' // Important pour envoyer les cookies de session
    })

    // En cas de succès, naviguer vers la page d'accueil (ou de connexion)
    router.push('/') 
  } catch (error: any) { 
    console.error('Registration error', error)

    // Gestion plus spécifique des erreurs du serveur
    if (error.response && error.response._data && error.response._data.errors) {
      // Erreurs de validation de Laravel (code 422)
      for (const key in error.response._data.errors) {
        if (Object.prototype.hasOwnProperty.call(error.response._data.errors, key)) {
          error.response._data.errors[key].forEach((msg: string) => {
            serverErrors.value.push(msg)
          })
        }
      }
    } else if (error.response && error.response._data && error.response._data.message) {
        // Autres messages d'erreur génériques du serveur
        serverErrors.value.push(error.response._data.message)
    }
    else {
      // Erreurs réseau ou autres imprévus
      serverErrors.value.push('Une erreur inattendue est survenue. Veuillez réessayer.')
    }
  } finally {
    loading.value = false // Désactiver l'état de chargement quoi qu'il arrive
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

      <div v-if="serverErrors.length" class="bg-red-500 bg-opacity-20 border border-red-400 text-red-300 px-4 py-3 rounded-md mb-4">
        <p v-for="(error, index) in serverErrors" :key="index" class="text-sm">
          {{ error }}
        </p>
      </div>

      <div class="space-y-4">
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

        <div>
          <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full p-2.5 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>

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

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
          :class="{ 'opacity-50 cursor-not-allowed': loading }"
        >
          {{ loading ? 'Inscription en cours...' : "S'inscrire" }}
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