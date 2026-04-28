<?php
session_start();

if (!isset($_SESSION["access"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DK Voice Agent</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
  background: radial-gradient(circle at center, #020617, #000);
  color: white;
  overflow: hidden;
}

.center {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  flex-direction: column;
}

/* ORB */
.orb-container {
  position: relative;
  width: 180px;
  height: 180px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* BALL */
.ball {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: radial-gradient(circle, #06b6d4, #0ea5e9, #020617);
  box-shadow: 0 0 30px cyan, 0 0 80px cyan;
  transition: all 0.3s ease;
  position: relative;
  z-index: 2;
}

/* WAVE */
.wave {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 220px;
  height: 220px;
  transform: translate(-50%, -50%);
  border-radius: 50%;
  border: 2px solid cyan;
  animation: wave 2s infinite;
  display: none;
  z-index: 1;
}

@keyframes wave {
  0% { transform: translate(-50%, -50%) scale(1); opacity: 0.6; }
  100% { transform: translate(-50%, -50%) scale(1.8); opacity: 0; }
}

/* SPEAKING */
.speaking {
  animation: speak 0.6s infinite;
}

@keyframes speak {
  0% { transform: scale(1); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); }
}

/* STATUS */
.status {
  margin-top: 15px;
  font-size: 13px;
  color: cyan;
  letter-spacing: 2px;
}

/* ADMIN BUTTON */
.admin-btn {
  position: absolute;
  top: 20px;
  right: 20px;
  border: 1px solid cyan;
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 12px;
  color: cyan;
  transition: 0.3s;
}

.admin-btn:hover {
  background: cyan;
  color: black;
}
</style>
</head>

<body>

<!-- ADMIN BUTTON -->
<a href="login.php" class="admin-btn">Admin</a>

<div class="center">

  <div class="orb-container">
    <div class="wave" id="wave"></div>
    <div id="ball" class="ball"></div>
  </div>

  <div id="status" class="status">STARTING...</div>

</div>

<script>
const ball = document.getElementById("ball");
const wave = document.getElementById("wave");
const statusText = document.getElementById("status");

let mode = "active";
let standbyTimer;
let audioStream;

/* ========= STATE CONTROL ========= */
function setState(state) {
  ball.classList.remove("speaking");
  wave.style.display = "none";

  if (state === "listening") {
    wave.style.display = "block";
    statusText.innerText = "ACTIVE • LISTENING";
  }

  if (state === "processing") {
    ball.classList.add("speaking");
    statusText.innerText = "PROCESSING...";
  }

  if (state === "standby") {
    statusText.innerText = "STANDBY • SAY HEY DK";
  }

  if (state === "active-idle") {
    statusText.innerText = "ACTIVE • WAITING";
  }
}

/* ========= MIC ALWAYS ON ========= */
async function startMicStream() {
  try {
    audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
  } catch (e) {
    console.log("Mic denied");
  }
}

/* ========= STARTUP VOICE ========= */
function startupVoice() {

  const steps = [
    "Authentication successful",
    "Welcome sir",
    "System ready"
  ];

  let i = 0;

  function run() {

    if (i >= steps.length) {
      startActiveListening();
      resetStandbyTimer();
      return;
    }

    const msg = new SpeechSynthesisUtterance(steps[i]);
    msg.pitch = 0.7;

    statusText.innerText = steps[i].toUpperCase();
    ball.classList.add("speaking");

    msg.onend = () => {
      ball.classList.remove("speaking");
      i++;
      setTimeout(run, 400);
    };

    speechSynthesis.speak(msg);
  }

  run();
}

/* ========= INIT ========= */
window.onload = function () {
  startMicStream();
  startupVoice();
};

/* ========= ACTIVE MODE ========= */
function startActiveListening() {

  mode = "active";

  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();

  setState("listening");

  recognition.start();

  recognition.onresult = function(event) {

    const text = event.results[0][0].transcript.trim();

    if (text.length > 1) {
      resetStandbyTimer();
      processCommand(text);
    }
  };

  recognition.onend = function() {

    if (mode === "active") {
      setState("active-idle");
      setTimeout(startActiveListening, 200);
    }
  };
}

/* ========= TIMER ========= */
function resetStandbyTimer() {
  clearTimeout(standbyTimer);

  standbyTimer = setTimeout(() => {
    goStandby();
  }, 30000);
}

/* ========= STANDBY ========= */
function goStandby() {
  mode = "standby";
  setState("standby");
  startWakeWord();
}

/* ========= WAKE WORD ========= */
function startWakeWord() {

  const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
  recognition.continuous = true;

  recognition.start();

  recognition.onresult = function(event) {
    const text = event.results[event.results.length - 1][0].transcript.toLowerCase();

    if (text.includes("hey dk")) {
      recognition.stop();

      statusText.innerText = "ACTIVATING...";
      resetStandbyTimer();
      startActiveListening();
    }
  };

  recognition.onend = function() {
    if (mode === "standby") {
      setTimeout(startWakeWord, 300);
    }
  };
}

/* ========= PROCESS ========= */
function processCommand(text) {

  setState("processing");

  fetch("api.php", {
    method: "POST",
    headers: {"Content-Type":"application/json"},
    body: JSON.stringify({message:text})
  })
  .then(res=>res.json())
  .then(data=>{
    speak(data.reply || "Done");
  });
}

/* ========= SPEAK ========= */
function speak(text) {
  const voices = speechSynthesis.getVoices();
  const male = voices.find(v => v.name.toLowerCase().includes("google"));

  const msg = new SpeechSynthesisUtterance(text);
  msg.voice = male || voices[0];
  msg.pitch = 0.7;

  msg.onend = () => {
    setState("active-idle");
  };

  speechSynthesis.speak(msg);
}
</script>

</body>
</html>