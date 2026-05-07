<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Secure Access</title>
<script src="https://cdn.tailwindcss.com"></script>

<link rel="stylesheet" href="./assets/index/styles.css">

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