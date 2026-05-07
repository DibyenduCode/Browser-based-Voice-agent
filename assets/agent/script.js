
/* =========================================
   ELEMENTS
========================================= */

const ball = document.getElementById("ball");

const wave = document.getElementById("wave");

const statusText = document.getElementById("status");

const chatBody = document.getElementById("chatBody");

const chatInput = document.getElementById("chatInput");

const sendBtn = document.getElementById("sendBtn");

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
   CHAT HELPERS
========================================= */

function addMessage(type, text){

    const div = document.createElement("div");

    div.classList.add("message");

    if(type === "user"){

        div.classList.add("user-message");

    }else{

        div.classList.add("ai-message");
    }

    div.innerText = text;

    chatBody.appendChild(div);

    chatBody.scrollTop = chatBody.scrollHeight;
}

/* =========================================
   INPUT CONTROL
========================================= */

function disableChat(){

    chatInput.disabled = true;

    sendBtn.disabled = true;
}

function enableChat(){

    chatInput.disabled = false;

    sendBtn.disabled = false;

    chatInput.focus();
}

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

        "Welcome back",

        "Voice assistant is ready"
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

        msg.lang = "en-IN";

        msg.pitch = 1;

        msg.rate = 0.95;

        ball.classList.add("speaking");

        statusText.innerText = steps[i].toUpperCase();

        disableChat();

        msg.onend = () => {

            ball.classList.remove("speaking");

            i++;

            enableChat();

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

    recognition.lang = "en-IN";

    recognition.interimResults = false;

    recognition.maxAlternatives = 1;

    isListening = true;

    setState("listening");

    recognition.start();

    recognition.onresult = function(event){

        const text = event.results[0][0].transcript.trim();

        console.log("USER:", text);

        if(text.length > 1){

            addMessage("user", text);

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

    if(isProcessing) return;

    try{

        isProcessing = true;

        disableChat();

        stopListening();

        clearTimeout(standbyTimer);

        setState("processing");

        speechSynthesis.cancel();

        console.log("Sending:", text);

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

        reply = reply.replace(/\*/g, "");

        reply = reply.replace(/#/g, "");

        reply = reply.replace(/\n/g, " ");

        reply = reply.trim();

        addMessage("ai", reply);

        speak(reply);

    }catch(error){

        console.log("API ERROR:", error);

        isProcessing = false;

        enableChat();

        setState("idle");

        addMessage("ai", "Connection problem occurred");

        speak("Connection problem occurred");
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

    disableChat();

    const msg = new SpeechSynthesisUtterance(text);

    const voices = speechSynthesis.getVoices();

    let voice =

        voices.find(v =>
            v.name.includes("Google UK English Female")
        ) ||

        voices.find(v =>
            v.name.includes("Google US English")
        ) ||

        voices.find(v =>
            v.name.includes("Heera")
        ) ||

        voices.find(v =>
            v.name.includes("Zira")
        ) ||

        voices.find(v =>
            v.name.includes("Female")
        ) ||

        voices.find(v =>
            v.lang === "en-IN"
        ) ||

        voices[0];

    msg.voice = voice;

    msg.lang = "en-IN";

    msg.pitch = 1.1;

    msg.rate = 0.96;

    msg.volume = 1;

    msg.onstart = () => {

        isProcessing = true;

        disableChat();

        setState("speaking");
    };

    msg.onend = () => {

        console.log("AI FINISHED");

        isProcessing = false;

        enableChat();

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

    standbyRecognition.lang = "en-IN";

    standbyRecognition.start();

    standbyRecognition.onresult = function(event){

        const text = event.results[
            event.results.length - 1
        ][0].transcript.toLowerCase();

        console.log("WAKE:", text);

        if(

            text.includes("hey dk") ||

            text.includes("hey d k")

        ){

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
   SEND TEXT MESSAGE
========================================= */

function sendTextMessage(){

    if(isProcessing) return;

    const text = chatInput.value.trim();

    if(text.length < 1) return;

    addMessage("user", text);

    chatInput.value = "";

    resetStandbyTimer();

    processCommand(text);
}

/* =========================================
   EVENTS
========================================= */

sendBtn.addEventListener("click", sendTextMessage);

chatInput.addEventListener("keydown", function(e){

    if(e.key === "Enter"){

        e.preventDefault();

        sendTextMessage();
    }
});

/* =========================================
   INIT
========================================= */

window.onload = () => {

    startupVoice();
};
