<script lang="ts" setup>
// Mock data for demonstration. In a real app, this would come from an API.
// We'll simulate fetching this data in a real scenario later.
const admin = ref({
  id: 1,
  lastname: 'Admin',
  firstname: 'Super',
  email: 'admin@example.com',
  role: 'admin',
  createdAt: '2023-01-10T00:00:00Z' // ISO 8601 format is better for dates
})

const users = ref([
  {
    id: 1,
    lastname: 'Dupont',
    firstname: 'Jean',
    email: 'jean.dupont@example.com',
    role: 'user'
  },
  {
    id: 2,
    lastname: 'Martin',
    firstname: 'Sophie',
    email: 'sophie.martin@example.com',
    role: 'user'
  },
  {
    id: 3,
    lastname: 'Admin',
    firstname: 'Super',
    email: 'admin@example.com',
    role: 'admin'
  },
  {
    id: 4,
    lastname: 'Petit',
    firstname: 'Lucas',
    email: 'lucas.petit@example.com',
    role: 'user'
  }
])

const showModal = ref(false)
const currentUser = ref<any>(null) // Use any for flexibility with mock data, or define a User interface
const isEditing = ref(false)
const activeTab = ref('users') // 'stats' | 'users' | 'settings'

// State for modal loading and errors
const modalLoading = ref(false)
const modalErrors = ref<string[]>([])

const openEditModal = (user: any = null) => {
  // Reset errors and loading state
  modalErrors.value = []
  modalLoading.value = false

  // Initialize currentUser with default values or existing user data
  currentUser.value = user
    ? { ...user, password: '' } // Clear password when editing for security
    : { lastname: '', firstname: '', email: '', role: 'user', password: '' }
  isEditing.value = !!user
  showModal.value = true
}

const deleteUser = (userId: number) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')) {
    // Simulate API call for deletion
    // In a real app: await $fetch(`/api/users/${userId}`, { method: 'DELETE' });
    users.value = users.value.filter(u => u.id !== userId)
    // Add toast notification or similar feedback
    alert('Utilisateur supprimé avec succès!')
  }
}

