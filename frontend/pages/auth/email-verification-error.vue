<template>
  <div class="min-h-screen grid place-items-center bg-slate-900 p-4 text-center">
    <div class="w-full max-w-md space-y-6 bg-slate-800 p-8 rounded-xl shadow-lg">
      <h1 class="text-3xl font-bold" :class="isInitialMessage ? 'text-blue-400' : 'text-red-400'">
        {{ isInitialMessage ? 'Vérifiez votre boîte de réception !' : 'Erreur de vérification d\'e-mail' }}
      </h1>
      <p class="text-lg text-slate-300">
        {{ displayMessage }}
      </p>
      
      <NuxtLink
        v-if="!isInitialMessage"
        to="/auth/login"
        class="inline-block py-3 px-6 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-300"
      >
        Retour à la connexion
      </NuxtLink>

      <p v-if="showResendOption" class="text-sm text-slate-400 mt-4">
        Si vous n'avez pas reçu l'e-mail ou si le lien a expiré, vous pouvez en demander un nouveau.
        <button
          @click="resendVerificationEmail"
          :disabled="resending"
          class="text-blue-400 hover:underline ml-1"
          :class="{ 'opacity-50 cursor-not-allowed': resending }"
        >
          {{ resending ? 'Envoi...' : 'Renvoyer l\'e-mail' }}
        </button>
      </p>
      <p v-if="resendSuccess" class="text-green-500 text-sm mt-2">{{ resendSuccess }}</p>
      <p v-if="resendError" class="text-red-500 text-sm mt-2">{{ resendError }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const resending = ref(false)
const resendSuccess = ref('')
const resendError = ref('')

// Mapping des messages d'erreur du backend
const errorMessages: Record<string, string> = {
  invalid_link: 'Le lien de vérification est invalide ou a expiré.',
  user_not_found: 'L\'utilisateur associé à ce lien de vérification n\'a pas été trouvé.',
  default: 'Une erreur inconnue est survenue lors de la vérification de votre e-mail.'
}

// Détermine si c'est le message initial post-inscription (pas de paramètre de requête 'message')
const isInitialMessage = computed(() => !route.query.message);

// Message à afficher à l'utilisateur
const displayMessage = computed(() => {
  if (isInitialMessage.value) {
    return 'Un e-mail de vérification a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception (y compris les spams) pour activer votre compte. Vous ne pourrez pas vous connecter avant cette activation.';
  } else {
    // Si un paramètre 'message' est présent, c'est une erreur
    const messageKey = route.query.message as string;
    return errorMessages[messageKey] || errorMessages.default;
  }
});

// Affiche l'option de renvoi d'e-mail si c'est le message initial ou si le lien est invalide
const showResendOption = computed(() => {
  return isInitialMessage.value || route.query.message === 'invalid_link';
});

const resendVerificationEmail = async () => {
  resending.value = true
  resendSuccess.value = ''
  resendError.value = ''

  try {
    // Demande l'e-mail à l'utilisateur pour renvoyer le lien
    const userEmail = prompt('Veuillez entrer votre adresse email pour renvoyer le lien de vérification :');
    if (!userEmail) {
      resending.value = false;
      return;
    }

    // Appel à l'API pour renvoyer l'e-mail de vérification
    await $fetch('http://localhost:8000/api/resend-email-verify', {
      method: 'POST',
      body: { email: userEmail }, // Votre API doit accepter 'email' pour renvoyer le lien
      credentials: 'include'
    });

    resendSuccess.value = 'Un nouvel e-mail de vérification a été envoyé !';
  } catch (err: any) {
    console.error('Erreur lors du renvoi de l\'e-mail:', err);
    resendError.value = err.response?._data?.message || 'Échec de l\'envoi du lien de vérification. Veuillez réessayer.';
  } finally {
    resending.value = false;
  }
}
</script>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
