/* =========================
   THEME TOGGLE
========================= */

const themeToggle = document.getElementById("themeToggle");

/* LOAD SAVED THEME */

const savedTheme = localStorage.getItem("theme");

if(savedTheme){

    document.body.className = savedTheme;

    if(savedTheme === "light"){

        themeToggle.innerHTML = "☀ Light";

    }else{

        themeToggle.innerHTML = "🌙 Dark";

    }

}

/* TOGGLE THEME */

themeToggle.addEventListener("click", () => {

    if(document.body.classList.contains("dark")){

        document.body.classList.remove("dark");
        document.body.classList.add("light");

        themeToggle.innerHTML = "☀ Light";

        localStorage.setItem("theme", "light");

    }else{

        document.body.classList.remove("light");
        document.body.classList.add("dark");

        themeToggle.innerHTML = "🌙 Dark";

        localStorage.setItem("theme", "dark");

    }

});