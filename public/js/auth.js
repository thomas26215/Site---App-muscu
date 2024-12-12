const container = document.getElementById("container");
const overlayBtn = document.getElementById("overlayBtn");
const signupForm = document.getElementById("signupForm");

overlayBtn.addEventListener("click", () => {
    container.classList.toggle("right-panel-active");
    overlayBtn.classList.remove("btnScaled");
    window.requestAnimationFrame(() => {
        overlayBtn.classList.add("btnScaled");
    });
});

function validateStep(stepNumber) {
    const stepElement = document.getElementById("step" + stepNumber);
    const inputs = stepElement.querySelectorAll("input, select");
    for (let input of inputs) {
        if (input.hasAttribute("required") && !input.value) {
            return false;
        }
    }
    return true;
}

function updateNextButton(stepNumber) {
    const nextButton = document.querySelector(
        `#step${stepNumber} button[onclick^="nextStep"]`,
    );
    if (nextButton) {
        nextButton.disabled = !validateStep(stepNumber);
    }
}

function nextStep(currentStep) {
    if (validateStep(currentStep)) {
        document.getElementById("step" + currentStep).style.display = "none";
        let nextStepElement = document.getElementById(
            "step" + (currentStep + 1),
        );
        nextStepElement.style.display = "block";
        nextStepElement.style.animation = "none";
        nextStepElement.offsetHeight; // Déclenche un reflow
        nextStepElement.style.animation = "fadeInStep 0.5s ease-out";
        document
            .querySelectorAll(".step-indicator .step")
            [currentStep].classList.add("active");
        updateNextButton(currentStep + 1);
    }
}

function prevStep(currentStep) {
    document.getElementById("step" + currentStep).style.display = "none";
    let prevStepElement = document.getElementById("step" + (currentStep - 1));
    prevStepElement.style.display = "block";
    prevStepElement.style.animation = "none";
    prevStepElement.offsetHeight; // Déclenche un reflow
    prevStepElement.style.animation = "fadeInStep 0.5s ease-out";
    document
        .querySelectorAll(".step-indicator .step")
        [currentStep - 1].classList.remove("active");
    updateNextButton(currentStep - 1);
}

// Ajouter des écouteurs d'événements pour chaque étape
for (let i = 1; i <= 3; i++) {
    const stepElement = document.getElementById("step" + i);
    const inputs = stepElement.querySelectorAll("input, select");
    inputs.forEach((input) => {
        input.addEventListener("input", () => updateNextButton(i));
    });
}

// Initialiser l'état du bouton "Suivant" pour la première étape
updateNextButton(1);
