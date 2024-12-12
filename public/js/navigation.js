// js/navigation.js
document.addEventListener("DOMContentLoaded", () => {
    const navItems = document.querySelectorAll(".nav-item");
    const indicator = document.querySelector(".indicator");

    navItems.forEach((item) => {
        item.addEventListener("click", () => {
            const page = item.getAttribute("data-page");
            loadPage(page);
            updateIndicator(item);
        });
    });

    function loadPage(page) {
        // Ici, vous pouvez utiliser fetch ou AJAX pour charger le contenu dynamiquement
        // Exemple simple :
        fetch(`/path/to/${page}`)
            .then((response) => response.text())
            .then((html) => {
                document.querySelector(".content").innerHTML = html;
            })
            .catch((error) =>
                console.error("Erreur lors du chargement:", error),
            );
    }

    function updateIndicator(item) {
        const itemRect = item.getBoundingClientRect();
        indicator.style.width = `${itemRect.width}px`;
        indicator.style.left = `${itemRect.left - item.parentElement.getBoundingClientRect().left}px`;
    }
});
