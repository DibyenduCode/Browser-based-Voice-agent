
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Access</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
  background: radial-gradient(circle at center, #020617, #000);
  color: white;
  overflow: hidden;
}

/* grid background */
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image: linear-gradient(rgba(0,255,255,0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(0,255,255,0.05) 1px, transparent 1px);
  background-size: 40px 40px;
}

/* card glow */
.card {
  box-shadow: 0 0 30px rgba(0,255,255,0.2),
              inset 0 0 20px rgba(0,255,255,0.05);
  border: 1px solid rgba(0,255,255,0.2);
}

/* input focus */
input:focus {
  box-shadow: 0 0 10px cyan;
}

/* button glow */
.btn {
  background: linear-gradient(90deg, #06b6d4, #3b82f6);
  box-shadow: 0 0 15px rgba(0,255,255,0.5);
}
.btn:hover {
  box-shadow: 0 0 25px cyan;
  transform: scale(1.05);
}

/* scan line */
.card::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 2px;
  background: rgba(0,255,255,0.5);
  top: 0;
  left: 0;
  animation: scan 3s linear infinite;
}

@keyframes scan {
  0% { top: 0; }
  100% { top: 100%; }
}
</style>
</head>

<body class="flex items-center justify-center h-screen">

<div class="relative card backdrop-blur-xl bg-black/40 p-10 rounded-2xl w-80 text-center">

  <!-- Title -->
  <h1 class="text-cyan-400 text-2xl tracking-widest mb-2">
    ADMIN ACCESS
  </h1>

  <p class="text-gray-400 text-xs mb-6 tracking-wider">
    Secure Control Panel
  </p>

  <!-- Form -->
  <form class="space-y-5">

    <input 
      type="email"
      placeholder="Email"
      class="w-full p-3 bg-black/50 border border-white/10 rounded-xl outline-none text-center"
    />

    <input 
      type="password"
      placeholder="Password"
      class="w-full p-3 bg-black/50 border border-white/10 rounded-xl outline-none text-center tracking-widest"
    />

    <button class="btn w-full p-3 rounded-xl font-semibold transition">
      ▶ LOGIN
    </button>

  </form>

  <!-- Error -->
  <?php if(isset($_GET['error'])): ?>
    <p id="errorMsg" class="text-red-400 mt-5 text-sm tracking-wider">
      ❌ ACCESS DENIED
    </p>
  <?php endif; ?>

  <!-- Footer -->
  <p class="text-xs text-gray-500 mt-6">
    DK Voice Engine • Admin Panel
  </p>

</div>

</body>
</html>