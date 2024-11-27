// Show Modal
function showModal() {
    const modal = document.getElementById("modal");
    modal.style.display = "block";
}

// Hide Modal
function hideModal() {
    const modal = document.getElementById("modal");
    modal.style.display = "none";
}

// Close modal when clicking outside the modal content
window.onclick = function(event) {
    const modal = document.getElementById("modal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
};
