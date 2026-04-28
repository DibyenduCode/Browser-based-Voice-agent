<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Secure Access</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>

/* ===== BACKGROUND ===== */
body {
  background: radial-gradient(circle at top, #020617, #000);
  overflow: hidden;
}

/* Cyber grid overlay */
body::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image: linear-gradient(rgba(0,255,255,0.05) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(0,255,255,0.05) 1px, transparent 1px);
  background-size: 40px 40px;
  z-index: 0;
}

/* Floating glow */
.glow-bg::before,
.glow-bg::after {
  content: "";
  position: absolute;
  width: 400px;
  height: 400px;
  background: cyan;
  filter: blur(150px);
  opacity: 0.15;
  border-radius: 50%;
  animation: float 8s infinite alternate;
}

.glow-bg::after {
  right: 0;
  bottom: 0;
  animation-delay: 3s;
}

@keyframes float {
  from { transform: translateY(0px); }
  to { transform: translateY(-60px); }
}

/* ===== CARD ===== */
.card-glow {
  box-shadow: 0 0 25px rgba(0,255,255,0.2),
              inset 0 0 20px rgba(0,255,255,0.05);
  border: 1px solid rgba(0,255,255,0.2);
  position: relative;
  overflow: hidden;
}

/* scanning line */
.card-glow::before {
  content: "";
  position: absolute;
  width: 100%;
  height: 2px;
  background: rgba(0,255,255,0.6);
  top: 0;
  left: 0;
  animation: scan 3s linear infinite;
}

@keyframes scan {
  0% { top: 0; }
  100% { top: 100%; }
}

/* ===== INPUT ===== */
.input-focus {
  background: rgba(0,0,0,0.6);
  border: 1px solid rgba(0,255,255,0.2);
}

.input-focus:focus {
  box-shadow: 0 0 15px rgba(0,255,255,0.8);
  border-color: cyan;
}

/* ===== BUTTON ===== */
.cyber-btn {
  background: linear-gradient(90deg, #06b6d4, #3b82f6);
  box-shadow: 0 0 15px rgba(0,255,255,0.5);
}

.cyber-btn:hover {
  box-shadow: 0 0 25px rgba(0,255,255,1);
  transform: scale(1.05);
}

/* ===== ERROR ===== */
#errorMsg {
  text-shadow: 0 0 10px red;
}

/* ===== SHAKE ===== */
@keyframes shake {
  0%,100% { transform: translateX(0); }
  25% { transform: translateX(-6px); }
  75% { transform: translateX(6px); }
}
.shake {
  animation: shake 0.3s;
}

</style>
</head>

<body class="flex items-center justify-center h-screen text-white glow-bg relative">

<div id="card" class="bg-black/40 backdrop-blur-xl p-10 rounded-3xl w-80 text-center card-glow transition z-10">

  <!-- Title -->
  <h1 class="text-3xl font-semibold text-cyan-400 tracking-widest mb-2 animate-pulse">
    DK ACCESS
  </h1>

  <p class="text-gray-400 text-xs mb-6 tracking-wider">
    ◉ Secure Voice System Interface
  </p>

  <!-- FORM -->
  <form action="" method="POST" class="space-y-5">

    <input 
      name="pin"
      type="password"
      maxlength="6"
      placeholder="••••••"
      required
      class="input-focus w-full p-3 text-center text-xl tracking-[10px] rounded-xl outline-none transition"
    />

    <button 
      type="submit"
      class="cyber-btn w-full p-3 rounded-xl font-semibold transition"
    >
      ▶ UNLOCK SYSTEM
    </button>

  </form>

  <!-- Error -->
  <?php if(isset($_GET['error'])): ?>
    <p id="errorMsg" class="text-red-400 mt-5 text-sm tracking-wider">
      ❌ ACCESS DENIED
    </p>
  <?php endif; ?>

  <!-- Footer -->
  <p class="text-xs text-gray-500 mt-6 tracking-widest">
    Powered by DK Voice Engine
  </p>

</div>

<script>
// Shake animation if error
<?php if(isset($_GET['error'])): ?>
  document.getElementById("card").classList.add("shake");

  const msg = new SpeechSynthesisUtterance("Access denied. Invalid pin.");
  msg.rate = 1;
  msg.pitch = 1;
  msg.volume = 1;

  speechSynthesis.speak(msg);
<?php endif; ?>
</script>

</body>
</html>

<?php

include "./config/connect.php";

if(isset($_POST["pin"])){
    $pin=$_POST["pin"];
    $dbpin=$conn->prepare("SELECT * FROM pin");
    $dbpin->execute();
    $dbpinarr=$dbpin->fetchAll();

    $dbpincode=$dbpinarr[0]["pin"];

    if($pin==$dbpincode){
        session_start();
        $_SESSION["access"]=true;
        header("location:agent.php");
        
    }
    else{
    header("location:index.php?error=1");
    }

}
?>