<template>
  <div class="min-h-screen grid place-items-center bg-slate-900 p-4 text-center">
    <div class="w-full max-w-md space-y-6 bg-slate-800 p-8 rounded-xl shadow-lg">
      <h1 class="text-3xl font-bold text-green-400">Vérification d'e-mail réussie !</h1>
      <p class="text-lg text-slate-300">
        Votre adresse e-mail a été vérifiée avec succès. Redirection vers la page de connexion...
      </p>
      </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router' // Importez useRoute aussi

const router = useRouter()
const route = useRoute()

onMounted(() => {
  // Optionnel: Vous pouvez récupérer le message si vous voulez afficher un message spécifique
  // par exemple, si l'email était déjà vérifié
  const messageKey = route.query.message as string;

  // Redirige vers la page de connexion après un court délai
  // Cela permet à l'utilisateur de voir le message de succès avant la redirection
  setTimeout(() => {
    router.push('/auth/login')
  }, 3000) // Redirection après 3 secondes
})
</script>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>
