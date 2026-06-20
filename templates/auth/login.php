<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - PharmaFEFO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 antialiased font-sans">

<div class="max-w-md w-full bg-white p-6 sm:p-8 rounded-2xl shadow-xs border border-slate-100">

    <div class="text-center mb-6">
        <div class="inline-flex p-3 bg-emerald-50 rounded-2xl border border-emerald-100/50 text-emerald-600 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 0A48.536 48.536 0 0 1 12 3c1.464 0 2.893.065 4.305.191A2.493 2.493 0 0 1 18.5 5.618V18a2.5 2.5 0 0 1-2.5 2.5H5.5A2.5 2.5 0 0 1 3 18V5.618a2.493 2.493 0 0 1 1.695-2.427C6.107 3.065 7.536 3 9 3c.44 0 .878.006 1.314.019Z" />
            </svg>
        </div>
        <h2 class="text-xl font-black text-slate-900 tracking-tight">Pharma<span class="text-emerald-600">FEFO</span></h2>
        <p class="text-xs text-slate-400 font-medium mt-1">Gestion Sécurisée des Stocks Virtuels</p>
    </div>

    <!-- Zone d'erreur — remplie par JS si échec login -->
    <div id="login-error"
         class="hidden mb-5 p-3.5 bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold rounded-xl flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-rose-500 shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span id="login-error-text"></span>
    </div>

    <!-- Formulaire — soumission interceptée par login.js via fetch() -->
    <form id="login-form" class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Email Professionnel :</label>
            <input type="email" name="email" id="login-email"
                   placeholder="Ex: admin@pharma.com"
                   class="w-full p-3 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-slate-50/50 transition-all text-slate-800"
                   required autocomplete="email">
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Mot de Passe :</label>
            <input type="password" name="password" id="login-password"
                   placeholder="••••••••"
                   class="w-full p-3 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 bg-slate-50/50 transition-all text-slate-800"
                   required autocomplete="current-password">
        </div>
        <button type="submit" id="login-btn"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold p-3.5 rounded-xl text-xs shadow-xs hover:shadow-md transition-all uppercase tracking-wider mt-2">
            Se Connecter
        </button>
    </form>
</div>

<script src="js/app.js"></script>
<script src="js/login.js"></script>
</body>
</html>