const saveUser = async () => {
  modalErrors.value = [] // Clear previous errors
  modalLoading.value = true // Activate loading state

  // Basic client-side validation for modal
  if (!currentUser.value.lastname || !currentUser.value.firstname || !currentUser.value.email || !currentUser.value.role) {
    modalErrors.value.push('Tous les champs requis doivent être remplis.')
    modalLoading.value = false
    return
  }

  if (!isEditing.value && !currentUser.value.password) {
    modalErrors.value.push('Le mot de passe est requis pour un nouvel utilisateur.')
    modalLoading.value = false
    return
  }

  try {
    if (isEditing.value) {
      // Simulate API call for update
      // In a real app: await $fetch(`/api/users/${currentUser.value.id}`, { method: 'PUT', body: currentUser.value });
      const index = users.value.findIndex(u => u.id === currentUser.value.id)
      if (index !== -1) {
        users.value[index] = { ...currentUser.value }
      }
      alert('Utilisateur modifié avec succès!')
    } else {
      // Simulate API call for create
      // In a real app: await $fetch('/api/users', { method: 'POST', body: currentUser.value });
      const newUser = {
        ...currentUser.value,
        id: Math.max(...users.value.map(u => u.id)) + 1 || 1, // Ensure ID generation even if array is empty
      }
      users.value.push(newUser)
      alert('Utilisateur créé avec succès!')
    }
    showModal.value = false
  } catch (error: any) {
    console.error('Modal save error', error)
    modalErrors.value.push(error.message || 'Une erreur est survenue lors de l\'enregistrement.')
  } finally {
    modalLoading.value = false // Deactivate loading state
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-950 to-slate-800 text-slate-100 font-sans">
    <header class="bg-slate-800 p-4 shadow-xl">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-extrabold text-blue-400 tracking-wide">Administration Dashboard</h1>
        <div class="flex items-center space-x-4">
          <span class="text-base text-slate-300 font-semibold hidden md:block">{{ admin.firstname }} {{ admin.lastname }}</span>
          <button
            class="p-2 rounded-full bg-slate-700 hover:bg-blue-600 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800"
            title="User Profile Options"
          >
            <Icon name="heroicons:user-circle" class="w-6 h-6 text-slate-300 hover:text-white" />
          </button>
        </div>
      </div>
    </header>

    <div class="container mx-auto p-6 md:p-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1 bg-slate-800 rounded-xl p-4 shadow-lg h-fit sticky top-8">
          <nav class="space-y-3">
            <button
              @click="activeTab = 'stats'"
              class="w-full flex items-center p-3 rounded-lg text-lg font-medium transition-colors duration-200 group"
              :class="{'bg-blue-600 text-white shadow-md': activeTab === 'stats', 'hover:bg-slate-700 text-slate-300': activeTab !== 'stats'}"
            >
              <Icon name="heroicons:chart-bar" class="mr-3 w-6 h-6 text-slate-300 group-hover:text-white group-[.bg-blue-600]:text-white transition-colors duration-200" />
              Statistiques
            </button>
            <button
              @click="activeTab = 'users'"
              class="w-full flex items-center p-3 rounded-lg text-lg font-medium transition-colors duration-200 group"
              :class="{'bg-blue-600 text-white shadow-md': activeTab === 'users', 'hover:bg-slate-700 text-slate-300': activeTab !== 'users'}"
            >
              <Icon name="heroicons:users" class="mr-3 w-6 h-6 text-slate-300 group-hover:text-white group-[.bg-blue-600]:text-white transition-colors duration-200" />
              Gestion Utilisateurs
            </button>
            <button
              @click="activeTab = 'settings'"
              class="w-full flex items-center p-3 rounded-lg text-lg font-medium transition-colors duration-200 group"
              :class="{'bg-blue-600 text-white shadow-md': activeTab === 'settings', 'hover:bg-slate-700 text-slate-300': activeTab !== 'settings'}"
            >
              <Icon name="heroicons:cog" class="mr-3 w-6 h-6 text-slate-300 group-hover:text-white group-[.bg-blue-600]:text-white transition-colors duration-200" />
              Paramètres
            </button>
            <div class="border-t border-slate-700 my-3"></div> <button
              class="flex items-center p-3 rounded-lg text-lg font-medium hover:bg-red-600 text-red-300 hover:text-white transition-colors duration-200 w-full text-left"
              title="Déconnexion"
            >
              <Icon name="heroicons:arrow-left-on-rectangle" class="mr-3 w-6 h-6 text-red-300 group-hover:text-white transition-colors duration-200" />
              Déconnexion
            </button>
          </nav>
        </aside>

        <main class="lg:col-span-3 space-y-6">
          <section v-if="activeTab === 'stats'" class="bg-slate-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-blue-300">Statistiques de l'Application</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              <div class="bg-slate-700 p-5 rounded-lg border border-slate-600 flex items-center justify-between">
                <div>
                  <h3 class="text-sm text-slate-400 mb-1 uppercase tracking-wider">Total Utilisateurs</h3>
                  <p class="text-3xl font-bold text-white">{{ users.length }}</p>
                </div>
                <Icon name="heroicons:users" class="w-10 h-10 text-blue-400 opacity-50" />
              </div>
              <div class="bg-slate-700 p-5 rounded-lg border border-slate-600 flex items-center justify-between">
                <div>
                  <h3 class="text-sm text-slate-400 mb-1 uppercase tracking-wider">Administrateurs</h3>
                  <p class="text-3xl font-bold text-white">{{ users.filter(u => u.role === 'admin').length }}</p>
                </div>
                <Icon name="heroicons:user-group" class="w-10 h-10 text-purple-400 opacity-50" />
              </div>
              <div class="bg-slate-700 p-5 rounded-lg border border-slate-600 flex items-center justify-between">
                <div>
                  <h3 class="text-sm text-slate-400 mb-1 uppercase tracking-wider">Membres Standards</h3>
                  <p class="text-3xl font-bold text-white">{{ users.filter(u => u.role === 'user').length }}</p>
                </div>
                <Icon name="heroicons:user" class="w-10 h-10 text-green-400 opacity-50" />
              </div>
            </div>
          </section>

          <section v-if="activeTab === 'users'" class="space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-center bg-slate-800 rounded-xl p-6 shadow-lg">
              <h2 class="text-2xl font-bold text-blue-300 mb-4 sm:mb-0">Gestion des Utilisateurs</h2>
              <button
                @click="openEditModal()"
                class="flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-800"
              >
                <Icon name="heroicons:plus" class="mr-2 w-5 h-5" />
                Ajouter un utilisateur
              </button>
            </div>

            <div class="bg-slate-800 rounded-xl overflow-hidden shadow-lg border border-slate-700">
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-700">
                  <thead class="bg-slate-700">
                    <tr>
                      <th class="p-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Nom Complet</th>
                      <th class="p-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Email</th>
                      <th class="p-4 text-left text-sm font-semibold text-slate-300 uppercase tracking-wider">Rôle</th>
                      <th class="p-4 text-right text-sm font-semibold text-slate-300 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-slate-800 divide-y divide-slate-700">
                    <tr v-for="user in users" :key="user.id" class="hover:bg-slate-700 transition-colors duration-200">
                      <td class="p-4 whitespace-nowrap text-white font-medium">{{ user.firstname }} {{ user.lastname }}</td>
                      <td class="p-4 whitespace-nowrap text-slate-300">{{ user.email }}</td>
                      <td class="p-4 whitespace-nowrap">
                        <span
                          class="px-3 py-1 text-xs rounded-full font-semibold capitalize"
                          :class="user.role === 'admin' ? 'bg-purple-600 text-purple-100' : 'bg-blue-600 text-blue-100'"
                        >
                          {{ user.role }}
                        </span>
                      </td>
                      <td class="p-4 text-right whitespace-nowrap">
                        <div class="flex justify-end space-x-2">
                          <button
                            @click="openEditModal(user)"
                            class="px-3 py-1.5 text-blue-400 hover:bg-blue-900/50 rounded-lg transition-colors duration-200 text-sm font-medium"
                            title="Modifier l'utilisateur"
                          >
                            Modifier
                          </button>
                          <button
                            @click="deleteUser(user.id)"
                            class="px-3 py-1.5 text-red-400 hover:bg-red-900/50 rounded-lg transition-colors duration-200 text-sm font-medium"
                            title="Supprimer l'utilisateur"
                          >
                            Supprimer
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-if="users.length === 0" class="text-center text-slate-400 py-8 text-lg">
                  Aucun utilisateur trouvé. Ajoutez-en un nouveau pour commencer !
              </div>
            </div>
          </section>

          <section v-if="activeTab === 'settings'" class="bg-slate-800 rounded-xl p-6 shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-blue-300">Paramètres Généraux</h2>
            <div class="space-y-6">
              <div>
                <label class="block text-sm text-slate-400 font-medium mb-2">Nom du site</label>
                <input
                  type="text"
                  class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Mon Application"
                />
              </div>
              <div>
                <label class="block text-sm text-slate-400 font-medium mb-2">Logo du site</label>
                <div class="flex flex-col sm:flex-row items-center space-y-4 sm:space-y-0 sm:space-x-4">
                  <div class="w-20 h-20 bg-slate-700 rounded-lg flex items-center justify-center border border-slate-600 shadow-inner">
                    <Icon name="heroicons:photo" class="w-10 h-10 text-slate-500" />
                  </div>
                  <button class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-semibold transition-colors duration-200">
                    Changer le logo
                  </button>
                </div>
              </div>
              <div class="pt-4 border-t border-slate-700">
                <button class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-slate-800">
                  Enregistrer les modifications
                </button>
              </div>
            </div>
          </section>
        </main>
      </div>
    </div>

    <div v-if="showModal" class="fixed inset-0 bg-black/70 flex items-center justify-center p-4 z-50 animate-fade-in" @click.self="showModal = false">
      <div class="bg-slate-800 rounded-xl w-full max-w-md p-6 shadow-2xl transform scale-95 transition-all duration-300 ease-out-back"
           :class="{'scale-100': showModal}"
      >
        <div class="flex justify-between items-center border-b border-slate-700 pb-4 mb-6">
          <h3 class="text-xl font-bold text-blue-300">
            {{ isEditing ? 'Modifier l\'utilisateur' : 'Ajouter un nouvel utilisateur' }}
          </h3>
          <button @click="showModal = false" class="text-slate-400 hover:text-white transition-colors duration-200 p-1 rounded-full hover:bg-slate-700" title="Fermer">
            <Icon name="heroicons:x-mark" class="w-7 h-7" />
          </button>
        </div>

        <div v-if="modalErrors.length" class="bg-red-500 bg-opacity-20 border border-red-400 text-red-300 px-4 py-3 rounded-md mb-4">
          <p v-for="(error, index) in modalErrors" :key="index" class="text-sm">
            {{ error }}
          </p>
        </div>

        <form @submit.prevent="saveUser" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm text-slate-400 font-medium mb-1">Nom</label>
              <input
                v-model="currentUser.lastname"
                type="text"
                required
                class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label class="block text-sm text-slate-400 font-medium mb-1">Prénom</label>
              <input
                v-model="currentUser.firstname"
                type="text"
                required
                class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>

          <div>
            <label class="block text-sm text-slate-400 font-medium mb-1">Email</label>
            <input
              v-model="currentUser.email"
              type="email"
              required
              class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm text-slate-400 font-medium mb-1">Rôle</label>
            <select
              v-model="currentUser.role"
              class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="user">Utilisateur</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>

          <div v-if="!isEditing">
            <label class="block text-sm text-slate-400 font-medium mb-1">Mot de passe</label>
            <input
              v-model="currentUser.password"
              type="password"
              required
              class="w-full p-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <div class="flex justify-end space-x-3 pt-4 border-t border-slate-700">
            <button
              type="button"
              @click="showModal = false"
              class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 focus:ring-offset-slate-800"
            >
              Annuler
            </button>
            <button
              type="submit"
              :disabled="modalLoading"
              class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-800"
              :class="{ 'opacity-50 cursor-not-allowed': modalLoading }"
            >
              {{ modalLoading ? 'Chargement...' : (isEditing ? 'Modifier' : 'Créer') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style>
/* Basic fade-in animation for modal overlay */
.animate-fade-in {
  animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Optional: Slight bounce-in animation for modal content */
.ease-out-back {
  transition-timing-function: cubic-bezier(0.34, 1.56, 0.64, 1);
}
</style>