document.addEventListener("DOMContentLoaded", function () {
  // --- PARTIE 1 : GESTION DU CHANGEMENT DE VIDÉO ET MISE À JOUR DE L'ID ---
  const videoList = document.querySelector("aside ul");
  const iframePlayer = document.getElementById("youtube-player");
  const capsuleIdInput = document.getElementById("id_capsule_input"); // Le champ caché qui stocke l'ID

  // DEBUG: On vérifie si les éléments principaux sont bien trouvés au chargement
  console.log("Éléments au chargement :", {
    videoList,
    iframePlayer,
    capsuleIdInput,
  });

  if (videoList && iframePlayer && capsuleIdInput) {
    videoList.addEventListener("click", function (event) {
      const clickedItem = event.target.closest("li[data-youtube-link]");
      console.log("Clic détecté sur l'élément :", clickedItem); // DEBUG: On voit sur quoi on a cliqué

      if (clickedItem) {
        const newLink = clickedItem.dataset.youtubeLink;
        const newCapsuleId = clickedItem.dataset.capsuleId; // On récupère l'ID de la capsule cliquée

        // DEBUG: On affiche l'ID qu'on a récupéré
        console.log("ID de capsule récupéré :", newCapsuleId);

        // Met à jour la source de l'iframe
        iframePlayer.src = newLink;

        // Met à jour la valeur du champ caché dans le formulaire
        capsuleIdInput.value = newCapsuleId;

        // DEBUG: On vérifie la valeur du champ juste après l'avoir modifiée
        console.log(
          "Nouvelle valeur du champ caché id_capsule_input :",
          capsuleIdInput.value
        );

        // Gère la classe 'active' pour le style
        const listItems = videoList.querySelectorAll("li[data-youtube-link]");
        listItems.forEach((item) => item.classList.remove("active"));
        clickedItem.classList.add("active");
      }
    });
  }

  // --- PARTIE 2 : GESTION DE L'ENVOI DU FORMULAIRE (AJAX) ---
  const form = document.getElementById("question-form");
  const feedbackDiv = document.getElementById("form-feedback");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      // DEBUG: On affiche la valeur de l'ID juste avant l'envoi
      console.log(
        "ID de capsule envoyé avec le formulaire :",
        capsuleIdInput.value
      );

      const formData = new FormData(form);
      const submitButton = form.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.textContent;

      if (feedbackDiv) feedbackDiv.style.display = "none";
      submitButton.disabled = true;
      submitButton.textContent = "Envoi en cours...";

      fetch(form.action, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (feedbackDiv) {
            feedbackDiv.textContent = data.message;
            // CORRECTION DU TYPO : feedback_div -> feedbackDiv
            feedbackDiv.className = "form-message";

            if (data.success) {
              feedbackDiv.classList.add("success");
              form.reset();
            } else {
              feedbackDiv.classList.add("error");
            }
            feedbackDiv.style.display = "block";

            setTimeout(() => {
              feedbackDiv.style.display = "none";
            }, 5000);
          }
        })
        .catch((error) => {
          console.error("Erreur:", error);
          if (feedbackDiv) {
            feedbackDiv.textContent =
              "Erreur réseau. Veuillez vérifier votre connexion.";
            feedbackDiv.className = "form-message error";
            feedbackDiv.style.display = "block";
          }
        })
        .finally(() => {
          submitButton.disabled = false;
          submitButton.textContent = originalButtonText;
        });
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // ... (votre code JavaScript existant pour la vidéo et le formulaire reste ici) ...

  // --- NOUVEAU : GESTION DU MENU LATÉRAL ---
  const menuToggle = document.getElementById("menu-toggle");
  const asideMenu = document.querySelector("aside");

  if (menuToggle && asideMenu) {
    menuToggle.addEventListener("click", function () {
      // Au clic sur le bouton, on ajoute ou on retire la classe 'is-open'
      asideMenu.classList.toggle("is-open");
    });
  }
});
