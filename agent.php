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

body{
    margin:0;
    padding:0;
    background: radial-gradient(circle at center, #020617, #000);
    overflow:hidden;
    color:white;
    font-family:Arial, sans-serif;
}

.center{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    flex-direction:column;
}

.orb-container{
    position:relative;
    width:220px;
    height:220px;
    display:flex;
    justify-content:center;
    align-items:center;
}

.ball{
    width:180px;
    height:180px;
    border-radius:50%;
    background: radial-gradient(circle, #22d3ee, #0ea5e9, #020617);
    box-shadow:
        0 0 40px cyan,
        0 0 80px rgba(0,255,255,0.7),
        0 0 140px rgba(0,255,255,0.3);
    transition:0.3s;
    z-index:2;
}

.wave{
    position:absolute;
    width:220px;
    height:220px;
    border-radius:50%;
    border:2px solid cyan;
    display:none;
    animation:wave 2s infinite;
}

@keyframes wave{
    0%{
        transform:scale(1);
        opacity:0.7;
    }
    100%{
        transform:scale(1.8);
        opacity:0;
    }
}

.speaking{
    animation:speaking 0.7s infinite;
}

@keyframes speaking{
    0%{
        transform:scale(1);
    }
    50%{
        transform:scale(1.13);
    }
    100%{
        transform:scale(1);
    }
}

.status{
    margin-top:25px;
    font-size:14px;
    color:cyan;
    letter-spacing:3px;
    text-align:center;
}

.admin-btn{
    position:absolute;
    top:20px;
    right:20px;
    border:1px solid cyan;
    color:cyan;
    padding:8px 18px;
    border-radius:10px;
    font-size:12px;
    transition:0.3s;
    text-decoration:none;
}

.admin-btn:hover{
    background:cyan;
    color:black;
}

</style>
</head>

<body>

<a href="login.php" class="admin-btn">Admin</a>

<div class="center">

    <div class="orb-container">

        <div class="wave" id="wave"></div>

        <div class="ball" id="ball"></div>

    </div>

    <div class="status" id="status">
        STARTING...
    </div>

</div>

<script>

/* =========================================
   ELEMENTS
========================================= */

const ball = document.getElementById("ball");
const wave = document.getElementById("wave");
const statusText = document.getElementById("status");

/* =========================================
   VARIABLES
========================================= */

let recognition = null;

let standbyRecognition = null;

let isListening = false;

let isProcessing = false;

let standbyTimer = null;

let mode = "active";

/* =========================================
   UI STATE
========================================= */

function setState(state){

    ball.classList.remove("speaking");

    wave.style.display = "none";

    if(state === "listening"){

        wave.style.display = "block";

        statusText.innerText = "LISTENING...";
    }

    if(state === "processing"){

        ball.classList.add("speaking");

        statusText.innerText = "THINKING...";
    }

    if(state === "speaking"){

        ball.classList.add("speaking");

        statusText.innerText = "AI SPEAKING...";
    }

    if(state === "idle"){

        statusText.innerText = "ACTIVE • WAITING";
    }

    if(state === "standby"){

        statusText.innerText = "STANDBY • SAY HEY DK";
    }
}

/* =========================================
   STARTUP VOICE
========================================= */

function startupVoice(){

    const steps = [
        "Authentication successful",
        "Welcome sir",
        "System ready"
    ];

    let i = 0;

    function next(){

        if(i >= steps.length){

            setState("idle");

            resetStandbyTimer();

            startListening();

            return;
        }

        const msg = new SpeechSynthesisUtterance(steps[i]);

        msg.pitch = 0.8;

        msg.rate = 1;

        ball.classList.add("speaking");

        statusText.innerText = steps[i].toUpperCase();

        msg.onend = () => {

            ball.classList.remove("speaking");

            i++;

            setTimeout(next, 500);
        };

        speechSynthesis.speak(msg);
    }

    next();
}

/* =========================================
   START LISTENING
========================================= */

function startListening(){

    if(isListening || isProcessing) return;

    stopStandbyRecognition();

    recognition = new (
        window.SpeechRecognition ||
        window.webkitSpeechRecognition
    )();

    recognition.lang = "en-US";

    recognition.interimResults = false;

    recognition.maxAlternatives = 1;

    isListening = true;

    setState("listening");

    recognition.start();

    recognition.onresult = function(event){

        const text = event.results[0][0].transcript.trim();

        console.log("USER:", text);

        if(text.length > 1){

            stopListening();

            resetStandbyTimer();

            processCommand(text);
        }
    };

    recognition.onerror = function(event){

        console.log("Recognition Error:", event.error);

        stopListening();

        restartListening();
    };

    recognition.onend = function(){

        isListening = false;

        if(mode === "active" && !isProcessing){

            restartListening();
        }
    };
}

/* =========================================
   STOP LISTENING
========================================= */

function stopListening(){

    if(recognition){

        recognition.onend = null;

        recognition.stop();

        recognition = null;
    }

    isListening = false;
}

/* =========================================
   SAFE RESTART
========================================= */

function restartListening(){

    if(mode !== "active") return;

    if(isProcessing) return;

    setTimeout(() => {

        if(!isListening && !isProcessing){

            startListening();
        }

    }, 700);
}

/* =========================================
   PROCESS COMMAND
========================================= */

async function processCommand(text){

    try{

        isProcessing = true;

        stopListening();

        clearTimeout(standbyTimer);

        setState("processing");

        speechSynthesis.cancel();

        console.log("Sending to API:", text);

        const response = await fetch("api.php", {

            method:"POST",

            headers:{
                "Content-Type":"application/json"
            },

            body:JSON.stringify({
                message:text
            })
        });

        const data = await response.json();

        console.log("AI RESPONSE:", data);

        let reply = data.reply || "No response";

        reply = reply.replace(/[*#]/g, "");

        speak(reply);

    }catch(error){

        console.log("API ERROR:", error);

        isProcessing = false;

        setState("idle");

        restartListening();
    }
}

/* =========================================
   SPEAK
========================================= */

function speak(text){

    speechSynthesis.cancel();

    stopListening();

    clearTimeout(standbyTimer);

    setState("speaking");

    const msg = new SpeechSynthesisUtterance(text);

    const voices = speechSynthesis.getVoices();

    const voice =
        voices.find(v =>
            v.name.toLowerCase().includes("google")
        ) || voices[0];

    msg.voice = voice;

    msg.pitch = 0.9;

    msg.rate = 1;

    msg.volume = 1;

    msg.onstart = () => {

        isProcessing = true;

        setState("speaking");
    };

    msg.onend = () => {

        console.log("AI FINISHED");

        isProcessing = false;

        mode = "active";

        setState("idle");

        resetStandbyTimer();

        setTimeout(() => {

            startListening();

        }, 700);
    };

    speechSynthesis.speak(msg);
}

/* =========================================
   STANDBY TIMER
========================================= */

function resetStandbyTimer(){

    clearTimeout(standbyTimer);

    if(isProcessing) return;

    standbyTimer = setTimeout(() => {

        if(!isProcessing){

            goStandby();
        }

    }, 30000);
}

/* =========================================
   GO STANDBY
========================================= */

function goStandby(){

    if(isProcessing) return;

    mode = "standby";

    stopListening();

    setState("standby");

    startStandbyRecognition();
}

/* =========================================
   STANDBY RECOGNITION
========================================= */

function startStandbyRecognition(){

    stopStandbyRecognition();

    standbyRecognition = new (
        window.SpeechRecognition ||
        window.webkitSpeechRecognition
    )();

    standbyRecognition.continuous = true;

    standbyRecognition.lang = "en-US";

    standbyRecognition.start();

    standbyRecognition.onresult = function(event){

        const text = event.results[
            event.results.length - 1
        ][0].transcript.toLowerCase();

        console.log("WAKE:", text);

        if(text.includes("hey dk")){

            stopStandbyRecognition();

            mode = "active";

            setState("idle");

            resetStandbyTimer();

            setTimeout(() => {

                startListening();

            }, 500);
        }
    };

    standbyRecognition.onerror = function(){

        if(mode === "standby"){

            setTimeout(startStandbyRecognition, 1000);
        }
    };

    standbyRecognition.onend = function(){

        if(mode === "standby"){

            setTimeout(startStandbyRecognition, 1000);
        }
    };
}

/* =========================================
   STOP STANDBY RECOGNITION
========================================= */

function stopStandbyRecognition(){

    if(standbyRecognition){

        standbyRecognition.onend = null;

        standbyRecognition.stop();

        standbyRecognition = null;
    }
}

/* =========================================
   INIT
========================================= */

window.onload = () => {

    startupVoice();
};

</script>

</body>
</html>