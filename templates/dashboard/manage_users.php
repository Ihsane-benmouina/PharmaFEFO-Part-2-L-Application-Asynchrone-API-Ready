<div class="space-y-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-4 gap-2">
        <div>
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2 tracking-tight">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.65-3.886m-3.21 3.521a10.5 10.5 0 0 0-7.422 0m0 0A10.511 10.511 0 0 1 12 13.25c2.256 0 4.352.708 6.074 1.913M11.25 10.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM18.75 8a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                Gestion des Comptes &amp; Équipe
            </h2>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Espace Administrateur — Configuration des accès restrictifs et rôles applicatifs</p>
        </div>
        <div class="bg-emerald-500/10 text-emerald-800 border border-emerald-500/10 px-2.5 py-1 rounded-xl text-[11px] font-bold flex items-center gap-1.5 shadow-xs uppercase tracking-wide shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Mode Admin Strict
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Formulaire de création d'utilisateur -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-xs border border-slate-100 lg:col-span-1 h-fit lg:sticky lg:top-6">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-slate-800">Ajouter un Collaborateur</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Créez un compte sécurisé pour l'équipe</p>
            </div>

            <!-- Feedback JS — succès ou erreur -->
            <div id="users-feedback" class="hidden mb-4"></div>

            <!-- Formulaire — soumission interceptée par users.js via fetch() -->
            <form id="create-user-form" class="space-y-4">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nom :</label>
                        <input type="text" name="nom" placeholder="Nom"
                               class="w-full p-3 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-slate-50/50 transition-all font-medium text-slate-800"
                               required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Prénom :</label>
                        <input type="text" name="prenom" placeholder="Prénom"
                               class="w-full p-3 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-slate-50/50 transition-all font-medium text-slate-800"
                               required>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Professionnel :</label>
                    <input type="email" name="email" placeholder="Ex: sara@pharma.com"
                           class="w-full p-3 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-slate-50/50 transition-all font-mono text-slate-800"
                           required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mot de passe provisoire :</label>
                    <input type="password" name="password" placeholder="••••••••"
                           class="w-full p-3 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-slate-50/50 transition-all text-slate-800"
                           required>
                    <span class="text-[10px] text-slate-400 font-medium block mt-1.5">Le mot de passe sera automatiquement crypté via PASSWORD_BCRYPT.</span>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Rôle &amp; Droits d'accès :</label>
                    <select name="role"
                            class="w-full p-3 border border-slate-200 rounded-xl text-xs bg-slate-50 text-slate-700 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all"
                            required>
                        <option value="pharmacien">💊 Pharmacien Titulaire (Droits US 2.1, 3.1, 4.1)</option>
                        <option value="preparateur">🩺 Préparateur en Pharmacie (Droits US 1.1, 3.1)</option>
                    </select>
                </div>

                <button type="submit" id="btn-create-user"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3.5 px-4 rounded-xl shadow-xs hover:shadow-md transition-all uppercase tracking-wider mt-2">
                    Créer l'accès sécurisé
                </button>
            </form>
        </div>

        <!-- Tableau des utilisateurs — rempli par JS via GET /api/v1/users -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl shadow-xs border border-slate-100 lg:col-span-2">
            <div class="mb-5">
                <h3 class="text-sm font-bold text-slate-800">Utilisateurs enregistrés</h3>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Visualisation des comptes actifs disposant d'un rôle d'authentification</p>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                            <th class="p-4">Membre de l'Équipe</th>
                            <th class="p-4">Identifiant / Email</th>
                            <th class="p-4 text-center">Niveau de Privilèges</th>
                        </tr>
                    </thead>
                    <!-- Corps du tableau — entièrement généré par users.js -->
                    <tbody id="users-table-body" class="text-xs">
                        <tr id="users-loading-row">
                            <td colspan="3" class="p-8 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-5 h-5 border-2 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                                    <span class="text-xs">Chargement des utilisateurs…</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
