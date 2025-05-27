<script lang="ts" setup>
import { ref } from 'vue' 
import { useRouter } from 'vue-router' 

// Références pour les champs du formulaire
const lastname = ref('')
const firstname = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const acceptTerms = ref(false) 

const loading = ref(false)
const serverErrors = ref<string[]>([]) 
const showEmailVerificationMessage = ref(false)

const router = useRouter()


const handleRegister = async () => {
  serverErrors.value = []
  showEmailVerificationMessage.value = false 
  
  if (password.value !== passwordConfirmation.value) {
    serverErrors.value.push('Les mots de passe ne correspondent pas.')
    return 
  }

  if (!acceptTerms.value) {
    serverErrors.value.push('Vous devez accepter les conditions pour vous inscrire.')
    return 
  }

  loading.value = true // Activer l'état de chargement du bouton

  try {
    // Ceci est crucial pour la protection CSRF dans les requêtes API
    await $fetch('http://localhost:8000/sanctum/csrf-cookie', {
      credentials: 'include' 
    })

    // Le corps de la requête doit correspondre aux attentes de votre fonction register dans ApiController
    await $fetch('http://localhost:8000/api/register', { 
      method: 'POST',
      body: {
        name: `${firstname.value} ${lastname.value}`, 
        lastname: lastname.value,
        firstname: firstname.value,
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
      },
      credentials: 'include' // Important pour envoyer les cookies de session
    })

   
    router.push('/')

  } catch (error: any) { 
    console.error('Erreur d\'inscription:', error); 

    // Gérer les erreurs du serveur
    if (error.response && error.response._data) {
      const errorData = error.response._data;

      // Gérer les erreurs de validation (statut 422)
      if (error.response.status === 422) {
        if (errorData.errors) {
          // Si errorData.errors est un objet (le format attendu de Laravel MessageBag)
          if (typeof errorData.errors === 'object' && errorData.errors !== null) {
            for (const key in errorData.errors) {
              if (Object.prototype.hasOwnProperty.call(errorData.errors, key)) {
                const fieldErrors = errorData.errors[key];
                if (Array.isArray(fieldErrors)) {
                  // C'est le cas normal : fieldErrors est un tableau de messages
                  fieldErrors.forEach((err: string) => serverErrors.value.push(err));
                } else if (typeof fieldErrors === 'string') {
                  // Si fieldErrors est une chaîne (pas un tableau), ajoutez-la directement
                  serverErrors.value.push(fieldErrors);
                }
              }
            }
          } 
          // Si errorData.errors est une chaîne de caractères (par exemple, de ->first())
          else if (typeof errorData.errors === 'string') {
            serverErrors.value.push(errorData.errors);
          }
        } 
        // Si aucune erreur spécifique mais un message général pour le 422
        else if (errorData.message) {
          serverErrors.value.push(errorData.message);
        }
      } 
      // Gérer le cas spécifique de l'e-mail non vérifié (statut 403, plus typique du login)
      else if (error.response.status === 403 && errorData.message === 'Votre email n\'est pas vérifié. Veuillez vérifier votre boîte de réception.') {
        showEmailVerificationMessage.value = true;
      }
      // Gérer les autres messages d'erreur du backend
      else if (errorData.message) {
        serverErrors.value.push(errorData.message);
      }
    } else {
      // Erreur réseau ou réponse inattendue du serveur
      serverErrors.value.push('Une erreur inattendue est survenue. Veuillez réessayer.');
    }
  } finally {
    loading.value = false; // Désactiver l'état de chargement quoi qu'il arrive
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

      <div v-if="showEmailVerificationMessage" class="bg-yellow-500 bg-opacity-20 border border-yellow-400 text-yellow-300 px-4 py-3 rounded-md mb-4">
        <p class="text-sm">
          Votre email n'est pas vérifié. Veuillez vérifier votre boîte de réception.
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
